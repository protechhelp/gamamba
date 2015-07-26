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

class Mmsmods_Serialcodes_Model_Product_Customer_Groups_Backend extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	const ATTRIBUTE_CODE = 'serial_code_customer_groups';

	public function beforeSave($object)
	{
		$attributeCode = $this->getAttribute()->getName();
		if ($attributeCode == self::ATTRIBUTE_CODE) {
			$data = $object->getData($attributeCode);
			$postData = Mage::app()->getRequest()->getPost('product');
			if (!empty($postData) && empty($postData[$attributeCode])) {
				$data = array();
			}
			if (is_array($data)) {
				$object->setData($attributeCode, implode(',', $data));
			}
		}
		parent::beforeSave($object);
	}

	public function afterLoad($object)
	{
		$attributeCode = $this->getAttribute()->getName();
		if ($attributeCode == self::ATTRIBUTE_CODE) {
			$data = $object->getData($attributeCode);
			if (!is_array($data)) {
				$object->setData($attributeCode, explode(',', $data));
			}
		}
		parent::afterLoad($object);
	}
}