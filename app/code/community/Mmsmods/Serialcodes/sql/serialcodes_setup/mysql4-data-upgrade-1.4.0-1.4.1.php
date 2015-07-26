<?php
/*
* ModifyMage Solutions (http://ModifyMage.com)
* Serial Codes - Serial Numbers, Product Codes, PINs, and More
*
* NOTICE OF LICENSE
* This source code is owned by ModifyMage Solutions and distributed for use under the
* provisions, terms, and conditions of our Commercial Software License Agreement which
* is bundled with this package in the file LICENSE.txt. This license is also available
* through the world-wide-web at this URL: http://www.modifymage.com/software-license
* If you do not have a copy of this license and are unable to obtain it through the
* world-wide-web, please email us at license@modifymage.com so we may send you a copy.
*
* @category		Mmsmods
* @package		Mmsmods_Serialcodes
* @author		David Upson
* @copyright	Copyright 2013 by ModifyMage Solutions
* @license		http://www.modifymage.com/software-license
*/

$installer = $this;
$installer->startSetup();
	$sc_model = Mage::getSingleton('serialcodes/serialcodes');
	$oldver = (!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && version_compare(Mage::getVersion(),'1.4.1.1') < 0);
	$items = Mage::getSingleton('sales/order_item')->getCollection()
		->addFieldToSelect(array('created_at', 'product_id', 'product_type', 'product_options', 'sku', 'serial_codes', 'serial_code_ids', 'serial_codes_issued', 'serial_code_pool'))
		->addFieldToFilter('serial_codes', array('notnull'=>''))
//		->addFieldToFilter('created_at', array('gt' => date('Y-m-d H:i:s', strtotime('2012-05-01 00:00:00'))))
		->setOrder('created_at');
//		$items->getSelect()->limit(5000);
	foreach ($items as $item) {
		$pcodes = explode("\n", $item->getSerialCodes());
		$issued = count($pcodes);
		$icodes = array_fill(0, $issued, '');
		if ($oldver) {
			$storeid = Mage::getSingleton('sales/order')->load($item->getOrderId())->getStoreId();
		} else {
			$storeid = $item->getStoreId();
		}
		$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
		if (!$sku = trim($product->getSerialCodePool())) {$sku = trim($product->getSku());}
		$codes = $sc_model->getCollection()->addFieldToFilter('sku', array('like' => $sku));
		if ($codes->getData() == null && $item->getProductType() != 'bundle') {
			if ($item->getProductType() == 'configurable') {
				$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($product->getIdBySku($item->getProductOptionByCode('simple_sku')));
				if (!$sku = trim($product->getData('serial_code_pool'))) {$sku = trim($product->getSku());}
				$codes = $sc_model->getCollection()->addFieldToFilter('sku',array('like' => $sku));
			} elseif (trim($item->getSku()) != trim($product->getSku())) {
				$sku = trim($item->getSku());
				$codes = $sc_model->getCollection()->addFieldToFilter('sku',array('like' => $sku));
			}
		}
		if (count($codes)) {
			for ($i=0; $i<$issued; $i++) {
				$codes = $sc_model->getCollection()
					->addFieldToFilter('sku',array('like' => $sku))
					->addFieldToFilter('code', array('eq' => $pcodes[$i]));
				if (count($codes)) {$icodes[$i] = $codes->getFirstItem()->getSerialcodesId();}
			}
		} else {$sku = '';}
		$item
			->setSerialCodeIds(implode(',',$icodes))
			->setSerialCodesIssued($issued)
			->setSerialCodePool($sku)
			->save();
	}
$installer->endSetup();