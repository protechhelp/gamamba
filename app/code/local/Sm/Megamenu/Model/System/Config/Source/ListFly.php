<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_ListFly extends Varien_Object
{
    const ONE			= 1;
    const TWO			= 2;		
    static public function getOptionArray()
    {
        return array(	
			self::ONE 		=> Mage::helper('megamenu')->__('Right'),
			self::TWO		=> Mage::helper('megamenu')->__('Left'),		
        );
    }	
    static public function toOptionArray()
    {
        return array(	
			array(
			  'value'     => self::ONE,
			  'label'     => Mage::helper('megamenu')->__('Right'),
			),

			array(
			  'value'     => self::TWO,
			  'label'     => Mage::helper('megamenu')->__('Left'),
			),

        );
    }
}