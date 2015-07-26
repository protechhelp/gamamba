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
class Magestore_Customercredit_Block_Cart_Customercredit extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        $couponBlock = $this->getLayout()->getBlock('checkout.cart.coupon');
        $this->setData('coupon_html', $couponBlock->toHtml());
        $couponBlock->setTemplate('customercredit/cart/coupon.phtml');
        return parent::_prepareLayout();
    }
    public function isLoggedIn()
    {
        return Mage::helper('customercredit/account')->isLoggedIn();
    }
    public function isEnableCredit()
    {
        return Mage::helper('customercredit')->getGeneralConfig('enable');
    }
    public function hasCustomerCreditItem()
    {
        return Mage::getSingleton('checkout/session')->getHasCustomerCreditItem();
    }

    public function getCurrentCreditAmount()
    {
        $base_amount = Mage::getSingleton('checkout/session')->getBaseCustomerCreditAmount();
        return Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($base_amount);
    }
    public function getCustomerCredit()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCredit();
    }
    public function getCustomerCreditLabel()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCreditLabel();
    }
	
	public function shoppingCartHasCredit(){
        $hasCredit = false;
		$session = Mage::getSingleton('checkout/session');
        $cart = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        foreach($cart as $item){
            $typeProduct = $item->getProduct()->getTypeId();
            if($typeProduct == 'customercredit'){
				$session->setHasCustomerCreditItem(true);
                $hasCredit = true;
            }else{
				$session->setHasCustomerCreditItem(false);
			}
            
        }
        return $hasCredit;
        
    }
}