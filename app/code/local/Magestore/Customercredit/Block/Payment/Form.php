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
class Magestore_Customercredit_Block_Payment_Form extends Mage_Payment_Block_Form
{
    protected function _construct(){
        parent::_construct();
        $this->setTemplate('customercredit/payment/form.phtml');
    }
    public function getUseCustomerCredit() {
        return Mage::getSingleton('checkout/session')->getUseCustomerCredit();
    }
    public function hasCustomerCreditItem()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach($quote->getAllItems() as $item){
            if($item->getProductType() == 'customercredit'){
                return true;
            }
        }
        return false;
    }
    public function getCustomerCredit()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCredit();
    }
    public function getCustomerCreditLabel()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCreditLabel();
    }
    public function getCurrentCreditAmount()
    {
        $base_amount = Mage::getSingleton('checkout/session')->getBaseCustomerCreditAmount();
        return Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($base_amount);
    }
    public function getCurrentCreditAmountLabel()
    {
        $base_amount = Mage::getSingleton('checkout/session')->getBaseCustomerCreditAmount();
        return Mage::getModel('customercredit/customercredit')
            ->getLabel(Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($base_amount));
    }
    public function getUpdateUrl()
    {
        return $this->getUrl('customercredit/checkout/setAmountPost');
    }
    
}
