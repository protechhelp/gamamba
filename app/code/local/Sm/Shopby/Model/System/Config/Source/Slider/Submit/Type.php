<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_System_Config_Source_Slider_Submit_Type
{

    const SUBMIT_AUTO_DELAYED = 1;
    const SUBMIT_BUTTON = 2;
    protected $_options;

    public function toOptionArray(){
        if (null === $this->_options) {
            $helper = Mage::helper('sm_shopby');
            $this->_options = array(
                self::SUBMIT_AUTO_DELAYED => $helper->__('Delayed auto submit'),
                self::SUBMIT_BUTTON => $helper->__('Submit button')
            );
        }

        return $this->_options;
    }

}