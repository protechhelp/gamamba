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
 * Customercredit Controller
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Adminhtml_CheckoutController extends Mage_Adminhtml_Controller_Action {

    public function customercreditPostAction() {
        $request = $this->getRequest();
        $result = array();
        if ($request->isPost()) {
            $creditvalue = $request->getParam('credit_value');
            $session = Mage::getSingleton('checkout/session');
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

            $totals = $quote->getTotals();
            $tax = 0;
            foreach ($totals as $total) {
                if ($total->getCode() == 'tax') {
                    foreach ($total->getFullInfo() as $t) {
                        if (isset($t['base_amount'])) {
                            $tax = $t['base_amount'];
                            break;
                        }
                    }
                }
            }
            $sub_total = $quote->getBaseSubtotal();
            $shippingAmount = $quote->getShippingAddress()->getBaseShippingAmount();
            $shippingTax = $quote->getShippingAddress()->getBaseShippingTaxAmount();
            $customer_id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
            $max_credit_value = Mage::getSingleton('customer/customer')->load($customer_id)->getCreditValue();
            if (Mage::helper('customercredit')->getSpendConfig("tax") == "1") {
                $sub_total += ($tax - $shippingTax);
            }
            if (Mage::helper('customercredit')->getSpendConfig("shipping") == "1") {
                $sub_total += $shippingAmount;
            }
            if (Mage::helper('customercredit')->getSpendConfig("shipping_tax") == "1") {
                $sub_total += $shippingTax;
            }
            if ($sub_total < $max_credit_value) {
                $max_credit_value = $sub_total;
            }
            if ($creditvalue <= $max_credit_value) {
                $session->setBaseCustomerCreditAmount($creditvalue);
                $session->setCustomerCreditAmount($creditvalue);
                $result['balance'] = Mage::getSingleton('customer/customer')->load($customer_id)->getCreditValue() - $creditvalue;
            } else {
                $session->setBaseCustomerCreditAmount($max_credit_value);
                $session->setCustomerCreditAmount($max_credit_value);
                $result['balance'] = Mage::getSingleton('customer/customer')->load($customer_id)->getCreditValue() - $max_credit_value;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        //  Mage::getSingleton('adminhtml/session_quote')->addError($this->__('Gift card "%s" is not avaliable.', $code));
    }

}
