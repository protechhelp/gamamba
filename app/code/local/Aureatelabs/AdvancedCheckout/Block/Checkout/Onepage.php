<?php
class Aureatelabs_AdvancedCheckout_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
    public function getSteps()
    {
        $steps = array();
        $stepCodes = $this->_getStepCodes();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if ($this->isCustomerLoggedIn()) {
            $stepCodes = array_diff($stepCodes, array('login'));
        }
        if(!$this->isCustomerLoggedIn() && $customerId){
            $stepCodes = array_diff($stepCodes, array('login','billing','shipping','shipping_method','review'));
        }
        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }

        return $steps;
    }

    public function getActiveStep()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if(!$this->isCustomerLoggedIn() && $customerId){
            return 'payment';
        }else{
            return $this->isCustomerLoggedIn() ? 'billing' : 'login';
        }
    }
}