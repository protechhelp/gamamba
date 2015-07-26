<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Model_System_Config_Source_ListBodyFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'', 'label'=>Mage::helper('creative')->__('No select')),
			array('value'=>'Arial', 'label'=>Mage::helper('creative')->__('Arial')),
			array('value'=>'Arial Black', 'label'=>Mage::helper('creative')->__('Arial-black')),
			array('value'=>'Courier New', 'label'=>Mage::helper('creative')->__('Courier New')),
			array('value'=>'Georgia', 'label'=>Mage::helper('creative')->__('Georgia')),
			array('value'=>'Impact', 'label'=>Mage::helper('creative')->__('Impact')),
			array('value'=>'Lucida Console', 'label'=>Mage::helper('creative')->__('Lucida-console')),
			array('value'=>'Lucida Grande', 'label'=>Mage::helper('creative')->__('Lucida-grande')),
			array('value'=>'Palatino', 'label'=>Mage::helper('creative')->__('Palatino')),
			array('value'=>'Tahoma', 'label'=>Mage::helper('creative')->__('Tahoma')),
			array('value'=>'Times New Roman', 'label'=>Mage::helper('creative')->__('Times New Roman')),	
			array('value'=>'Trebuchet', 'label'=>Mage::helper('creative')->__('Trebuchet')),	
			array('value'=>'Verdana', 'label'=>Mage::helper('creative')->__('Verdana'))		
		);
	}
}
