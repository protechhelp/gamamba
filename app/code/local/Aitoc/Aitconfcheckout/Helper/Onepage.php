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
class Aitoc_Aitconfcheckout_Helper_Onepage extends Mage_Core_Helper_Abstract
{
    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress($address)
    {
        if ($address AND $data = $address->getData())
        {
            foreach ($data as $key => $val)
            {
                if ($val == Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE)
                {
                    $data[$key] = '';
                }
            }
            $address->addData($data);
        }
        return $address;
    }

    public function getAddressesHtmlSelect($sHtml)
    {
        if ($sHtml)
        {
            for ($i=1;$i<=10; $i++)
            {
                $sHtml = str_replace(array(', , ', ' ,
                        </option>', ', </option>', ' , ', ',,'), array(', ', '</option>', '</option>', ', ', ','), $sHtml);
            }
        }
        return $sHtml;
    }

    public function checkSkipShippingAllowed()
    {
        $allowedBillingHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('billing');
        $allowedShipingHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');

        $requiredHash = array('address', 'city', 'region', 'country', 'postcode', 'telephone');

        foreach ($allowedShipingHash as $key => $fieldActive)
        {
            if ($fieldActive AND in_array($key, $requiredHash) AND !$allowedBillingHash[$key])
            {
                return false;
            }
        }
        return true;
    }

    public function checkFieldShow($key, $configs)
    {
        return !(!$key || !isset($configs[$key]) || !$configs[$key]);
    }

    public function initConfigs($type)
    {
        $allowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash($type);
        $configs = array();
        foreach ($allowedFieldHash as $key => $value)
        {
            $configs[$key] = $value;
        }
        return $configs;
    }

}