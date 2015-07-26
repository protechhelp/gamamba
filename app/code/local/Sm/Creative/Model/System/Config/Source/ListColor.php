<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Model_System_Config_Source_ListColor
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'orange', 'label'=>Mage::helper('creative')->__('Orange')),
		array('value'=>'yellow', 'label'=>Mage::helper('creative')->__('Yellow')),
		array('value'=>'blue', 'label'=>Mage::helper('creative')->__('Blue')),
		array('value'=>'red', 'label'=>Mage::helper('creative')->__('Red'))
		);
	}
}
