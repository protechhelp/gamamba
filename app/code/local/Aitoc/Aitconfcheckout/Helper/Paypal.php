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
class Aitoc_Aitconfcheckout_Helper_Paypal extends Mage_Core_Helper_Abstract
{
    protected $configs = array();

    public function __construct()
    {
        foreach(array('billing','shipping') as $sType)
        {
            if(!isset($this->_configs[$sType]))
            {
                $this->_configs[$sType] = array();
            }

            $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash($sType);

            foreach ($aAllowedFieldHash as $sKey => $bValue)
            {
                $this->_configs[$sType][$sKey] = $bValue;
            }
        }

    }

    public function checkFieldShow($sType,$sKey)
    {
        if (!$sKey || !isset($this->_configs[$sType]) || !isset($this->_configs[$sType][$sKey]))
        {
            return false;
        }

        if ($this->_configs[$sType][$sKey])
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}