<?php
/*------------------------------------------------------------------------
 # SM Categories - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Categories_Model_System_Config_Source_LinkTargets
{
	public function toOptionArray()
	{
		return array(
			array('value'	=>		'_self',		'label'=>Mage::helper('categories')->__('Same Window')),
        	array('value'	=>		'_blank',		'label'=>Mage::helper('categories')->__('New Window')),
			array('value'	=>		'_windowopen',	'label'=>Mage::helper('categories')->__('Popup Window'))
		);
	}
}
