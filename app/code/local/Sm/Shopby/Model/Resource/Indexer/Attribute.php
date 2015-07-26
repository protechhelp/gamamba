<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Resource_Indexer_Attribute extends Mage_Index_Model_Resource_Abstract{

    protected $_storesIds;
    protected $_helper;

    protected function _construct(){
        $this->_init('sm_shopby/attribute_url_key', 'id');
    }

    public function reindexAll(){
        $this->reindexSeoUrlKeys();
        return $this;
    }

    public function reindexSeoUrlKeys($attributeId = null){
        $attributes = $this->_getAttributes($attributeId);
        $stores = $this->_getAllStoresIds();

        $data = array();
        foreach ($attributes as $attribute) {
            if ($attribute->usesSource()) {
                foreach ($stores as $storeId) {
                    $result = $this->_getInsertValues($attribute, $storeId);
                    $data = array_merge($data, $result);
                }
            }
        }

        if (!empty($attributeId)) {
            $this->_saveData($data, array("`attribute_id` = ?" => $attributeId));
        } else {
            $this->_saveData($data);
        }

        return $this;
    }

    protected function _saveData(array $data, array $deleteWhere = array()){
        if (empty($data)) {
            return $this;
        }
        $this->beginTransaction();

        try {
            $writeAdapter = $this->_getWriteAdapter();
            $writeAdapter->delete($this->getMainTable(), $deleteWhere);
            $writeAdapter->insertMultiple($this->getMainTable(), $data);

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    protected function _getAttributes($attributeId = null){
        $collection = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
            ->getAttributeCollection()
            ->addFieldToFilter('`main_table`.`frontend_input`', array('in' => array('select', 'multiselect')));
        if (!empty($attributeId)) {
            $collection->addFieldToFilter('`main_table`.`attribute_id`', $attributeId);
        }

        return $collection;
    }

    protected function _getInsertValues($attribute, $storeId){
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setStoreFilter($storeId)
            ->setPositionOrder('asc')
            ->setAttributeFilter($attribute->getId())
            ->load();
        $options = $collection->toOptionArray();

        $data = array();
        foreach ($options as $option) {
            $urlKey = $this->_getHelper()->transliterate($option['label']);

            $data[] = array(
                'attribute_code' => $attribute->getAttributeCode(),
                'attribute_id' => $attribute->getId(),
                'store_id' => $storeId,
                'option_id' => $option['value'],
                'url_key' => $urlKey
            );
        }

        return $data;
    }

    protected function _getAllStoresIds(){
        if ($this->_storesIds === null) {
            $this->_storesIds = array();
            $stores = Mage::app()->getStores();
            foreach ($stores as $storeId => $store) {
                $this->_storesIds[] = $storeId;
            }
        }

        return $this->_storesIds;
    }

    protected function _getHelper()
    {
        if ($this->_helper === null) {
            $this->_helper = Mage::helper('sm_shopby');
        }

        return $this->_helper;
    }

    public function catalogEavAttributeSave(Mage_Index_Model_Event $event)
    {
        $attribute = $event->getDataObject();
        $this->reindexSeoUrlKeys($attribute->getId());

        return $this;
    }

}