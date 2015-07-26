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

class MageWorx_SeoSuite_Block_Rss_Catalog_New extends Mage_Rss_Block_Catalog_New
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        //$this->setCacheKey('rss_catalog_new_'.$this->_getStoreId());
        //$this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $storeId = $this->_getStoreId();

        $newurl = Mage::helper('core/url')->getCurrentUrl();
        $title = Mage::helper('rss')->__('New Products from %s', Mage::app()->getStore()->getGroup()->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'generator'   => Mage::helper('seosuite')->getRssGenerator(),
                'language'    => $lang
                );
        $rssObj->_addHeader($data);
/*
oringinal price - getPrice() - inputed in admin
special price - getSpecialPrice()
getFinalPrice() - used in shopping cart calculations
*/

        $product = Mage::getModel('catalog/product');
        $todayDate = $product->getResource()->formatDate(time());

        $products = $product->getCollection()
            ->setStoreId($storeId)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
            ->addAttributeToFilter(array(array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$todayDate), array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))),'','left')
            ->addAttributeToSort('news_from_date','desc')
            ->addAttributeToSelect(array('name', 'short_description', 'description', 'price', 'thumbnail'), 'inner')
            ->addAttributeToSelect(array('special_price', 'special_from_date', 'special_to_date'), 'left')
        ;

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
//echo $products->getSelect();
        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($products->getSelect(), array(array($this, 'addNewItemXmlCallback')), array('rssObj'=> $rssObj, 'product'=>$product));

        return $rssObj->createRssXml();
    }

    public function addNewItemXmlCallback($args)
    {
        $product = $args['product'];
        //$product->unsetData()->load($args['row']['entity_id']);
        $product->setData($args['row']);
        $final_price = $product->getFinalPrice();
        $description = '<table><tr>'.
            '<td><a href="'.$product->getProductUrl().'"><img src="'. $this->helper('catalog/image')->init($product, 'thumbnail')->resize(75, 75) .'" border="0" align="left" height="75" width="75"></a></td>'.
            '<td  style="text-decoration:none;">'.$product->getDescription().
            '<p> Price:'.Mage::helper('core')->currency($product->getPrice()).
            ($product->getPrice() != $final_price  ? ' Special Price:'. Mage::helper('core')->currency($final_price) : '').
            '</p>'.
            '</td>'.
            '</tr></table>';
        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,

                );
        $rssObj->_addEntry($data);
    }
}