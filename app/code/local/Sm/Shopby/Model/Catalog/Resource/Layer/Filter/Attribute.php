<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Catalog_Resource_Layer_Filter_Attribute extends Mage_Catalog_Model_Resource_Layer_Filter_Attribute{

    public function applyFilterToCollection($filter, $value){
        if (!Mage::helper('sm_shopby')->isEnabled()) {
            return parent::applyFilterToCollection($filter, $value);
        }

        $collection = $filter->getLayer()->getProductCollection();
        $attribute = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = $attribute->getAttributeCode() . '_idx' . uniqid();
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
        );

        $attrUrlKeyModel = Mage::getResourceModel('sm_shopby/attribute_urlkey');
        if (!is_array($value)) {
            foreach ($options as $option) {
                if ($option['label'] == $value) {
                    $value = $option['value'];
                }
            }
            $conditions[] = $connection->quoteInto("{$tableAlias}.value = ?", $value);
        } else {
            $conditions[] = "{$tableAlias}.value in ( ";
            foreach ($value as $v) {
                $v = $attrUrlKeyModel->getOptionId($attribute->getId(), $v);
                $conditions[count($conditions) - 1] .= $connection->quoteInto("?", $v) . ' ,';
            }
            $conditions[count($conditions) - 1] = rtrim($conditions[count($conditions) - 1], ',');
            $conditions[count($conditions) - 1] .= ')';
        }

        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()), implode(' AND ', $conditions), array()
        );
        $collection->getSelect()->distinct();

        return $this;
    }

    public function getCount($filter){
        if (!Mage::helper('sm_shopby')->isEnabled()) {
            return parent::getCount($filter);
        }

        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);


        $connection = $this->_getReadAdapter();
        $attribute = $filter->getAttributeModel();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
        );

        $parts = $select->getPart(Zend_Db_Select::FROM);
        $from = array();
        foreach ($parts as $key => $part) {
            if (stripos($key, $tableAlias) === false) {
                $from[$key] = $part;
            }
        }
        $select->setPart(Zend_Db_Select::FROM, $from);

        $select
            ->join(
                array($tableAlias => $this->getMainTable()), join(' AND ', $conditions), array('value', 'count' => new Zend_Db_Expr("COUNT({$tableAlias}.entity_id)")))
            ->group("{$tableAlias}.value");

        return $connection->fetchPairs($select);
    }

}