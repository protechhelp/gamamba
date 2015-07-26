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
class Magestore_Customercredit_CheckoutController extends Mage_Core_Controller_Front_Action {

    /**
     * change use customer credit to spend
     */
    public function setAmountPostAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $session = Mage::getSingleton('checkout/session');
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $customer_id = $customerData->getId();
            $subtotal = (float) (string) Mage::getSingleton('checkout/session')->getQuote()->getBaseSubtotal();
            $tax = (float) (string) Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('base_tax_amount');
            $shipping = (float) (string) Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('base_shipping_amount');
            $shipping_tax = (float) (string) Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('base_shipping_tax_amount');
            $maxcredit = Mage::helper('customercredit')->getMaxCreditCanUse($customer_id, $subtotal, $tax, $shipping, $shipping_tax);

            if ($session->getData('onestepcheckout_giftwrap')) {
                $maxcredit += $session->getData('onestepcheckout_giftwrap_amount');
            }

            if (is_numeric($request->getParam('customer_credit')) && Mage::helper('customercredit')->getGeneralConfig('enable')) {
                $credit_amount = $request->getParam('customer_credit');
                $base_credit_amount = Mage::getModel('customercredit/customercredit')
                        ->getConvertedToBaseCustomerCredit($credit_amount);
                $base_customer_credit = Mage::getModel('customercredit/customercredit')->getBaseCustomerCredit();

                $base_credit_amount = ($base_credit_amount > $base_customer_credit) ? $base_customer_credit : $base_credit_amount;


                if ($base_credit_amount > $maxcredit) {
                    $session->setBaseCustomerCreditAmount($maxcredit);
                } else {
                    $session->setBaseCustomerCreditAmount($base_credit_amount);
                }
                $session->setUseCustomerCredit(true);


                $this->_redirect('checkout/cart');
            }


