<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_Align extends Varien_Object
{
    const LEFT	= 1;
    const RIGHT	= 2;

    static public function getOptionArray()
    {
        return array(
            self::LEFT    => Mage::helper('megamenu')->__('Left'),
            self::RIGHT   => Mage::helper('megamenu')->__('Right')
        );
    }
    static public function toOptionArray()
    {
        return array(
			array(
			  'value'     => self::LEFT,
			  'label'     => Mage::helper('megamenu')->__('Left'),
			),		
			array(
			  'value'     => self::RIGHT,
			  'label'     => Mage::helper('megamenu')->__('Right'),
			),        
		);
    }	
}