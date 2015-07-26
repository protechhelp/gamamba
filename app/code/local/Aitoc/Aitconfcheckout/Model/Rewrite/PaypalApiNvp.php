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
class Aitoc_Aitconfcheckout_Model_Rewrite_PaypalApiNvp extends Mage_Paypal_Model_Api_Nvp
{
    public function callDoExpressCheckoutPayment()
    {
        if(
            !Mage::getStoreConfig('aitconfcheckout/shipping/active') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/address') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/city') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/region') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/country') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/postcode') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/telephone')
          )
        {
            $this->setSuppressShipping(true);
        }

        parent::callDoExpressCheckoutPayment();
    }
}