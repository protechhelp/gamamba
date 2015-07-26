<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_ListNumCol extends Varien_Object
{
    const ONE			= 1;
    const TWO			= 2;
    const THREE			= 3;
    const FOUR			= 4;
    const FIVE			= 5;
    const SIX			= 6;		
    static public function getOptionArray()
    {
        return array(	
			self::ONE 		=> Mage::helper('megamenu')->__('1 column'),
			self::TWO		=> Mage::helper('megamenu')->__('2 columns'),
			self::THREE		=> Mage::helper('megamenu')->__('3 columns'),
			self::FOUR		=> Mage::helper('megamenu')->__('4 columns'),
			self::FIVE		=> Mage::helper('megamenu')->__('5 columns'),
			self::SIX		=> Mage::helper('megamenu')->__('6 columns'),		
        );
    }	
    static public function toOptionArray()
    {
        return array(	
			array(
			  'value'     => self::ONE,
			  'label'     => Mage::helper('megamenu')->__('1 column'),
			),

			array(
			  'value'     => self::TWO,
			  'label'     => Mage::helper('megamenu')->__('2 columns'),
			),

			array(
			  'value'     => self::THREE,
			  'label'     => Mage::helper('megamenu')->__('3 columns'),
			),		
			array(
			  'value'     => self::FOUR,
			  'label'     => Mage::helper('megamenu')->__('4 columns'),
			),			
			array(
			  'value'     => self::FIVE,
			  'label'     => Mage::helper('megamenu')->__('5 columns'),
			),		
			array(
			  'value'     => self::SIX,
			  'label'     => Mage::helper('megamenu')->__('6 columns'),
			),	
        );
    }
}