            if (is_numeric($request->getParam('credit_amount'))) {
                $amount = $request->getParam('credit_amount');
                $base_amount = Mage::getModel('customercredit/customercredit')
                        ->getConvertedToBaseCustomerCredit($amount);
                $base_customer_credit = Mage::getModel('customercredit/customercredit')->getBaseCustomerCredit();
                $base_credit_amount = ($base_amount > $base_customer_credit) ? $base_customer_credit : $base_amount;
                if ($base_credit_amount > $maxcredit) {
                    $session->setBaseCustomerCreditAmount($maxcredit);
                } else {
                    $session->setBaseCustomerCreditAmount($base_credit_amount);
                }
                $session->setUseCustomerCredit(true);
                $result = array();
                $result['success'] = 1;
                $result['amount'] = Mage::getModel('customercredit/customercredit')
                        ->getConvertedFromBaseCustomerCredit($session->getBaseCustomerCreditAmount());
                $result['price0'] = 0;
                if ($session->getBaseCustomerCreditAmount() == Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal())
                    $result['price0'] = 1;
                
                //Tich hop One step checkout - Marko
                $moduleOnestepActive = Mage::getConfig()->getModuleConfig('Magestore_Onestepcheckout')->is('active', 'true');
                if ($moduleOnestepActive && Mage::getStoreConfig('onestepcheckout/general/active') == '1') {
                    $result['isonestep'] = Mage::getUrl('onestepcheckout/index/save_shipping', array('_secure' => true));
                } else {
                    //update lai payment khi khong co one step
                    Mage::getSingleton('checkout/type_onepage')->getQuote()->collectTotals()->save();
                    $html = $this->_getPaymentMethodsHtml();
                    $result['payment_html'] = $html;
                    // {"goto_section":"payment", "update_section":{"name":"payment-method", "html":"\n    <dt>\n            <input id=\"p_method_cashondelivery\" value=\"cashondelivery\" type=\"radio\" name=\"payment[method]\" title=\"Cash On Delivery\" onclick=\"payment.switchMethod('cashondelivery')\" checked=\"checked\" class=\"radio\" \/>\n            <label for=\"p_method_cashondelivery\">Cash On Delivery <\/label>\n    <\/dt>\n        <dt>\n            <input id=\"p_method_free\" value=\"free\" type=\"radio\" name=\"payment[method]\" title=\"No Payment Information Required\" onclick=\"payment.switchMethod('free')\" class=\"radio\" \/>\n            <label for=\"p_method_free\">No Payment Information Required <\/label>\n    <\/dt>\n    <script type=\"text\/javascript\">\n    \/\/<![CDATA[\n        payment.init();\n        \/\/]]>\n<\/script>\n<script type=\"text\/javascript\">checkOutLoadCustomerCredit({\"html\":\"\\r\\n    <dl id=\\\"customercredit_container\\\">\\r\\n                                    <dt class=\\\"customercredit\\\">\\r\\n                <input type=\\\"checkbox\\\" name=\\\"payment[customercredit]\\\" id=\\\"customercreditcheck\\\" onclick=\\\"changeUseCustomercredit(this, 'http:\\\/\\\/localhost:88\\\/magento_standard_1901\\\/customercredit\\\/index\\\/checkCredit\\\/');\\\"  checked=\\\"checked\\\" \\\/>\\r\\n                <label for=\\\"customercredit\\\" style=\\\"font-weight: bold; color: #666;display:inline;float:none\\\">Use Customer Credit to check out  (<span class=\\\"price\\\">$20,928.42<\\\/span> available )<\\\/label>\\r\\n                <form action=\\\"\\\" method=\\\"post\\\" onsubmit=\\\"return false;\\\" id=\\\"customercredit-payment-form\\\">\\r\\n                    <div id=\\\"cc_checkout\\\"  >\\r\\n                        <div style=\\\"line-height: 15px;margin-top: 5px;margin-left: 10px\\\">\\r\\n                            <span style=\\\"float:left; green\\\"><strong style=\\\"margin-left: 7px;\\\"> You're using:<\\\/strong><\\\/span>\\r\\n                            <span id=\\\"checkout-cc-input\\\"><strong><span class=\\\"price\\\">$120.00<\\\/span><\\\/strong><\\\/span>\\r\\n                            <div style=\\\"float: left;\\\" class=\\\"checkout_cc_input_alert\\\">\\r\\n                                <input type=\\\"text\\\" id=\\\"checkout_cc_inputtext\\\" onchange=\\\"validateCheckOutCC(20928.423);\\\" class=\\\"input-text validate-number validate-zero-or-greater required-entry\\\" value=\\\"120\\\" style=\\\"display: none;margin: -2px 0px 0px 5px;\\\"\\\/>\\r\\n                                <div class=\\\"validation-advice\\\" id=\\\"advice-validate-number-checkout_cc_smaller\\\" style=\\\"display: none\\\">Please enter a number smaller than <span class=\\\"price\\\">$20,928.42<\\\/span>.<\\\/div>\\r\\n                            <\\\/div>\\r\\n                            <img id=\\\"customercredit_cc_success_img\\\" style=\\\"display: none;padding-left:5px\\\" src=\\\"http:\\\/\\\/localhost:88\\\/magento_standard_1901\\\/skin\\\/frontend\\\/base\\\/default\\\/images\\\/customercredit\\\/i_msg-success.gif\\\"\\\/>\\r\\n                            <p id=\\\"customercredit_cc_show_loading\\\" style=\\\"display: none;margin-left: 8px;float: left;\\\">\\r\\n                                <img src=\\\"http:\\\/\\\/localhost:88\\\/magento_standard_1901\\\/skin\\\/frontend\\\/base\\\/default\\\/images\\\/customercredit\\\/opc-ajax-loader.gif\\\"\\\/>\\r\\n                                Loading...                            <\\\/p>\\r\\n                            <button type=\\\"submit\\\" class=\\\"button\\\" id=\\\"checkout-cc-button\\\" style=\\\"display: none !important; margin-left: 10px;\\\" onclick=\\\"updateCustomerCredit('http:\\\/\\\/localhost:88\\\/magento_standard_1901\\\/customercredit\\\/checkout\\\/setAmountPost\\\/',20928.423);\\\">\\r\\n                                <span><span>Update Value<\\\/span><\\\/span>\\r\\n                            <\\\/button>\\r\\n                            <span id=\\\"checkout-cc-img\\\"><img style=\\\"cursor: pointer;height: 15px;width: 15px;margin-left: 3px;\\\" onclick=\\\"showEditText(this);\\\"src=\\\"http:\\\/\\\/localhost:88\\\/magento_standard_1901\\\/skin\\\/frontend\\\/base\\\/default\\\/images\\\/customercredit\\\/btn_edit.gif\\\"><\\\/span>\\r\\n\\r\\n                        <\\\/div>\\r\\n                <\\\/form>\\r\\n                        <script type=\\\"text\\\/javascript\\\">\\r\\n                var ccPaymentForm = new VarienForm('customercredit-payment-form', true);\\r\\n\\r\\n            <\\\/script>\\r\\n            <\\\/dt>\\r\\n            <\\\/dl>\\r\\n\\r\\n\"});enableCheckbox();<\/script>"}}
                }
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }

    protected function _getPaymentMethodsHtml() {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

}
