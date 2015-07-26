<?php
/*------------------------------------------------------------------------
 # SM Basic Products - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_BasicProducts_Model_System_Config_Source_TrueFalse
{
	public function toOptionArray()
	{
		return array(
			array('value' => true,  'label' => Mage::helper('basicproducts')->__('True')),
			array('value' => false, 'label' => Mage::helper('basicproducts')->__('False'))
		);
	}
}
