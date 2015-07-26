<?php
/*------------------------------------------------------------------------
 # Yt Slideshow III - Version 1.0
# Copyright (C) 2009-2011 The YouTech JSC. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: The YouTech JSC
# Websites: http://magento.ytcvn.com - http://joomla.ytcvn.com
-------------------------------------------------------------------------*/


class Sm_Cartpro_Model_System_Config_Source_ListEffectSelector
{
	public function toOptionArray()
	{
		return array(
				array('value'=>'click', 'label'=>Mage::helper('cartpro')->__('Click')),
				array('value'=>'hover', 'label'=>Mage::helper('cartpro')->__('Hover'))
		);
	}
}
