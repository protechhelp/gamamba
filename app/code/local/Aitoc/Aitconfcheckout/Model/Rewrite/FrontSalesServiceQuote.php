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
class Aitoc_Aitconfcheckout_Model_Rewrite_FrontSalesServiceQuote extends Mage_Sales_Model_Service_Quote
{
    
    /**
     * Submit nominal items
     *
     * @return array
     */
    public function submitNominalItems()
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
        {
            $this->_validate();
            $this->_submitRecurringPaymentProfiles();
            $this->_deleteNominalItems();
        }
    }

    /**
     * get rate
     * @param $address
     * @return  float
     */
    protected function _getRateForValidate($address)
    {
        if(!Mage::getStoreConfig('aitconfcheckout/shipping_method/active'))
        {
            return 0.0001;
        }
        $method= $address->getShippingMethod();
        return $address->getShippingRateByCode($method);
    }

    /**
     * get name shipping method
     * @param $address
     * @return  string
     */
    protected function _getMethodForValidate($address)
    {
        if(!Mage::getStoreConfig('aitconfcheckout/shipping_method/active'))
        {
            return 'n_a';
        }
        return $address->getShippingMethod();
    }

    /**
     * overwrite parent
     * @return  Aitoc_Aitconfcheckout_Model_Rewrite_FrontSalesServiceQuote
     */
    protected function _validate()
    {
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException(
                    Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                );
            }

            $rate = $this->_getRateForValidate($address);
            $method = $this->_getMethodForValidate($address);

            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException(Mage::helper('sales')->__('Please specify a shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException(
                Mage::helper('sales')->__('Please check billing address information. %s', implode(' ', $addressValidation))
            );
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException(Mage::helper('sales')->__('Please select a valid payment method.'));
        }

        $this->_processBillingAndShipping();

        return $this;
    }

    /**
     * process in validate
     */
    protected function _processBillingAndShipping()
    {
        // process billing
        $billing = $this->getQuote()->getBillingAddress();
        $data = $this->_processBilling($billing);

        $billDataAfterClear = $data;
        $billing->addData($data);

        // process shipping
        if (!$this->getQuote()->isVirtual())
        {
            $shipping = $this->getQuote()->getShippingAddress();
            $data = $this->_processShipping($shipping, $billDataAfterClear);

            $shipping->addData($data);
        }
    }

    /**
     * replace field in billing address
     * @param $billing
     * @return array
     */
    protected function _processBilling($billing)
    {
        $data = $billing->getData();
        return Mage::helper('aitconfcheckout')->replaceAddressData($data,'billing', '');
    }

    /**
     * replace field in shipping address
     * @param $shipping
     * @param array $shipping
     * @return array
     */
    protected function _processShipping($shipping, $billDataAfterClear)
    {
        $data = $shipping->getData();

        $substituteCode = Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE;

        if (Mage::getStoreConfig('aitconfcheckout/shipping/active')) // step is activated
        {

            $data = Mage::helper('aitconfcheckout')->replaceAddressData($data,'shipping', '');

        }
        else
        {
            foreach ($data as $key => $val)
            {
                if (is_array($val) && (1 == count($val)))
                {
                    $val = current($val);
                }

                try
                {
                    $val = (string) $val;
                }
                catch (Exception $e)
                {
                    continue;
                }

                if ($val == $substituteCode OR strpos($val, $substituteCode) !== false)
                {
                    $data[$key] = '';
                }
            }
            if (!$this->_isCopyToShipping())
            {
                $data['country_id'] = '';
                $data['postcode']   = '';
            }
            else
            {
                foreach(Mage::helper('aitconfcheckout')->getElementsAddressToReplace() as $k => $v)
                {
                    if(isset($billDataAfterClear[$v]))
                    {
                        $data[$v] = $billDataAfterClear[$v];
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @return bool
     */
    protected function _isCopyToShipping()
    {
        return !Mage::getStoreConfig('aitconfcheckout/shipping/active')&&Mage::getStoreConfig('aitconfcheckout/shipping/copytoshipping');
    }	
}