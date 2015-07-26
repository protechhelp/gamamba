<?php

/*------------------------------------------------------------------------
 # SM Listing Tabs - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Listingtabs_Model_System_Config_Source_ListEffect
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'slideLeft', 'label' => Mage::helper('listingtabs')->__('Slide Left')),
            array('value' => 'slideRight', 'label' => Mage::helper('listingtabs')->__('Slide Right')),
            array('value' => 'zoomOut', 'label' => Mage::helper('listingtabs')->__('Zoom Out')),
            array('value' => 'zoomIn', 'label' => Mage::helper('listingtabs')->__('Zoom In')),
            array('value' => 'flip', 'label' => Mage::helper('listingtabs')->__('Flip')),
            array('value' => 'flipInX', 'label' => Mage::helper('listingtabs')->__('Fip in Vertical')),
            array('value' => 'starwars', 'label' => Mage::helper('listingtabs')->__('Star Wars')),
            array('value' => 'flipInY', 'label' => Mage::helper('listingtabs')->__('Flip in Horizontal')),
            array('value' => 'bounceIn', 'label' => Mage::helper('listingtabs')->__('Bounce In')),
            array('value' => 'fadeIn', 'label' => Mage::helper('listingtabs')->__('Fade In')),
            array('value' => 'pageTop', 'label' => Mage::helper('listingtabs')->__('Page Top')),
        );
    }
}
