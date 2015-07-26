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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_XSitemap_Block_Catalog_Products extends Mage_Core_Block_Template
{

    const XML_PATH_SORT_ORDER = 'mageworx_seo/xsitemap/sort_order';

    public function getCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */

        $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('url_key')
                ->addStoreFilter()
                ->addUrlRewrite($this->getCategory()->getId())
                ->setOrder(Mage::getStoreConfig(self::XML_PATH_SORT_ORDER), 'ASC');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $collection->addCategoryFilter($this->getCategory());
        //$collection->load(true);
        return $collection;
    }

    public function getItemUrl($product)
    {
        if (version_compare(Mage::getVersion(), '1.2', '<')) {
            return $product->getProductUrl($product);
        }
        $url       = '';
        $seoHelper = Mage::helper('seosuite');
        if ((string) Mage::getConfig()->getModuleConfig('MageWorx_SeoSuite')->active == 'true' && method_exists($seoHelper,
                        'getCanonicalUrl')) {
            $url = $seoHelper->getCanonicalUrl($product);
        }
        return $url ? Mage::getBaseUrl() . $url : Mage::helper('catalog/product')->getProductUrl($product);
    }

}
