<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Model_System_Config_Source_ListGoogleFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'', 'label'=>Mage::helper('creative')->__('No select')),
			array('value'=>'Raleway', 'label'=>Mage::helper('creative')->__('Raleway')),
			array('value'=>'Anton', 'label'=>Mage::helper('creative')->__('Anton')),
			array('value'=>'Questrial', 'label'=>Mage::helper('creative')->__('Questrial')),
			array('value'=>'Kameron', 'label'=>Mage::helper('creative')->__('Kameron')),
			array('value'=>'Oswald', 'label'=>Mage::helper('creative')->__('Oswald')),
			array('value'=>'Open Sans', 'label'=>Mage::helper('creative')->__('Open Sans')),
			array('value'=>'BenchNine', 'label'=>Mage::helper('creative')->__('BenchNine')),
			array('value'=>'Droid Sans', 'label'=>Mage::helper('creative')->__('Droid Sans')),
			array('value'=>'Droid Serif', 'label'=>Mage::helper('creative')->__('Droid Serif')),
			array('value'=>'PT Sans', 'label'=>Mage::helper('creative')->__('PT Sans')),
			array('value'=>'Vollkorn', 'label'=>Mage::helper('creative')->__('Vollkorn')),
			array('value'=>'Ubuntu', 'label'=>Mage::helper('creative')->__('Ubuntu')),
			array('value'=>'Neucha', 'label'=>Mage::helper('creative')->__('Neucha')),
			array('value'=>'Cuprum', 'label'=>Mage::helper('creative')->__('Cuprum'))	
		);
	}
}
