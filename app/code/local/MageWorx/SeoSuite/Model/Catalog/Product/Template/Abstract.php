<?php

/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */
abstract class MageWorx_SeoSuite_Model_Catalog_Product_Template_Abstract extends Varien_Object
{

    protected $_product = null;

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $store          = Mage::app()->getStore($product->getStoreId());
        $product->setStore($store);
        $this->_product = $product;
        return $this;
    }

    protected function __parse($template)
    {
        $vars = array();
        preg_match_all('~(\[(.*?)\])~', $template, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            preg_match('~^((?:(.*?)\{(.*?)\}(.*)|[^{}]*))$~', $match[2], $params);
            array_shift($params);

            if (count($params) == 1) {
                $vars[$match[1]]['prefix']     = $vars[$match[1]]['suffix']     = '';
                $vars[$match[1]]['attributes'] = explode('|', $params[0]);
            }
            else {
                $vars[$match[1]]['prefix']     = $params[1];
                $vars[$match[1]]['suffix']     = $params[3];
                $vars[$match[1]]['attributes'] = explode('|', $params[2]);
            }
        }
        return $vars;
    }

    protected function __compile($template)
    {
        $vars         = $this->__parse($template);
        $taxHelper    = Mage::helper('tax');
        if ($taxHelper->displayPriceIncludingTax()) $includingTax = true;
        else $includingTax = null;
        foreach ($vars as $key => $params) {
            foreach ($params['attributes'] as $n => $attribute) {
                if (in_array($attribute, $this->_useDefault)) {
                    $product = &$this->_defaultProduct;
                }
                else {
                    $product = &$this->_product;
                }

                $value = '';

                switch ($attribute) {
                    case 'category':
                        $category = $this->_product->getCategory();
                        if ($category) {
                            $value = $this->_product->getCategory()->getName();
                        }
                        else {
                            $categoryItems = $this->_product->getCategoryCollection()->load()->getIterator();
                            $category      = current($categoryItems);
                            if ($category) {
                                $category = Mage::getModel('catalog/category')->load($category->getId());
                                $value    = $category->getName();
                            }
                        }
                        break;
                    case 'categories':
                        if (!Mage::registry('current_category')) {
                            $value = '';
                        }
                        else {
                            $separator = (string) Mage::getStoreConfig('catalog/seo/title_separator');
                            $separator = ' ' . $separator . ' ';
                            $title     = array();
                            $path      = Mage::helper('catalog')->getBreadcrumbPath();
                            foreach ($path as $name => $breadcrumb) {
                                $title[] = $breadcrumb['label'];
                            }
                            array_pop($title);
                            $value = join($separator, array_reverse($title));
                            if (!$value) {
                                $value = '[categories]';
                            }
                        }
                        break;
                    case 'store_view_name':
                        $value   = Mage::app()->getStore($this->_product->getStoreId())->getName();
                        break;
                    case 'store_name':
                        $value   = Mage::app()->getStore($this->_product->getStoreId())->getGroup()->getName();
                        break;
                    case 'website_name':
                        $value   = Mage::app()->getStore($this->_product->getStoreId())->getWebsite()->getName();
                        break;
                    case 'price':
                        $product = $this->_product;
                        if ($product->getTypeId() == 'bundle') {
                            if ($product->getStoreId() !== "") {
                                list($_minimalPriceTax, $_maximalPriceTax) = $product->getPriceModel()->getTotalPrices($product,
                                        null, null, false);
                                //   echo "$_minimalPriceTax, $_maximalPriceTax"; exit;
                                $value = Mage::helper('core')->__('From') . ' ' . Mage::helper('core')->currency($_minimalPriceTax,
                                                true, FALSE) . " " . Mage::helper('core')->__('To') . " " . Mage::helper('core')->currency($_maximalPriceTax,
                                                true, FALSE);
                            }
                            else {
                                $value = '';
                            }
                        }
                        elseif ($product->getTypeId() == 'grouped') {
                            if ($product->getMinimalPrice()) {
                                $price = Mage::helper('tax')->getPrice($product, $product->getMinimalPrice(),
                                        $includingTax);
                                $value = Mage::helper('core')->__('Starting at') . ' ' . Mage::helper('core')->currencyByStore($price,
                                                $product->getStore(), true, FALSE);
                            }
                        }
                        else {
                            if ($product->getPrice() > 0) {
                                $price = Mage::helper('tax')->getPrice($product, $product->getPrice(), $includingTax);
                                $value = Mage::helper('core')->currencyByStore($price, $product->getStore(), true, FALSE);
                                if ($product->getMinimalPrice() && $product->getMinimalPrice() <> $product->getPrice()) {
                                    $price = Mage::helper('tax')->getPrice($product, $product->getMinimalPrice(),
                                            $includingTax);
                                    $value = Mage::helper('core')->__('Starting at') . ' ' . Mage::helper('core')->currencyByStore($price,
                                                    $product->getStore(), true, FALSE);
                                }
                            }
                        }
                        break;
                    case 'special_price':
                        $product = $this->_product;
                        if ($product->getSpecialPrice() > 0) {
                            $price = Mage::helper('tax')->getPrice($product, $product->getSpecialPrice(), $includingTax);
                            $value = Mage::helper('core')->currencyByStore($price, $product->getStore(), true, FALSE);
                        }
                        break;
                    default:
                        $tempValue = '';
                        $value     = $product->getData($attribute);
                        if ($_attr = $product->getResource()->getAttribute($attribute)) {
                            $_attr->setStoreId($product->getStoreId());
                            $tempValue = $_attr->setStoreId($product->getStoreId())->getSource()->getOptionText($product->getData($attribute));
                        }
                        if ($tempValue) {
                            $value = $tempValue;
                        }
                        if (!$value) {
                            if ($product->getTypeId() == 'configurable') {
                                $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                                $attributeOptions        = array();
                                foreach ($productAttributeOptions as $productAttribute) {
                                    if ($productAttribute['attribute_code'] == $attribute) {
                                        foreach ($productAttribute['values'] as $attribute) {
                                            $attributeOptions[] = $attribute['store_label'];
                                        }
                                    }
                                }
                                if (count($attributeOptions) == 1) {
                                    $value = array_shift($attributeOptions);
                                }
                            }
                            else {
                                $value = $product->getData($attribute);
                            }
                        }
                        if (is_array($value)) $value = implode(' ', $value);
                }

                if ($value) {
                    $value = $params['prefix'] . $value . $params['suffix'];
                    break;
                }
            }
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }

    public function getCompile($product, $template)
    {
        $this->_product = $product;
        return $this->__compile($template);
    }

}