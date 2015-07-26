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

class MageWorx_SeoSuite_Model_Catalog_Product_Attribute_Backend_Meta_Canonical extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract {

    private $_hashId;
    
    public function beforeSave($object)
    {
        if(!Mage::app()->getRequest()->getParam('canonical_url_custom')) return parent::beforeSave($object);
        $stores = array();
        echo "<pre>"; print_r($object->getCanonicalUrl()); //exit;
        if(Mage::app()->getRequest()->getParam('store')) {
            $stores[] = Mage::app()->getRequest()->getParam('store');
            $product = Mage::getSingleton('catalog/product')
			->setStoreId(Mage::app()->getRequest()->getParam('store'))
			->load($object->getId());
        }
        else {
            foreach (Mage::app()->getStores() as $store) {
                $product = Mage::getSingleton('catalog/product')
			->setStoreId($store->getId())
			->load($object->getId());
                if($product) {
                    $stores[] = $store->getId();
                }
            }
             $product = Mage::getSingleton('catalog/product')->load($object->getId());
        }
        foreach ($stores as $storeId) {
            $hashID = str_replace('0.', '', str_replace(' ', '_', microtime()));
            if(!$this->_hashId) {
                $this->_hashId = $hashID;
            }
            
            try {
                Mage::getModel('core/url_rewrite')
                ->setStoreId($storeId)
                ->setCategoryId(null)
                ->setProductId($object->getId())
                ->setIdPath($hashID)
                ->setRequestPath(Mage::app()->getRequest()->getParam('canonical_url_custom'))
                ->setTargetPath($product->getUrlPath())
                ->setIsSystem(1)
                ->setOptions('RP')
                ->save();
            } catch (Exception $e) {
                 $obj = Mage::getModel('core/url_rewrite')->load(Mage::app()->getRequest()->getParam('canonical_url_custom'),'request_path');
                 $this->_hashId = $obj->getIdPath();
            }
        }
        $product = Mage::app()->getRequest()->getParam('product');
        $product['canonical_url'] = $this->_hashId;
        Mage::app()->getRequest()->setParam('product', $product);
        $object->setCanonicalUrl($this->_hashId);
        return parent::beforeSave($object);
    }
}