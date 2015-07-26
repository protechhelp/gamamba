<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customercredit Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_CustomerCredit_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{
	public function prepareForCart(Varien_Object $buyRequest, $product = null){
		if (version_compare(Mage::getVersion(),'1.5.0','>='))
			return parent::prepareForCart($buyRequest,$product);
		if (is_null($product))
			$product = $this->getProduct();
		$result = parent::prepareForCart($buyRequest, $product);
		if (is_string($result))
			return $result;
		reset($result);
		$product = current($result);
		$result = $this->_prepareCustomerCredit($buyRequest,$product);
		return $result;
	}

	protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode){
		if (version_compare(Mage::getVersion(),'1.5.0','<'))
			return parent::_prepareProduct($buyRequest,$product,$processMode);
		if (is_null($product))
			$product = $this->getProduct();
		$result = parent::_prepareProduct($buyRequest, $product, $processMode);
		if (is_string($result))
			return $result;
		reset($result);
		$product = current($result);
		$result = $this->_prepareCustomerCredit($buyRequest,$product);
		return $result;
	}

	protected function _prepareCustomerCredit(Varien_Object $buyRequest, $product){
		$store = Mage::app()->getStore();
		if ($store->isAdmin()){
			$amount = $product->getPrice();
		}else{
			if ($buyRequest->getAmount()){
				$amount = $buyRequest->getAmount();
				$includeTax = ( Mage::getStoreConfig('tax/display/type') != 1 );
				$amount = $amount * 100 / Mage::helper('tax')->getPrice($product, 100, $includeTax);
			}else
				$amount = $product->getPrice();
			if (!$amount)
				return Mage::helper('customercredit')->__('Please enter customercredit information');
		}
		if (!$buyRequest->getAmount())
			$buyRequest->setAmount($amount);
		$product->addCustomOption('amount', $amount);

		return array($product);
	}

	public function isVirtual($product = null){
		if (is_null($product))
			$product = $this->getProduct();

		$item = Mage::getModel('checkout/session')->getQuote()->getItemByProduct($product);
		if (!$item) return false;

		$options = array();
		foreach ($item->getOptions() as $option) $options[$option->getCode()] = $option->getValue();
		if (empty($options['recipient_ship'])) return true;

		return false;
	}

	public function hasRequiredOptions($product = null){
		return true;
	}
}