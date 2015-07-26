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
class Aitoc_Aitconfcheckout_Model_Aitconfcheckout extends Mage_Eav_Model_Entity_Attribute
{
    const SUBSTITUTE_CODE = '100100101010100011100101010010101101';
    const SUBSTITUTE_CODE_INT = 2147483647; /*region_id field*/

    protected $_shippingMethodName = '';

	/**
     * Get disabled steps
     * @param Mage_Sales_Model_Quote $quote checkout quote
     * @return array
     */
    public function getDisabledSectionHash($quote)
    {
        if (Mage::registry('bDisabledSectionRegistered'))
        {
            return Mage::registry('disabledSectionHash');
		}
        
        $originalCodes = array('shipping', 'shipping_method', 'payment');
        
        if ($quote->isVirtual())
        {
            $originalCodes = array('payment');
        }
        
        $disabledSectionHash = array();
        
        foreach ($originalCodes as $stepKey)
        {
            if (!Mage::getStoreConfig('aitconfcheckout/' . $stepKey . '/active'))
            {
			    $needForDisable = true;
                if ($stepKey == 'shipping_method')
                {
                    $needForDisable = $this->_getShippingMethods();
                }
                elseif ($stepKey == 'payment')
                {
                    $needForDisable = $this->_getPaymentMethods($quote);
                }
                
                if ($needForDisable)
                {
                    $disabledSectionHash[] = $stepKey;
                }
            }
        }

        /* {#AITOC_COMMENT_END#}
        $iStoreId = Mage::app()->getStore()->getId();
        $iSiteId  = Mage::app()->getWebsite()->getId();

        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitconfcheckout')->getLicense()->getPerformer();
        $ruler     = $performer->getRuler();
        if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
        {
            $disabledSectionHash = array();
        }
        {#AITOC_COMMENT_START#} */

        Mage::register('disabledSectionHash', $disabledSectionHash);
        Mage::register('bDisabledSectionRegistered', true);
        
        return $disabledSectionHash;
    }      

	/**
	 * Check if shipping method step is disabled
	 * @return boolean
	 */
    protected function _getShippingMethods()
    {
        $this->_shippingMethodName = '';
        if (!($this->_getShippingMethodIfConfig('flatrate') xor $this->_getShippingMethodIfConfig('freeshipping')) || Mage::getStoreConfig('carriers/tablerate/active')) // only one method is allowed
        {
            return false;
        }
        elseif (!Mage::registry('sShippingMethodStrict') and !empty($this->_shippingMethodName))
        {
            Mage::register('sShippingMethodStrict', $this->_shippingMethodName);
        }

        return true;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function _getShippingMethodIfConfig($name)
    {
        if (Mage::getStoreConfig('carriers/'.$name.'/active'))
        {
            if (!Mage::getStoreConfig('carriers/'.$name.'/sallowspecific'))
            {
                $this->_shippingMethodName = $name;
                //return $name;
            }
            return true;
        }
        return false;
    }

	/**
	 * Check if payment step is disabled
	 * @return boolean
	 */
    protected function _getPaymentMethods($quote)
    {
        $methodCount = 0;

        $store = $quote ? $quote->getStoreId() : null;
        $methods = Mage::helper('payment')->getStoreMethods($store, $quote);

        foreach ($methods as $key => $method)
        {
            if ($method->canUseCheckout())
            {
                $methodCount++;
            }
        }

        if ($methodCount != 1 OR $method->getCode() != 'checkmo') // only one method is allowed (check money)
        {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getSubstituteCode()
    {
        return self::SUBSTITUTE_CODE;
    }

    /**
     * @param string $currentStep
     * @param $quote
     * @return string
     */
    public function getSkipStepData($currentStep, $quote)
    {
        if (!$quote) 
		{
		    return false;
		}
        
        $originalCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'review');
        
        if ($quote->isVirtual())
        {
            $originalCodes = array('billing', 'payment', 'review');
        }

        $disabledSectionHash = $this->getDisabledSectionHash($quote);

        if (!$disabledSectionHash)
        {
		    return false;
		}
       
        $stepCodes = array_diff($originalCodes, $disabledSectionHash);

        $newCodes = array_values($stepCodes);

        $gotoStep = array_search($currentStep, $newCodes);
        
        if (false === $gotoStep || !isset($newCodes[$gotoStep]))
        {
		    return false;
		}
        $gotoStep++;

        return $newCodes[$gotoStep];
    }
}