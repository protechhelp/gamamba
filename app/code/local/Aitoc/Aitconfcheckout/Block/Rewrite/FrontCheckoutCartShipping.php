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
class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutCartShipping extends Mage_Checkout_Block_Cart_Shipping
{
    /**
     * Get address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return Mage::helper('aitconfcheckout/onepage')->getAddress(parent::getAddress());
    }    
}