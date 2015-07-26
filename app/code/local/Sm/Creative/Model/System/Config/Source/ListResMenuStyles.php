<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Model_System_Config_Source_ListResMenuStyles
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'1', 'label'=>Mage::helper('creative')->__('Selectbox')),
			array('value'=>'2', 'label'=>Mage::helper('creative')->__('Collapse')),
			array('value'=>'3', 'label'=>Mage::helper('creative')->__('Sidebar'))
		);
	}
}
