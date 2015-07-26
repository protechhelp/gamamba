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
 * Customercredit Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Product_View extends Mage_Catalog_Block_Product_View_Abstract {

    public function getCreditAmount($product) {
        $creditAmount = $product->getCreditAmount();
        if (!$creditAmount)
            $creditAmount = Mage::helper('customercredit')->getGeneralConfig('amount');
        $creditAmount = Mage::helper('customercredit')->getCreditAmount($creditAmount);
        $store = Mage::app()->getStore();
        switch ($creditAmount['type']) {
            case 'range':
                $creditAmount['from'] = $this->convertPrice($product, $creditAmount['from']);
                $creditAmount['to'] = $this->convertPrice($product, $creditAmount['to']);
                $creditAmount['from_txt'] = $store->formatPrice($creditAmount['from']);
                $creditAmount['to_txt'] = $store->formatPrice($creditAmount['to']);
                break;
            case 'dropdown':
                $creditAmount['options'] = $this->_convertPrices($product, $creditAmount['options']);
                $creditAmount['options_txt'] = $this->_formatPrices($creditAmount['options']);
                break;
            case 'static':
                $creditAmount['value'] = $this->convertPrice($product, $creditAmount['value']);
                $creditAmount['value_txt'] = $store->formatPrice($creditAmount['value']);
                break;
            default:
                $creditAmount['type'] = 'any';
        }
        return $creditAmount;
    }

    protected function _convertPrices($product, $basePrices) {
        foreach ($basePrices as $key => $price)
            $basePrices[$key] = $this->convertPrice($product, $price);
        return $basePrices;
    }

    public function convertPrice($product, $price) {
        $includeTax = ( Mage::getStoreConfig('tax/display/type') != 1 );
        $store = Mage::app()->getStore();

        $priceWithTax = Mage::helper('tax')->getPrice($product, $price, $includeTax);
        return $store->convertPrice($priceWithTax);
    }

    protected function _formatPrices($prices) {
        $store = Mage::app()->getStore();
        foreach ($prices as $key => $price)
            $prices[$key] = $store->formatPrice($price, false);
        return $prices;
    }

    public function getFormConfigData() {
        $request = Mage::app()->getRequest();
        $action = $request->getRequestedRouteName() . '_' . $request->getRequestedControllerName() . '_' . $request->getRequestedActionName();
        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            $request = Mage::app()->getRequest();
            $options = Mage::getModel('sales/quote_item_option')->getCollection()->addItemFilter($request->getParam('id'));
            $formData = array();
            foreach ($options as $option)
                $formData[$option->getCode()] = $option->getValue();
            return new Varien_Object($formData);
        }
        return new Varien_Object();
    }

}
