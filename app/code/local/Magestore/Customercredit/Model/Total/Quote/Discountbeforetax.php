<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customercredit Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Total_Quote_Discountbeforetax extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function __construct() {
        $this->setCode('customercredit_before_tax');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address) {
        parent::collect($address);
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('tax/calculation/apply_after_discount', $quote->getStoreId()) == '0') {
            return $this;
        }
        $items = $quote->getAllItems();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }

        $can_use_customer_credit = true;
        foreach ($items as $item) {
            if ($item->getData('product_type') == 'customercredit') {
                $can_use_customer_credit = false;
                break;
            }
        }
        if (!$address->getBaseSubtotal()) {
            $address = $quote->getBillingAddress();
        }
        $session = Mage::getSingleton('checkout/session');
        if ($can_use_customer_credit) {
            $helper = Mage::helper('customercredit');
            $base_max_credit_discount = $address->getBaseSubtotal();
            if (!$helper->getSpendConfig('tax')) {
                $base_max_credit_discount -= ($address->getBaseTaxAmount() - $address->getBaseShippingTaxAmount());
            }

            if (!$helper->getSpendConfig('shipping')) {
                $base_max_credit_discount -= $address->getBaseShippingAmount();
            }


            if (!$helper->getSpendConfig('shipping_tax')) {
                $base_max_credit_discount -= $address->getBaseShippingTaxAmount();
            }
            $session->setHasCustomerCreditItem(false);
            $base_customer_credit_amount = $session->getBaseCustomerCreditAmount();
            $base_customer_credit_discount = ($base_customer_credit_amount < $base_max_credit_discount) ? $base_customer_credit_amount : $base_max_credit_discount;
            $customer_credit_discount = Mage::getModel('customercredit/customercredit')
                    ->getConvertedFromBaseCustomerCredit($base_customer_credit_discount);
            $session->setBaseCustomerCreditAmount($base_customer_credit_discount);
            $address->setGrandTotal($address->getGrandTotal() - $customer_credit_discount);
            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $base_customer_credit_discount);

            $address->setCustomercreditDiscount($customer_credit_discount);
            $address->setBaseCustomercreditDiscount($base_customer_credit_discount);
        }
//        else{
//            $session->setHasCustomerCreditItem(true);
//        }
        $this->_prepareDiscountCreditForAmount($address, $base_customer_credit_discount);
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('tax/calculation/apply_after_discount', $quote->getStoreId())) {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getData('address_type') == 'billing')
            return $this;
        $session = Mage::getSingleton('checkout/session');
        $customer_credit_discount = $address->getCustomercreditDiscount();
        if ($session->getBaseCustomerCreditAmount())
            $customer_credit_discount = $session->getBaseCustomerCreditAmount();
        //  zend_debug::Dump($session->getBaseCustomerCreditAmount());die();
        if (!$session->getHasCustomerCreditItem()) {
            if ($customer_credit_discount > 0) {
                $address->addTotal(array(
                    'code' => $this->getCode(),
                    'title' => Mage::helper('customercredit')->__('Customer Credit'),
                    'value' => -$customer_credit_discount,
                ));
            }
        }

        return $this;
    }

    public function _prepareDiscountCreditForAmount(Mage_Sales_Model_Quote_Address $address, $baseDiscount) {
        $items = $address->getAllItems();
        $store = $address->getQuote()->getStoreId();
        if (!count($items))
            return $this;

        // Calculate total item prices
        $baseItemsPrice = 0;
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemsPrice += $item->getQty() * ($child->getQty() * $child->getBasePrice() - $child->getBaseDiscountAmount());
                }
            } elseif ($item->getProduct()) {
                $baseItemsPrice += $item->getQty() * $item->getBasePrice() - $item->getBaseDiscountAmount() - $item->getBaseCustomercreditDiscount();
            }
        }


        if ($baseItemsPrice < 0.0001)
            return $this;

        // Update discount for each item
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {

                    $baseItemPrice = $item->getQty() * ($child->getQty() * $child->getBasePrice() - $child->getBaseDiscountAmount());
                    $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $child->setBaseCustomercreditDiscount($child->getBaseCustomercreditDiscount())
                            ->setCustomercreditDiscount($child->getCustomercreditDiscount());

                    $child->setBaseTaxableAmount($child->getBaseTaxableAmount() - $child->getBaseCustomercreditDiscount());
                    $child->setTaxableAmount($child->getTaxableAmount() - $child->getCustomercreditDiscount());
                    if ($child->getBaseTaxableAmount() < 0) {
                        $child->setBaseTaxableAmount(0);
                        $child->setTaxableAmount(0);
                    }
                }
            } elseif ($item->getProduct()) {

                $baseItemPrice = $item->getQty() * $item->getBasePrice() - $item->getBaseDiscountAmount() - $item->getBaseCustomercreditDiscount();
                $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);

                $item->setBaseCustomercreditDiscount($itemBaseDiscount)
                        ->setCustomercreditDiscount($itemDiscount);

                $item->setBaseTaxableAmount($item->getBaseTaxableAmount() - $item->getBaseCustomercreditDiscount());
                $item->setTaxableAmount($item->getTaxableAmount() - $item->getCustomercreditDiscount());
                if ($item->getBaseTaxableAmount() < 0) {
                    $item->setBaseTaxableAmount(0);
                    $item->setTaxableAmount(0);
                }
            }
        }

        return $this;
    }

}
