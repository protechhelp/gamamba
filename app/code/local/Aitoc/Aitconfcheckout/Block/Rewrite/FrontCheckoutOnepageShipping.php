<?php
/**
 * Configurable Checkout
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitconfcheckout
 * @version      2.1.27
 * @license:     2qI6oAD8hvvkjMutXwa7Vp1gcsMrnxn0DqZRLmfWz9
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/* AITOC static rewrite inserts start */
/* $meta=%default,AdjustWare_Giftreg,Aitoc_Aitcheckoutfields% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcheckoutfields')){
    class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageShipping_Aittmp extends Aitoc_Aitcheckoutfields_Block_Rewrite_FrontCheckoutOnepageShipping {} 
 }elseif(Mage::helper('core')->isModuleEnabled('AdjustWare_Giftreg')){
    class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageShipping_Aittmp extends AdjustWare_Giftreg_Block_Rewrite_FrontCheckoutOnepageShipping {} 
 }else{
    /* default extends start */
    class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageShipping_Aittmp extends Mage_Checkout_Block_Onepage_Shipping {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageShipping extends Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageShipping_Aittmp
{    
    protected $_configs = array();
        
    protected function _construct()
    {
        $this->_configs = Mage::helper('aitconfcheckout/onepage')->initConfigs('shipping');
        parent::_construct();
    }
    
    public function getDefaultCountryId()
    {
        return Mage::helper('aitconfcheckout')->getDefaultCountryId();
    }    
    
    public function checkFieldShow($key)
    {
        return Mage::helper('aitconfcheckout/onepage')->checkFieldShow($key, $this->_configs);
    }
    
    public function checkStepActive($sStepCode)
    {
        return Mage::helper('aitconfcheckout')->checkStepActive($this->getQuote(), $sStepCode);
    }

    public function getDisabledSectionHash()
    {
        return Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($this->getQuote());
    }   
    
	public function checkSkipShippingAllowed()
    {
        return Mage::helper('aitconfcheckout/onepage')->checkSkipShippingAllowed();
    }
	
    // override parent
    public function getAddressesHtmlSelect($type)
    {
	    return Mage::helper('aitconfcheckout/onepage')->getAddressesHtmlSelect(parent::getAddressesHtmlSelect($type));
    }    
       
    // override parent
    function getAddress() 
	{        
        return Mage::helper('aitconfcheckout/onepage')->getAddress(parent::getAddress());
    }    
}