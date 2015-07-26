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
 * Customercredit Observer Model
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Observer {
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Customercredit_Model_Observer
     */

    const XML_PATH_DISABLE_GUEST_CHECKOUT = 'catalog/downloadable/disable_guest_checkout';

    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function customercreditPaymentMethod($observer) {
        $block = $observer['block'];
        if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods) {
            $requestPath = $block->getRequest()->getRequestedRouteName()
                    . '_' . $block->getRequest()->getRequestedControllerName()
                    . '_' . $block->getRequest()->getRequestedActionName();
            $transport = $observer['transport'];
            $html_addcredit = $block->getLayout()->createBlock('customercredit/payment_form')->renderView();
            $html = $transport->getHtml();
            $html .= '<script type="text/javascript">checkOutLoadCustomerCredit(' . Mage::helper('core')->jsonEncode(array('html' => $html_addcredit)) . ');enableCheckbox();</script>';
            $transport->setHtml($html);
        }
    }

    public function customerSaveAfter($observer) {
        $customer = $observer->getEvent()->getCustomer();
        if (!$customer->getId())
            return $this;
        $credit_value = Mage::app()->getRequest()->getPost('credit_value');
        if (strpos($credit_value, ',')) {
            $credit_value = str_replace(',', '.', $credit_value);
        }
        $description = Mage::app()->getRequest()->getPost('description');
        $group = Mage::app()->getRequest()->getPost('account');
        $customer_group = $group['group_id'];
        $sign = substr($credit_value, 0, 1);
        if (!$credit_value)
            return $this;
        $credithistory = Mage::getModel('customercredit/transaction')->setCustomerId($customer->getId());
        $customers = Mage::getModel('customer/customer')->load($customer->getId());
        if ($sign == "-") {
            $end_credit = $customers->getCreditValue() - substr($credit_value, 1, strlen($credit_value));
            if ($end_credit < 0) {
                $end_credit = 0;
                $credit_value = -$customers->getCreditValue();
            }
        } else {
            $credithistory->setData('received_credit', $credit_value);
            $end_credit = $customers->getCreditValue() + $credit_value;
        }
        $customers->setCreditValue($end_credit);

        $credithistory->setData('type_transaction_id', 1)
                ->setData('detail_transaction', $description)
                ->setData('amount_credit', $credit_value)
                ->setData('end_balance', $customers->getCreditValue())
                ->setData('transaction_time', now())
                ->setData('customer_group_ids', $customer_group);
        try {
            $customers->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customercredit')->__($e->getMessage()));
        }
        try {
            $credithistory->save();
        } catch (Mage_Core_Exception $e) {

            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customercredit')->__($e->getMessage()));
        }
        return $this;
    }

    public function orderPlaceAfter($observer) {
        $order = $observer['order'];
        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
        if (!$customer_id) {
            $customer_id = $order->getCustomer()->getId();
        }
        $session = Mage::getSingleton('checkout/session');
        $amount = $session->getBaseCustomerCreditAmount();
        if ($amount && !$session->getHasCustomerCreditItem()) {
            Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id, Magestore_Customercredit_Model_TransactionType::TYPE_CHECK_OUT_BY_CREDIT, Mage::helper('customercredit')->__('check out by credit for order #') . $order->getIncrementId(), $order->getId(), -$amount);
            Mage::getModel('customercredit/customercredit')->changeCustomerCredit(-$amount, $customer_id);
        }
        if ($session->getUseCustomerCredit()) {
            $session->setBaseCustomerCreditAmount(null)
                    ->setUseCustomerCredit(false)
                    ->setHasCustomerCreditItem(false);
        } else {
            $session->setBaseCustomerCreditAmount(null)
                    ->setHasCustomerCreditItem(false);
        }
    }

    public function orderCancelAfter($observer) {
        $order = $observer->getOrder();
        $customer_id = $order->getCustomerId();
        $order_id = $order->getEntityId();
        $installer = Mage::getModel('core/resource_setup');
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');

        if ((float) (string) $order->getBaseCustomercreditDiscount() > 0) {
            $amount_credit = (float) (string) $order->getBaseCustomercreditDiscount();
            $query = 'SELECT SUM(  `customercredit_discount` ) as `total_customercredit_invoiced` , 
                        SUM(  `base_customercredit_discount` ) as `total_base_customercredit_invoiced`
                        FROM  `' . $installer->getTable('sales/invoice') . '` 
                        WHERE  `order_id` = ' . $order_id;
            $data = $read->fetchAll($query);
//            $total_customercredit_invoiced = $data['total_customercredit_invoiced'];
            $total_base_customercredit_invoiced = (float) $data[0]['total_base_customercredit_invoiced'];
            $amount_credit -= $total_base_customercredit_invoiced;
            $type_id = Magestore_Customercredit_Model_TransactionType::TYPE_CANCEL_ORDER;
            $transaction_detail = "Cancel order #" . $order->getIncrementId();
            Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id, $type_id, $transaction_detail, $order_id, $amount_credit);
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            $creditbefore = $customer->getCreditValue() + $amount_credit;
            $customer->setCreditValue($creditbefore);
            $customer->save();
            return true;
        }
    }

    public function orderSaveAfter($observer) {
        
    }

    public function invoiceSaveAfter($observer) {
        //Declare variables - Marko
        $invoice = $observer->getEvent()->getInvoice();
        $orderId = $invoice->getOrderId();
        $order = Mage::getSingleton('sales/order')->load($orderId);
        $customer_id = $order->getCustomerId();
        $product_credit_value = 0;

        //check if invoice store credit product - Marko
        foreach ($invoice->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $productId = $item->getProductId();
                $product = Mage::getModel('catalog/product')->load($productId);
                $type = $product->getTypeId();
                $credit_rate = $product->getCreditRate();
                if ($type == 'customercredit') {
                    //total credit value invoice - Marko
                    $product_credit_value += ((float) $item->getPrice()) * ((float) $item->getQty()) * $credit_rate;
                }
            }
        }

        //create transaction and add credit value to customer if invoice store credit product - Marko
        if ($product_credit_value > 0) {
            Mage::getModel('customercredit/transaction')
                    ->addTransactionHistory($order->getCustomerId(), Magestore_Customercredit_Model_TransactionType::TYPE_BUY_CREDIT, "buy credit " . $product_credit_value . " from store ", $order->getId(), $product_credit_value);
            Mage::getModel('customercredit/customercredit')
                    ->addCreditToFriend($product_credit_value, $customer_id);
        }
    }

    public function creditmemoSaveAfter(Varien_Event_Observer $observer) {
        //declare variables
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $data = Mage::app()->getRequest()->getPost('creditmemo');
        $order_id = $creditmemo->getOrderId();
        $order = Mage::getSingleton('sales/order');
        $order->load($order_id);
        $grand_total = $creditmemo->getGrandTotal();
        $amount_credit = $creditmemo->getCustomercreditDiscount();
        $customer_id = $creditmemo->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $maxcredit = $grand_total;
        $product_credit_value = 0;

        //check store credit is enough to refund or not - Marko
        if (round((float) $data['refund_creditbalance_return'], 3) > round($maxcredit, 3)) {
            Mage::throwException(Mage::helper('customercredit')->__('Credit amount cannot exceed order amount.'));
        }
        //prepare transaction - Marko
        $transaction_detail = "Refund order #" . $order->getIncrementId();
        $type_id = Magestore_Customercredit_Model_TransactionType::TYPE_REFUND_ORDER_INTO_CREDIT;
        //check if refund store credit product - Marko
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $productId = $item->getProductId();
                $product = Mage::getModel('catalog/product')->load($productId);
                $credit_rate = $product->getCreditRate();
                $type = $product->getTypeId();
                if ($type == 'customercredit') {
                    //total credit value refund - Marko
                    $product_credit_value += ((float) $item->getPrice()) * ((float) $item->getQty()) * $credit_rate;
                }
            }
        }

        //refund store credit product - Marko
        if ($product_credit_value > 0) {
            $type_id = Magestore_Customercredit_Model_TransactionType::TYPE_REFUND_CREDIT_PRODUCT;
            $amount_credit -= $product_credit_value;
        }
        //check if refund to store credit - Marko
        if ($data['refund_creditbalance_return_enable'] && $data['refund_creditbalance_return'] > 0) {
            $transaction_detail = "Refund order #" . $order->getIncrementId() . "into customer credit";
            $amount_credit += $data['refund_creditbalance_return'];
        }
        
        if ($amount_credit) {
            Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id, $type_id, $transaction_detail, $order_id, $amount_credit);

            //set credit value to customer - Marko
            $credit_value = $customer->getCreditValue() + $amount_credit;
            if ($credit_value < 0) {
                $credit_value = 0;
            }
            $customer->setCreditValue($credit_value);
            try {
                $customer->save();
            } catch (Exception $e) {
                echo Mage::helper('customercredit')->__($e->getMessage());
            }
        }
    }

    public function adminhtmlCatalogProductSaveAfter($observer) {
        $action = $observer->getEvent()->getControllerAction();
        $back = $action->getRequest()->getParam('back');
        $session = Mage::getSingleton('customercredit/session');
        $creditproductsession = $session->getCreditProductCreate();

        if ($back || !$creditproductsession)
            return $this;
        $type = $action->getRequest()->getParam('type');
        if (!$type) {
            $id = $action->getRequest()->getParam('id');
            $type = Mage::getModel('catalog/product')->load($id)->getTypeId();
        }
        if (!$type)
            return $this;

        $reponse = Mage::app()->getResponse();
        $url = Mage::getModel('adminhtml/url')->getUrl("customercreditadmin/adminhtml_creditproduct/index");
        $reponse->setRedirect($url);
        $reponse->sendResponse();
        $session->unsetData('credit_product_create');
        return $this;
    }

    //event checkout_allow guest
    public function isAllowedGuestCheckout(Varien_Event_Observer $observer) {
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote Mage_Sales_Model_Quote */
        $store = $observer->getEvent()->getStore();
        $result = $observer->getEvent()->getResult();
        $session = Mage::getSingleton('checkout/session');
        $isContain = false;

        foreach ($quote->getAllItems() as $item) {
            if (($product = $item->getProduct()) &&
                    $product->getTypeId() == 'customercredit') {
                $isContain = true;
            }
        }
        $session->setHasCustomerCreditItem(true);

        if ($isContain && Mage::getStoreConfigFlag(self::XML_PATH_DISABLE_GUEST_CHECKOUT, $store)) {
            $result->setIsAllowed(false);
        }

        return $this;
    }

    public function paypal_prepare_line_items($observer) {

        $paypalCart = $observer->getEvent()->getPaypalCart();
        if ($paypalCart) {
            $salesEntity = $paypalCart->getSalesEntity();

            if ($salesEntity->getCustomercreditDiscount() > 0) {
                $paypalCart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, abs((float) $salesEntity->getCustomercreditDiscount()), Mage::helper('customercredit')->__('Customer Credit'));
            }
        }
    }

    /* TrungHa: save credit value */

    public function saveCreditValue($observer) {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'customercredit') {
            $creditamount = $product->getCreditAmount();
            $rate = $product->getCreditRate();
            $creditamount = Mage::helper('customercredit')->getCreditAmount($creditamount);
            if ($creditamount['type'] == 'range') {
                $creditvalue = $creditamount['from'] * $rate . '-' . $creditamount['to'] * $rate;
            } elseif ($creditamount['type'] == 'dropdown') {
                $creditvalue = array();
                for ($i = 0; $i < count($creditamount['options']); $i++) {
                    $creditvalue[$i] = $creditamount['options'][$i] * $rate;
                }
                $creditvalue = implode(',', $creditvalue);
            } elseif ($creditamount['type'] == 'static') {
                $creditvalue = $creditamount['value'] * $rate;
            }
            $product->setCreditValue($creditvalue);
            $product->getResource()->saveAttribute($product, 'credit_value');
        }
    }

    /* TrungHa: lock attribute credit_value */

    public function lockAttributes($observer) {
        $event = $observer->getEvent();
        $product = $event->getProduct();
        $product->lockAttribute('credit_value');
    }

}

