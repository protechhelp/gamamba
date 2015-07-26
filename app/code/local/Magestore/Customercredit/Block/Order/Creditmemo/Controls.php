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
 * Customercredit Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */

/**
 * Refund to customer balance functionality block
 *
 */
class Magestore_Customercredit_Block_Order_Creditmemo_Controls extends Mage_Core_Block_Template {

    public function getGrandTotal() {
        $totalsBlock = Mage::getBlockSingleton('sales/order_creditmemo_totals');
        $creditmemo = $totalsBlock->getCreditmemo();
        return number_format($creditmemo->getGrandTotal(),2);
    }

    public function enableTemplate() {
        $order_id = Mage::app()->getRequest()->getParam('order_id');
        return Mage::helper('customercredit')->isBuyCreditProduct($order_id);
    }

    public function isAssignCredit() {
        $data = explode(",", Mage::helper('customercredit')->getGeneralConfig('assign_credit'));
        $order_id = Mage::app()->getRequest()->getParam('order_id');
        $order = Mage::getSingleton('sales/order');
        $order->load($order_id);
        $customer = Mage::getSingleton('customer/customer')->load($order->getCustomerId());
        foreach ($data as $group) {
            if ($customer->getGroupId() == $group) {
                return true;
            }
        }
        return false;
    }

}
