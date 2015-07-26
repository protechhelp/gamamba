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

class Mmsmods_Serialcodes_Block_Bundle_Sales_Order_Items_Renderer extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
	public function getValueHtml($item)
	{
		$html = parent::getValueHtml($item);
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		if($product->getSerialCodeShowOrder()) {
			$sc_model = Mage::getSingleton('serialcodes/serialcodes');
			$name = $this->htmlEscape($item->getName());
			$codetype = $item->getSerialCodeType();
			$codes = explode("\n",$item->getSerialCodes());
			$count = count($codes);
			$local = '';
			if ($codes[0]) {
				$order = Mage::getSingleton('sales/order')->load($item->getOrderId());
				$codeids = array_pad(explode(',',$item->getSerialCodeIds()),$count,'');
				for ($i=0; $i<$count; $i++) {
					if ($sc_model->hidePendingCodes($order, $item, $product, $codeids[$i], $i)) {
						$codes[$i] = Mage::helper('serialcodes')->__('Issued when payment received.');
					}
					$local .= '</br>'.$codetype.': '.$codes[$i];
				}
			}
			$test = trim(strip_tags($local));
			if ($test && $test <> ':') {$html = $html . $local;}
		}
		return $html;
	}
}