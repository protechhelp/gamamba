<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_LinkTargets
{
	const _BLANK		= 1;
    const _POPUP		= 2;
    const _SELF			= 3;
	public function toOptionArray()
	{
		return array(
			array('value'=> self::_SELF,	'label'=>Mage::helper('megamenu')->__('Same Window')),
        	array('value'=> self::_BLANK,	'label'=>Mage::helper('megamenu')->__('New Window')),
			array('value'=> self::_POPUP, 	'label'=>Mage::helper('megamenu')->__('Popup Window')),
		);
	}
	public function getOptionArray(){
		return array(
			self::_SELF		=>	Mage::helper('megamenu')->__('Same Window'),
			self::_BLANK	=>	Mage::helper('megamenu')->__('New Window'),
			self::_POPUP	=>	Mage::helper('megamenu')->__('Popup Window'),
		);	
	}
}
