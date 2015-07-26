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
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_SeoSuite_Model_Catalog_Product_Attribute_Source_Meta_Canonical extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {
        //echo "<pre>"; print_r($this->getCanonicalUrl()); exit;
        $productId = Mage::registry('seosuite_product_id');
        $product = Mage::getModel('catalog/product')->load($productId);
        $canonicalUrl = $product->getCanonicalUrl(); 
        $optionList = array();
        if (!$this->_options) {
            $this->_options = array(
                array('value' => '', 'label' => Mage::helper('seosuite')->__('Use Config')),
                array('value' => 'custom', 'label' => Mage::helper('seosuite')->__('Use Custom')),
            );
            $storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
            if ($productId!=null) {
                $collection = Mage::getResourceModel('seosuite/core_url_rewrite_collection')
                    //->addStoreFilter($storeId, false)
                    ->filterAllByProductId($productId)
                    ->groupByUrl()
                    ->sortByLength('ASC');
                if($storeId > 0) {
                    $collection->addStoreFilter($storeId, false);
                }
                $exists = false;
                if ($collection->count()) {
                    foreach ($collection->getItems() as $urlRewrite) {
                        if($urlRewrite->getIdPath() == $canonicalUrl) {
                            $exists = true;
                        }
                        if(!isset($this->_options[$urlRewrite->getStoreId() +2])) {
                            $this->_options[$urlRewrite->getStoreId() +2] = array('label' => Mage::app()->getStore($urlRewrite->getStoreId())->getName(),'value'=>array());
                        }
                        $this->_options[$urlRewrite->getStoreId() +2]['value'][] = array('value' => $urlRewrite->getIdPath(), 'label' => $urlRewrite->getRequestPath());
                    
                    }
                }
                if(!$exists) {
                    $urlRewriteModel = Mage::getModel('core/url_rewrite')->load($canonicalUrl,'id_path');
                    if($urlRewriteModel->getId()) {
                        $this->_options[$urlRewriteModel->getStoreId() +2]['value'][] = array('value' => $urlRewriteModel->getIdPath(), 'label' => $urlRewriteModel->getRequestPath());
                    }
                }
            }
        }
        return $this->_options;
    }
}