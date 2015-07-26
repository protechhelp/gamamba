<?php
/*------------------------------------------------------------------------
 # SM QuickView - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Quickview_Model_System_Config_Source_ListEffects {
	public function toOptionArray()
	{
		return array(
				array('value'=>'elastic', 'label'=>Mage::helper('quickview')->__('Elastic')),
				array('value'=>'fade', 'label'=>Mage::helper('quickview')->__('Fade')),
				array('value'=>'none', 'label'=>Mage::helper('quickview')->__('None'))
		);
	}
}
