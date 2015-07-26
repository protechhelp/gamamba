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
class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageProgress extends Mage_Checkout_Block_Onepage_Progress
{
    public function getBilling()
    {
    // start aitoc
        return Mage::helper('aitconfcheckout/onepage')->getAddress(parent::getBilling());
    // finish aitoc        
    // return $this->getQuote()->getBillingAddress();
    }

    public function getShipping()
    {
    // start aitoc
        return Mage::helper('aitconfcheckout/onepage')->getAddress(parent::getShipping());
    // finish aitoc        
    // return $this->getQuote()->getShippingAddress();
    }

    public function checkStepActive($sStepCode)
    {
        return Mage::helper('aitconfcheckout')->checkStepActive($this->getQuote(), $sStepCode);
    }

    public function getProcessAddressHtml($sHtml)
    {
        $sHtml = nl2br($sHtml);

        $sHtml = str_replace(array('<br/>','<br />'), array('<br>', '<br>'), $sHtml); 
        
        $aReplace = array
        (
'<br><br>',    
    
'<br>
<br>',        

', <br>', ',  <br>'        
        );       
        
        while (strpos($sHtml, $aReplace[0]) !== false OR strpos($sHtml, $aReplace[1]) !== false) 
        {
        	$sHtml = str_replace($aReplace, '<br>', $sHtml);
        }

        if (strpos($sHtml, '<br>') === 0)
        {
            $sHtml = substr($sHtml, 4);
        }
           
        return $sHtml;
    }      
    
}