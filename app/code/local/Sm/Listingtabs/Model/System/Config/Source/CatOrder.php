<?php

/*------------------------------------------------------------------------
 # SM Listing Tabs - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Listingtabs_Model_System_Config_Source_CatOrder
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'name', 'label' => Mage::helper('listingtabs')->__('Name')),
            array('value' => 'position', 'label' => Mage::helper('listingtabs')->__('Position')),
            array('value' => 'random', 'label' => Mage::helper('listingtabs')->__('Random')),
        );
    }
}
