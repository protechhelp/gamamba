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
class Aitoc_Aitconfcheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEFAULT_COUNTRY  = 'general/country/default';
    protected $_disabledHash = null;

    protected   $_arrayAddressAssociative = array(
//        'name'      => array('firstname', 'lastname'),
        'company'   => array('company'),
        'address'   => array('street'),
        'city'      => array('city'),
        'region'    => array('region_id', 'region'),
        'country'   => array('country_id'),
        'postcode'  => array('postcode'),
        'telephone' => array('telephone'),
        'fax'       => array('fax'),
    );

    /**
     * @return array
     */
    public function getArrayAddressAssociative()
    {
        return $this->_arrayAddressAssociative;
    }

    /**
     * @return array
     */
    public function getElementsAddressToReplace()
    {
        return array('company', 'street', 'city', 'region', 'region_id', 'country_id', 'postcode', 'telephone', 'fax', 'firstname', 'middlename', 'lastname', 'suffix', 'prefix');
    }

    /**
     * @param string $sStepKey
     * @return array
     */
    public function getStepData($sStepKey = '')
    {
        $aConfigData = array
        (
            'billing' => $this->getArrayAddressAssociative(),
            'shipping' => $this->getArrayAddressAssociative(),
        );


        if ($sStepKey AND isset($aConfigData[$sStepKey]))
        {
            return $aConfigData[$sStepKey];
        }
        else
        {
            return $aConfigData;
        }
    }

    /**
     * @return int
     */
    public function getDefaultCountryId()
    {
        return $this->getDefaultCountry()->getId();
    }

    /**
     * @param string $sStepKey
     * @return array
     */
    public function getAllowedFieldHash($sStepKey)
    {
        if (!$sStepKey) return false;
        
        $aAllowedFieldHash = array();
        
        $aStepData = $this->getStepData($sStepKey);
        
		/* {#AITOC_COMMENT_END#}
        $iStoreId = Mage::app()->getStore()->getId();
        $iSiteId  = Mage::app()->getWebsite()->getId();
        
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitconfcheckout')->getLicense()->getPerformer();
        $ruler     = $performer->getRuler();
        if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
        {
            foreach ($aStepData as $sKey => $aData)
            {
                $aAllowedFieldHash[$sKey] = true;
            }

            return $aAllowedFieldHash;
        }
        {#AITOC_COMMENT_START#} */

        foreach ($aStepData as $sKey => $aData)
        {
            $aAllowedFieldHash[$sKey] = Mage::getStoreConfig('aitconfcheckout/' . $sStepKey . '/' . $sKey);
            
            if ($sKey == 'country' AND !$aAllowedFieldHash[$sKey] AND $aAllowedFieldHash['region'])
            {
                $countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
                ->loadByStore();
        
                $aCountryOptions = $countryCollection->toOptionArray();        
                
                if (sizeof($aCountryOptions) == 2) // more then 1 country are active
                {
                    $this->_contryId = $aCountryOptions[1]['value'];
                }
                else 
                {
                    //start fix of bug with displaying country field when it is disabled
                    //$aAllowedFieldHash[$sKey] = true;
					//end fix of bug with displaying country field when it is disabled
                }
            }
        }

        return $aAllowedFieldHash;
    }

    /**
     *
     * @param int $store
     * @return Mage_Directory_Model_Country
     */
    public function getDefaultCountry($store = null)
    {
        return Mage::getModel('directory/country')->loadByCode(Mage::getStoreConfig(self::XML_PATH_DEFAULT_COUNTRY, $store));
    }

    /**
     * @param $quote
     * @param string $sStepCode
     * @return bool
     */
    public function checkStepActive($quote, $sStepCode)
    {
        $aDisabledSectionHash = $this->getDisabledSectionHash($quote);

        return ($aDisabledSectionHash AND in_array($sStepCode, $aDisabledSectionHash)) ? false : true;
    }

    public function getDisabledSectionHash($quote)
    {
        if(is_null($this->_disabledHash)) {
            $this->_disabledHash = Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($quote);
        }
        return $this->_disabledHash;
    }

    /**
     * @return bool
     */
	public function getShippingEnabled()
    {
        return (bool) Mage::getStoreConfig('aitconfcheckout/shipping/active');
    }

    /**
     * @return bool
     */
    public function getShippingMethodEnabled()
    {
        return (bool) Mage::getStoreConfig('aitconfcheckout/shipping_method/active');
    }

    /**
     * @return bool
     */
    public function getPaymentEnabled()
    {
        return (bool) Mage::getStoreConfig('aitconfcheckout/payment/active');
    }

    /**
     * @param array $data
     * @param string $addressType
     * @param string $replaseTo
     * @return array
     */
    public function replaceAddressData($data, $addressType = 'billing', $replaseTo = Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE)
    {
        $localConfig = $this->getStepData($addressType);
        $allowedFieldHash = $this->getAllowedFieldHash($addressType);

        foreach ($localConfig as $key => $value)
        {
            foreach ($value as $field)
            {
                if (!isset($allowedFieldHash[$key]) OR !$allowedFieldHash[$key] OR (!empty($data[$field]) && $data[$field] == Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE))
                {
                    if (!($key == 'country' AND  !empty($data[$field])))
                    {
                        $data[$field] = $replaseTo;
                    }

                    if($key == 'country_id')
                    {
                        $data[$field] = Mage::getStoreConfig('general/country/default');
                    }
                }
            }
        }
        return $data;
    }
}

?>