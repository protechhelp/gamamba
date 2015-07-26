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

class MageWorx_SeoSuite_Block_Rss_Catalog_Category extends Mage_Rss_Block_Catalog_Category
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_category_'
            .$this->getRequest()->getParam('cid').'_'
            .$this->getRequest()->getParam('store_id')
        );
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $categoryId = $this->getRequest()->getParam('cid');
        $storeId = $this->_getStoreId();
        $rssObj = Mage::getModel('rss/rss');
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category && $category->getId()) {
                $layer = Mage::getSingleton('catalog/layer')->setStore($storeId);
                //want to load all products no matter anchor or not
                $category->setIsAnchor(true);
                $newurl = $category->getUrl();
                $title = $category->getName();
                $data = array('title' => $title . ' @ ' . Mage::app()->getStore()->getGroup()->getName(),
                        'description' => $title,
                        'link'        => $newurl,
                        'charset'     => 'UTF-8',
                        'generator'   => Mage::helper('seosuite')->getRssGenerator()
                        );

                $rssObj->_addHeader($data);

                $_collection = $category->getCollection();
                $_collection->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_anchor')
                    ->addAttributeToFilter('is_active',1)
                    ->addIdFilter($category->getChildren())
                    ->load()
                ;
                $productCollection = Mage::getModel('catalog/product')->getCollection();

                $currentyCateogry = $layer->setCurrentCategory($category);
                $layer->prepareProductCollection($productCollection);
                $productCollection->addCountToCategories($_collection);

                /*if ($_collection->count()) {
                    foreach ($_collection as $_category){
                         $data = array(
                                    'title'         => $_category->getName(),
                                    'link'          => $_category->getCategoryUrl(),
                                    'description'   => $this->helper('rss')->__('Total Products: %s', $_category->getProductCount()),
                                    );

                        $rssObj->_addEntry($data);
                    }
                }
                */
                $category->getProductCollection()->setStoreId($storeId);
                /*
                only load latest 50 products
                */
                $_productCollection = $currentyCateogry
                    ->getProductCollection()
                    ->addAttributeToSort('updated_at','desc')
                    ->setCurPage(1)
                    ->setPageSize(50)
                ;
                if ($_productCollection->getSize()>0) {
                    foreach ($_productCollection as $_product) {
                        $final_price = $_product->getFinalPrice();
                        $description = '<table><tr>'.
                            '<td><a href="'.$_product->getProductUrl().'"><img src="' . $this->helper('catalog/image')->init($_product, 'thumbnail')->resize(75, 75)
                            .'" border="0" align="left" height="75" width="75"></a></td>'.
                            '<td  style="text-decoration:none;">'.$_product->getDescription().
                            '<p> Price:'.Mage::helper('core')->currency($_product->getPrice()).
                            ($_product->getPrice() != $final_price  ? ' Special Price:'. Mage::helper('core')->currency($final_price) : '').
                            '</p>'.
                            '</td>'.
                            '</tr></table>'
                        ;
                        $data = array(
                                    'title'         => $_product->getName(),
                                    'link'          => $_product->getProductUrl(),
                                    'description'   => $description,
                                    );

                        $rssObj->_addEntry($data);
                    }
                }
            }
        }
        return $rssObj->createRssXml();

    }
}