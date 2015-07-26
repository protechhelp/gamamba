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
class MageWorx_Seosuite_Model_Richsnippet_Catalog_Product_Price_Grouped extends MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Price_Abstract
{
    protected $_validViewBlockType = 'Mage_Catalog_Block_Product_View_Type_Grouped';

    /**
     * Return array prices (include tax / exclude tax sorted by priority)
     * of associated product have minimal price or itself prices.
     * @return array
     */
    protected function _getItemValues($product = null)
    {
        $associatedProducts = $this->_block->getAssociatedProducts();

        if(count($associatedProducts)){
            $allProductPrices = array();
            foreach($associatedProducts as $product){
                $productPrices =  parent::_getItemValues($product);
                $allProductPrices[(string)$productPrices[0]] = $productPrices;
            }
            if(count($allProductPrices)){
                ksort($allProductPrices);
                return array_shift($allProductPrices);
            }
        }
        return parent::_getItemValues();
    }
}