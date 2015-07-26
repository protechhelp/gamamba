<?php
/*------------------------------------------------------------------------
 # SM Categories - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Categories_Model_System_Config_Source_AccMouse
{
	public function toOptionArray()
	{
		return array(
			array('value'	=> 	'click', 		    'label' => Mage::helper('categories')->__('Click')),
			array('value'	=> 	'mouseenter',		'label' => Mage::helper('categories')->__('Hover')),
		);
	}
}
