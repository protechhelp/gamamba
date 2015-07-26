<?php
/*------------------------------------------------------------------------
 # SM Categories - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Categories_Model_System_Config_Source_Theme
{
	public function toOptionArray()
	{
		return array(
			array('value'	=> 	'theme1', 		'label' => Mage::helper('categories')->__('Theme1')),
			array('value'	=> 	'theme2',		'label' => Mage::helper('categories')->__('Theme2')),
			array('value'	=> 	'theme3',		'label' => Mage::helper('categories')->__('Theme3')),
			array('value'	=> 	'theme4',		'label' => Mage::helper('categories')->__('Theme4'))
		);
	}
}
