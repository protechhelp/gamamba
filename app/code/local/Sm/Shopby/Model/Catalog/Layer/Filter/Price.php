<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price{

    public function getMaxPriceFloat(){
        if (!$this->hasData('max_price_float')) {
            $this->_collectPriceRange();
        }

        return $this->getData('max_price_float');
    }

    public function getMinPriceFloat(){
        if (!$this->hasData('min_price_float')) {
            $this->_collectPriceRange();
        }

        return $this->getData('min_price_float');
    }

    protected function _collectPriceRange(){
        $collection = $this->getLayer()->getProductCollection();
        $select = $collection->getSelect();
        $conditions = $select->getPart(Zend_Db_Select::WHERE);

        $conditionsNoPrice = array();
        foreach ($conditions as $key => $condition) {
            if (stripos($condition, 'price_index') !== false) {
                continue;
            }
            $conditionsNoPrice[] = $condition;
        }        
        $select->setPart(Zend_Db_Select::WHERE, $conditionsNoPrice);
        
        $this->setData('min_price_float', floor($collection->getMinPrice()));
        $this->setData('max_price_float', round($collection->getMaxPrice()));

        $select->setPart(Zend_Db_Select::WHERE, $conditions);
        
        return $this;
		var_dump($this);
    }

}
