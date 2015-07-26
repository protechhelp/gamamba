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
class Magestore_Customercredit_Block_Order_Create_Credit
 extends Mage_Core_Block_Template
{
 	public function getCustomerCredit(){
            
            $customer_id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
            $customer = Mage::getSingleton('customer/customer')->load($customer_id);
            $credit = $customer->getCreditValue();
            $session = Mage::getSingleton('checkout/session');
            if($session->getBaseCustomerCreditAmount())
                $credit-=$session->getBaseCustomerCreditAmount();
            return number_format($credit, 2, '.', ' ');
        }
        public function isAssignCredit(){
            $data =explode(",",Mage::helper('customercredit')->getGeneralConfig('assign_credit'));
            $customer_id=Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
            $customer = Mage::getSingleton('customer/customer')->load($customer_id);
            foreach($data as $group){
                if($customer->getGroupId()==$group){
                    return true;
                }
            }
            return false;
        }
        public function isCreditProduct(){
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
            $items =  Mage::getSingleton('sales/quote_item')->getCollection();
            $items->addFieldToFilter("quote_id",$quote->getId());
            foreach ($items->getData() as $item){
                
                if($item['product_type']=='customercredit'){
                    return true;
                }
            }
             return false;;
        }
}