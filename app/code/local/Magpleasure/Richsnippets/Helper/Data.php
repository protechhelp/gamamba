<?php

/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Richsnippets
 * @copyright  Copyright (c) 2014-2015 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */
class Magpleasure_Richsnippets_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Value from config path 'richsnippets/group/field'
     *
     * @param $group
     * @param $field
     * @return mixed
     */
    public function getConfigValue($group, $field)
    {
        return Mage::getStoreConfig('richsnippets' . DS . $group . DS . $field);
    }

    /**
     * Calculate default configurable price for bundle product
     *
     * @param $product
     * @return int
     */
    public function getConfiguredPrice($product)
    {
        if (Mage_Catalog_Model_Product_Type::TYPE_BUNDLE != $product->getTypeId()) {
            return 0;
        }
        $priceModel = $product->getPriceModel();
        $bundleBlock = Mage::getSingleton('core/layout')->createBlock('bundle/catalog_product_view_type_bundle');
        $options = $bundleBlock->setProduct($product)->getOptions();
        $price = 0;
        /** @var Mage_Bundle_Model_Option $option */
        foreach ($options as $option) {
            $selection = $option->getDefaultSelection();
            if (null === $selection) {
                continue;
            }
            $price += $priceModel->getSelectionPreFinalPrice($product, $selection, $selection->getSelectionQty());
        }
        return $price;
    }

    /**
     * Calculate default price for grouped product
     *
     * @param      $groupedProduct
     * @param bool $incTax
     * @return int
     */
    public function getGroupedProductPrice($groupedProduct, $incTax = true)
    {
        $productIds = $groupedProduct->getTypeInstance()->getChildrenIds($groupedProduct->getId());
        $price = 0;
        foreach ($productIds as $ids) {
            foreach ($ids as $id) {
                $product = Mage::getModel('catalog/product')->load($id);
                if ($incTax) {
                    $price += Mage::helper('tax')->getPrice($product, $product->getPriceModel()->getFinalPrice(null, $product, true), true);
                } else {
                    $price += $product->getPriceModel()->getFinalPrice(null, $product, true);
                }
            }
        }
        return $price;
    }
}
	 