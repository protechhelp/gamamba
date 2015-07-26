<?php

class Creare_DynamicSitemap_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function showCMS()
	{
		return Mage::getStoreConfig('dynamicsitemap/dynamicsitemap/showcms');
	}

	public function showCategories()
	{
		return Mage::getStoreConfig('dynamicsitemap/dynamicsitemap/showcategories');
	}

	public function showXMLSitemap()
	{
		return Mage::getStoreConfig('dynamicsitemap/dynamicsitemap/showxml');
	}

	public function showAccount()
	{
		return Mage::getStoreConfig('dynamicsitemap/dynamicsitemap/showaccount');
	}

	public function showContact()
	{
		return Mage::getStoreConfig('dynamicsitemap/dynamicsitemap/showcontact');
	}
}