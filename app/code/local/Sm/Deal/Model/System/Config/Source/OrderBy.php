<?php
/*------------------------------------------------------------------------
 # SM Deal - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Deal_Model_System_Config_Source_OrderBy
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'name',                'label' => Mage::helper('deal')->__('Name')),
			array('value' => 'entity_id',           'label' => Mage::helper('deal')->__('Id')),
			array('value' => 'created_at',          'label' => Mage::helper('deal')->__('Date Created')),
			array('value' => 'price',               'label' => Mage::helper('deal')->__('Price')),
			array('value' => 'lastest_product',     'label' => Mage::helper('deal')->__('Lastest Product')),
			array('value' => 'top_rating',          'label' => Mage::helper('deal')->__('Top Rating')),
			array('value' => 'most_reviewed',       'label' => Mage::helper('deal')->__('Most Reviews')),
			array('value' => 'most_viewed',         'label' => Mage::helper('deal')->__('Most Viewed')),
			array('value' => 'best_sales',          'label' => Mage::helper('deal')->__('Most Selling')),
			array('value' => 'random',              'label' => Mage::helper('deal')->__('Random')),
		);
	}
}
