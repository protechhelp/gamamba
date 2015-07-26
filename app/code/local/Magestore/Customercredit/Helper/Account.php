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
 * Customercredit Helper
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Helper_Account extends Mage_Core_Helper_Abstract {

    public function getNavigationLabel() {
        return $this->__('Store Credit');
    }

    public function getDashboardLabel() {
        return $this->__('Account Dashboard');
    }

    public function accountNotLogin() {
        return !$this->isLoggedIn();
    }

    public function isLoggedIn() {
        return Mage::getSingleton('customercredit/session')->isLoggedIn();
    }

    //check customer can use store credit or not
    public function customerGroupCheck() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customer_group = $customer->getGroupId();
        $group = Mage::getStoreConfig('customercredit/general/assign_credit');
        $group = explode(',', $group);
        if (in_array($customer_group, $group)) {
            return true;
        } else {
            return false;
        }
    }

}