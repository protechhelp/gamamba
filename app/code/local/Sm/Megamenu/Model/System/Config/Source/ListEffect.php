<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_ListEffect
{
	const CSS		 	=	1;
	const ANIMATION	 	=	2;
	const TOGGLE 		=	3;

	static public function getOptionArray()
    {
        return array(	
			self::CSS 				=> Mage::helper('megamenu')->__('Css'),
			self::ANIMATION			=> Mage::helper('megamenu')->__('Animation'),
			self::TOGGLE			=> Mage::helper('megamenu')->__('toggle'),
        );
    }	
    static public function toOptionArray()
    {
        return array(	
					array(
					  'value'     => self::CSS,
					  'label'     => Mage::helper('megamenu')->__('Css'),
					),		
					array(
					  'value'     => self::ANIMATION,
					  'label'     => Mage::helper('megamenu')->__('Animation'),
					),
					array(
					  'value'     => self::TOGGLE,
					  'label'     => Mage::helper('megamenu')->__('Toggle'),
					),			
		);
    }	
}
