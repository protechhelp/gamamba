<?php
/*------------------------------------------------------------------------
 # SM Deal - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Deal_Model_System_Config_Source_ListSource
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'catalog',     'label' => Mage::helper('deal')->__('Catalog')),
			array('value' => 'ids',         'label' => Mage::helper('deal')->__('Product IDs to Exclude'))
		);
	}
}
