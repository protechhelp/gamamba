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

class MageWorx_SeoSuite_Model_Template extends Mage_Core_Model_Abstract
{
    private $_store;
    private $_adminStore;
    
    protected function _construct()
    {
        $this->_init('seosuite/template');
    }
    
    public function loadTitle()
    {
        $storeModel = $this->_loadByCode('product_meta_title');
        return $storeModel->getTemplateKey();
    }
    
    public function isEnable()
    {
        return $this->getStatus();
    }
    
    public function loadDescription()
    {
        $storeModel = $this->_loadByCode('product_description');
        return $storeModel->getTemplateKey();
    }
    
    public function loadMetaDescription()
    {
        $storeModel = $this->_loadByCode('product_meta_description');
        return $storeModel->getTemplateKey();
    }
    
    public function loadUrlKey()
    {
        $storeModel = $this->_loadByCode('product_url');
        return $storeModel->getTemplateKey();
    }
    
    public function loadShortDescription()
    {
        $storeModel = $this->_loadByCode();
        return $storeModel->getTemplateKey();
    }
    public function loadKeywords()
    {
        $storeModel = $this->_loadByCode('product_meta_keywords');
        return $storeModel->getTemplateKey();
    }
    
    public function loadCategoryTitle()
    {
        $storeModel = $this->_loadByCode('category_meta_title');
        return $storeModel->getTemplateKey();
    }
    
    public function loadCategoryMetaDescription()
    {
        $storeModel = $this->_loadByCode('category_meta_description');
        return $storeModel->getTemplateKey();
    }
    
    public function loadCategoryMetaKeywords()
    {
        $storeModel = $this->_loadByCode('category_meta_keywords');
        return $storeModel->getTemplateKey();
    }
    
    public function loadCategoryDescription()
    {
        $storeModel = $this->_loadByCode('category_description');
        return $storeModel->getTemplateKey();
    }
    
    protected function _loadById($id) {
        $storeModel = Mage::getResourceModel('seosuite/template_store_collection')
               ->filterStoreId($this->getStore()->getId())
               ->addStatus()
               ->filterTemplateId($id)
               ->getFirstItem();
       
        if(!$storeModel->getId()) {
            $storeModel = Mage::getResourceModel('seosuite/template_store_collection')
               ->filterStoreId(0)
               ->addStatus()
               ->filterTemplateId($id)
               ->getFirstItem();
        }
        if(!$storeModel->getStatus()) {
            return $storeModel->setTemplateKey(false);
        }
        return $storeModel;
    }
    
    protected function _loadByCode($code) {
        $storeModel = Mage::getResourceModel('seosuite/template_store_collection')
               ->filterStoreId($this->getStore()->getId())
               ->addStatus()
               ->filterTemplateCode($code)
               ->getFirstItem();
       
        if(!$storeModel->getId()) {
            $storeModel = Mage::getResourceModel('seosuite/template_store_collection')
               ->filterStoreId(0)
               ->addStatus()
               ->filterTemplateCode($code)
               ->getFirstItem();
        }
      //  echo "<pre>"; print_r($storeModel->toArray()); exit;
        if(!$storeModel->getStatus()) {
            return $storeModel->setTemplateKey(false);
        }
        return $storeModel;
    }


    public function _afterLoad() 
    {
        $this->setStore($this->getStore());
        
        $storeModel = Mage::getResourceModel('seosuite/template_store_collection')
                ->filterStoreId($this->getStore()->getId())
                ->filterTemplateId($this->getId())
                ->getFirstItem();
        if (!$storeModel->getId())
        {
            $storeModel = Mage::getResourceModel('seosuite/template_store_collection')
                ->filterStoreId($this->getAdminStore()->getId())
                ->filterTemplateId($this->getId())
                ->getFirstItem();
            $storeModel->setData('is_default_value',true);
        }
        $data = $this->getData();
        $storeData = $storeModel->getData();
        $fullData = array_merge($data,$storeData);
        $this->setData($fullData);
        parent::_afterLoad();
    }
    
    public function getStore()
    {
        if(!$this->_store) {
            $storeId = 0;
            $storeId = Mage::app()->getStore()->getId();
            if(Mage::app()->getStore()->isAdmin())
            {
                $storeId = Mage::app()->getStore()->getId();
                $storeId = Mage::app()->getRequest()->getParam('store');
            }
            $this->_store = Mage::app()->getStore($storeId);
        }
        return $this->_store;
    }
    
    private function getAdminStore()
    {
        if(!$this->_adminStore) {
            $stores = Mage::app()->getStores();
            foreach ($stores as $store) {
                if($store->isAdmin()) {
                    return $this->_adminStore = $store;
                    
                }
            }
            $this->_adminStore = Mage::app()->getStore(0);
        }
        return $this->_adminStore;
    }
    
    protected function _beforeSave() {
        $storeData = $this->getStoreData();
        $storeData = $storeData['store_template'];
        $storeModel = Mage::getModel('seosuite/template_store');
        if($storeData['entity_id'])
        {
            $storeModel->load($storeData['entity_id']);
        }
        if(!$storeModel->getEntityId()) {
            unset($storeModel);
            $storeModel = Mage::getModel('seosuite/template_store');
        }
        if(!isset($storeData['template_key'])) {
            if($storeData['entity_id']) {
                $storeModel->delete();
            }
            return true;
        }
      //  echo $storeModel->getId(); exit;
        $storeModel->setTemplateId($this->getId())
                   ->setStoreId($storeData['store_id'])
                   ->setTemplateKey($storeData['template_key'])
                   ->setLastUpdate(date("Y-m-d H:i:s",$this->getLastUpdate()));
        
        if(!$storeModel->getId())
        {
            $storeModel->setId(NULL);
        }
        try {
            $storeModel->save();
        } catch (Exception $e){
            return $e;
        }
        parent::_beforeSave();
    }
}