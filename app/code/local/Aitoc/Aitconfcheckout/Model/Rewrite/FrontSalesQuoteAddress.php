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
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitconfcheckout_Model_Rewrite_FrontSalesQuoteAddress extends Mage_Sales_Model_Quote_Address
{
    protected $_errors = array();
    protected $_allowed = null;
   /**
     * Validate address attribute values
     *
     * @return bool
     */
    private function _getAllowedFields()
    {        
        
        $allowed = Mage::helper('aitconfcheckout')->getAllowedFieldHash($this->getAddressType());
        if(!Mage::getStoreConfig('aitconfcheckout/shipping/active') && $this->getAddressType() == 'shipping') {
            foreach($allowed as $k=>$v)
            {
                $allowed[$k] = 0;
            }
            $allowed['firstname'] = 0;
            $allowed['secondname'] = 0;
        }
        return $allowed;
    }

    /**
     * Validate $var is not empty
     *
     * @param string $var
     * @param string $name
     * @param string $errorMessage
     * @return  array
     */
    protected function _validateNotEmpty($var, $name, $errorMessage)
    {
        if (isset($this->_allowed[$name]) && $this->_allowed[$name] && !Zend_Validate::is($var, 'NotEmpty')) {
            $this->_errors[] = Mage::helper('customer')->__($errorMessage);
        }
        return $this->_errors;
    }

    /**
     * overwrite parent
     *
     * @return  array|bool
     */
    public function validate()
    {
        $this->_errors = array();

        $this->_allowed = $this->_getAllowedFields();
        $this->implodeStreetAddress();

        $this->_validateNotEmpty($this->getFirstname(), 'firstname', 'Please enter the first name.');
        $this->_validateNotEmpty($this->getLastname(), 'secondname', 'Please enter the last name.');
        $this->_validateNotEmpty($this->getStreet(1), 'address', 'Please enter the street.');
        $this->_validateNotEmpty($this->getCity(), 'city', 'Please enter the city.');
        $this->_validateNotEmpty($this->getTelephone(), 'telephone', 'Please enter the telephone number.');

        $_havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
        if ($this->_allowed['postcode'] && !in_array($this->getCountryId(), $_havingOptionalZip)
            && !Zend_Validate::is($this->getPostcode(), 'NotEmpty')
        ) {
            $this->_errors[] = Mage::helper('customer')->__('Please enter the zip/postal code.');
        }

        $this->_validateNotEmpty($this->getCountryId(), 'country', 'Please enter the country.');

        if ($this->_allowed['region'] && $this->getCountryModel()->getRegionCollection()->getSize()
               && !Zend_Validate::is($this->getRegionId(), 'NotEmpty')
               && Mage::helper('directory')->isRegionRequired($this->getCountryId())
        ) {
            $this->_errors[] = Mage::helper('customer')->__('Please enter the state/province.');
        }

        if (empty($this->_errors) || $this->getShouldIgnoreValidation()) {
            return true;
        }
        return $this->_errors;
    }
}