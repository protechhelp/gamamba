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
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_SeoSuite_Block_Rss_List extends Mage_Rss_Block_List
{
    private function __addRssFeed($url, $label)
    {
        $this->_rssFeeds[] = new Varien_Object(
            array(
                'url'   => $url,
                'label' => $label
            )
        );
        return $this;
    }

    public function NewProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/new';
        if((bool)Mage::getStoreConfig($path)){
            $this->__addRssFeed(Mage::getUrl('rss/') . Mage::app()->getStore()->getCode() . '/@new', $this->__('New Products'));
        }
    }

    public function SpecialProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/special';
        if((bool)Mage::getStoreConfig($path)){
            $this->__addRssFeed(Mage::getUrl('rss/') . Mage::app()->getStore()->getCode() . '/@specials/' . $this->getCurrentCustomerGroupId(), $this->__('Special/Discount Products'));
        }
    }

    public function SalesRuleProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/salesrule';
        if((bool)Mage::getStoreConfig($path)){
            $this->__addRssFeed(Mage::getUrl('rss/') . Mage::app()->getStore()->getCode() . '/@discounts/' . $this->getCurrentCustomerGroupId(), $this->__('Coupons/Discounts'));
        }
    }

    public function CategoriesRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/category';
        if ((bool)Mage::getStoreConfig($path)){
            $category = Mage::getModel('catalog/category');

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $treeModel = $category->getTreeModel()->loadNode(Mage::app()->getStore()->getRootCategoryId());
            $nodes = $treeModel->loadChildren()->getChildren();

            $nodeIds = array();
            foreach ($nodes as $node){
                $nodeIds[] = $node->getId();
            }

            $collection = $category->getCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_anchor')
                ->addAttributeToFilter('is_active',1)
                ->addIdFilter($nodeIds)
                ->addAttributeToSort('name')
                ->load();

            foreach ($collection as $category) {
                $this->__addRssFeed(Mage::getUrl('rss/') . Mage::app()->getStore()->getCode() . '/' . $category->getUrlKey(), $category->getName());
            }
        }
    }
}