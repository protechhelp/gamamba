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
class Magestore_Customercredit_Model_Customercredit extends Mage_Core_Model_Abstract
{

    public function sendVerifyEmail($email,$value,$message,$keycode)
    {
        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
        $customerData = Mage::getModel('customer/customer')->load($customer_id);
        $send_name = $customerData->getFirstname()." ".$customerData->getLastname();
        $store = Mage::app()->getStore($this->getStoreId());
        $share_url = Mage::getSingleton('core/url')->getUrl('customercredit/index/validateCustomer');
        $veriryurl = Mage::getSingleton('core/url')->getUrl('customercredit/index/sharepost?keycode='.$keycode);
        $veriryurl = substr($veriryurl,0,strlen($veriryurl)-1);
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area'	=> 'frontend',
                'store'	=> $store->getStoreId()
            ));
            $mailTemplate->sendTransactional(
            Mage::helper('customercredit')->getEmailConfig('verify',$store->getStoreId()),
            Mage::helper('customercredit')->getEmailConfig('sender',$store->getStoreId()),
            $customerData->getEmail(),
            $customerData->getName(),
                array(
                'store'		=> $store,
                'recipient_email'     =>$email,
                'value'     =>$this->getLabel($value),
                'send_name' => $send_name,
                'message'   =>$message,
                'emailcode' =>$keycode,
                'verifyurl' =>$veriryurl,
                'share_url' =>$share_url,
            )
        );
        $translate->setTranslateInline(true);
        return $this;
    }

    public function changeCustomerCredit($credit_amount,$customer_id=null)
    {
        if($customer_id==null)
        	$customer = Mage::helper('customercredit')->getCustomer();
	else
		$customer = Mage::getModel('customer/customer')->load($customer_id);
        $customer->setCreditValue($customer->getCreditValue()+$credit_amount)->save();
    }

    public function addCreditToFriend($credit_amount,$customer_id)
    {	
		$friend_account = Mage::getModel('customer/customer')->load($customer_id);
		if(isset($friend_account)){
			$friend_credit_balance = $friend_account->getCreditValue() + $credit_amount;
			$friend_account->setCreditValue($friend_credit_balance);
			try{
				$friend_account->save();
			}
			catch(Exception $e){
				echo $e->getMessage();
			}
		}
    }

    public function sendCreditToFriendByEmail($credit_amount,$friend_email,$message)
    {
		$credit_code = Mage::getModel('customercredit/creditcode');
		$code = $credit_code->addCreditCode($friend_email, $credit_amount, Magestore_Customercredit_Model_Status::STATUS_UNUSED);
        $this->sendCodeToFriendEmail($friend_email,$credit_amount,$message,$code);
    }

	public function sendCreditToFriendByEmailAfterVerify($credit_code_id,$credit_amount,$friend_email,$message){
		$credit_code = Mage::getModel('customercredit/creditcode')->load($credit_code_id);
		if(isset($credit_code) && isset($friend_email) && isset($credit_amount)){
			 $this->sendCodeToFriendEmail($friend_email,$credit_amount,$message,$credit_code->getCreditCode());
		}
	}
    public function sendCodeToFriendEmail($email,$amount,$message,$creditcode)
    {
        $receiver_name = Mage::helper('customercredit')->getNameCustomerByEmail($email);
        $customerData = Mage::helper('customercredit')->getCustomer();
        $send_name = $customerData->getFirstname()." ".$customerData->getLastname();
        $store = Mage::app()->getStore($this->getStoreId());
        $login_url = Mage::getSingleton('core/url')->getUrl('customer/account/login');
        $redeem_page_url = Mage::getSingleton('core/url')->getUrl('customercredit/index/redeem');
        $redeemurl = Mage::getSingleton('core/url')->getUrl('customercredit/index/redeem?code='.$creditcode);
       // $redeemurl = substr($redeemurl,0,strlen($redeemurl)-1).$creditcode;
        $redeemurl = substr($redeemurl,0,strlen($redeemurl)-1);
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area'	=> 'frontend',
                'store'	=> $store->getStoreId()
            ));
        $mailTemplate->sendTransactional(
            Mage::helper('customercredit')->getEmailConfig('creditcode',$store->getStoreId()),
            Mage::helper('customercredit')->getEmailConfig('sender',$store->getStoreId()),
            $email,
            $send_name,
            array(
                'store'		=> $store,
                'send_name'     =>$send_name,
		'receiver_name'=>$receiver_name,
                'value'     =>$amount,
                'login_url'     =>$login_url,
                'redeem_page_url'     =>$redeem_page_url,
                'message'   =>$message,
                'creditcode' =>$creditcode,
                'redeemurl' =>$redeemurl,
            )
        );
        $translate->setTranslateInline(true);
        return $this;
    }
    public function getBaseLabel($amount)
    {
        $base_currency = Mage::app()->getStore()->getBaseCurrency();
        return $base_currency->format($amount);
    }
    public function getLabel($amount)
    {
        $current_currency = Mage::app()->getStore()->getCurrentCurrency();
        return $current_currency->format($amount);
    }
    public function getBaseCustomerCredit()
    {
        return Mage::helper('customercredit')->getCustomer()->getCreditValue();
    }

    public function getBaseCustomerCreditLabel()
    {
        $customer_credit = $this->getBaseCustomerCredit();
        return $this->getBaseLabel($customer_credit);
    }
    public function getCustomerCredit()
    {
        $store = Mage::app()->getStore();
        $customer_credit = $this->getBaseCustomerCredit();
        return round($store->convertPrice($customer_credit),3);
    }
    public function getCustomerCreditLabel()
    {
        return $this->getLabel($this->getCustomerCredit());
    }
    public function getConvertedFromBaseCustomerCredit($credit_amount)
    {
        $store = Mage::app()->getStore();
        return $store->convertPrice($credit_amount);
    }
    public function getConvertedToBaseCustomerCredit($credit_amount)
    {
        $rate = Mage::app()->getStore()->convertPrice(1);
        return $credit_amount/$rate;
    }
    public function createCreditProduct(){
        $product1 = new Mage_Catalog_Model_Product();
        $product1->setStoreId(0)
                ->setId(null)
                ->setAttributeSetId(9)
                ->setTypeId("customercredit")
                ->setName("Credit product 1")
                ->setSku("creditproduct")
                ->setStatus("1")
                ->setTaxClassId("0")
                ->setVisibility("4")
                ->setEnableGooglecheckout("1")
                ->setCreditAmount("100")
                ->setDescription("Credit product")
                ->setShortDescription("credit")
                ->save();
        $product2 = new Mage_Catalog_Model_Product();
        $product2->setStoreId(0)
                ->setId(null)
                ->setAttributeSetId(9)
                ->setTypeId("customercredit")
                ->setName("Credit product 2")
                ->setSku("creditproduct")
                ->setStatus("1")
                ->setTaxClassId("0")
                ->setVisibility("4")
                ->setEnableGooglecheckout("1")
                ->setCreditAmount("1-10000")
                ->setDescription("Credit product")
                ->setShortDescription("credit")
                ->save();
    }
}
