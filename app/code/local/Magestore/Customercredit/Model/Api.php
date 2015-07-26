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
class Magestore_Customercredit_Model_Api extends Mage_Api_Model_Resource_Abstract
{
    private function getCustomerId($customer){
        $customerid=Mage::getModel('customer/customer')->load($customer);
        if($customerid->getData()) {
            return $customerid->getId();
        }
        else {
            $collection=Mage::getModel('customer/customer')->getCollection();
            $collection->addFieldToFilter('email',$customer);
            return $collection->getFirstItem()->getId();
        }
    }
    /*set transaction type
     *
     * return type_id
     */
    private function setType($type){
         $collection=Mage::getModel('customercredit/typetransaction')->getCollection();
         $collection->addFieldToFilter('transaction_name',$type);
         if(count($collection)){
              return $collection->getFirstItem()->getId();
         }
         else{
             $update_type=Mage::getModel('customercredit/typetransaction');
             $update_type->setTransactionName($type);
             try{
                $update_type->save();
                return $update_type->getId();
             }
             catch (exception  $e){
                 return null;
             }
         }
    }
    /*update credit balance*/
    public function updateBalance($customer,$value){
        $customer_id=$this->getCustomerId($customer);
        $customer=Mage::getModel('customer/customer')->load($customer_id);
        $customer->setCreditValue($customer->getCreditValue()+$value)->save();
    }
    public function getCreditBalance($customer){
         $customer_id=$this->getCustomerId($customer);
         $customer=Mage::getModel('customer/customer')->load($customer_id);
         return $customer->getCreditValue();
    }
    public function updateCredit($customer,$amount_credit,$type,$transaction_detail,$order_id){
        $transaction_type_id = $this->setType($type);
        /*update credit balance*/
        $customer_id=$this->getCustomerId($customer);
        //$customer_group_id=Mage::getModel('customer/customer')->load($customer_id)->getCustomerGroupId();
        Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id,$transaction_type_id,$transaction_detail,$order_id,$amount_credit);
        $this->updateBalance($customer_id,$amount_credit);
    }
    public function redeemCredit($customer,$creditcode){
        $customer_id = $this->getCustomerId($customer);
        $credit = Mage::getModel('customercredit/creditcode')->getCollection()
            ->addFieldToFilter('credit_code', $creditcode);
        if($credit->getSize()==0){
			return false;
        }
        elseif($credit->getFirstItem()->getStatus()!=1){
            return false;
        }
        else{
            Mage::getModel('customercredit/creditcode')
                ->changeCodeStatus($credit->getFirstItem()->getId(),
                    Magestore_Customercredit_Model_Status::STATUS_ACTIVED);
            $credit_amount = $credit->getFirstItem()->getAmountCredit();
            Mage::getModel('customercredit/transaction')->addTransactionHistory($customer_id,
                Magestore_Customercredit_Model_TransactionType::TYPE_REDEEM_CREDIT,
                "redeemcredit","",$credit_amount);
            Mage::getModel('customercredit/customercredit')->updateBalance($customer,$credit_amount);
			return true;
        }
    }
}