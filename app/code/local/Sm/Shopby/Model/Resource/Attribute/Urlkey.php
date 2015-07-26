<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Resource_Attribute_Urlkey extends Mage_Core_Model_Resource_Db_Abstract{

    protected static $_cachedResults;

    protected function _construct(){
        $this->_init('sm_shopby/attribute_url_key', 'id');
    }

    public function getUrlKey($attributeId, $optionId, $storeId = null){
        foreach ($this->_getOptions($attributeId, $storeId) as $result) {
            if ($result['option_id'] == $optionId) {
                return $result['url_key'];
            }
        }

        return $optionId;
    }

    public function getOptionId($attributeId, $urlKey, $storeId = null){
        foreach ($this->_getOptions($attributeId, $storeId) as $result) {
            if ($result['url_key'] == $urlKey) {
                return $result['option_id'];
            }
        }

        return $urlKey;
    }

    protected function _getOptions($attributeId, $storeId){
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset(self::$_cachedResults[$attributeId][$storeId])) {
            $readAdapter = $this->_getReadAdapter();
            $select = $readAdapter->select()
                ->from($this->getMainTable())
                ->where('`store_id` = ?', $storeId)
                ->where("`attribute_id` = ?", $attributeId);
            $data = $readAdapter->fetchAll($select);

            self::$_cachedResults[$attributeId][$storeId] = $data;
        }

        return self::$_cachedResults[$attributeId][$storeId];
    }

    public function preloadAttributesOptions(Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection, $storeId = null){
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $attributesIds = array();
        foreach ($collection as $attribute) {
            $attributesIds[] = $attribute->getId();
        }
        
        if (empty($attributesIds)) {
            return $this;
        }

        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getMainTable())
            ->where('`store_id` = ?', $storeId)
            ->where('`attribute_id` IN (?)', array('in' => $attributesIds));

        $data = $readAdapter->fetchAll($select);
        foreach ($data as $attr) {
            self::$_cachedResults[$attr['attribute_id']][$attr['store_id']][] = $attr;
        }

        foreach ($attributesIds as $attributeId) {
            if (!isset(self::$_cachedResults[$attributeId][$storeId])) {
                self::$_cachedResults[$attributeId][$storeId] = array();
            }
        }

        return $this;
    }

}