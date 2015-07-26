<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_Position extends Varien_Object
{
    const BEFORE	= 1;
    const AFTER		= 2;
	const FIRST		= 3;

    static public function getOptionArray()
    {
        return array(
            self::BEFORE    => Mage::helper('megamenu')->__('Before'),
            self::AFTER   	=> Mage::helper('megamenu')->__('After'),
			self::FIRST   	=> Mage::helper('megamenu')->__('First')
        );
    }
    static public function toOptionArray()
    {
        return array(
			array(
			  'value'     => self::BEFORE,
			  'label'     => Mage::helper('megamenu')->__('Before'),
			),		
			array(
			  'value'     => self::AFTER,
			  'label'     => Mage::helper('megamenu')->__('After'),
			),  
			array(
			  'value'     => self::FIRST,
			  'label'     => Mage::helper('megamenu')->__('First'),
			),   			
		);
    }	
}