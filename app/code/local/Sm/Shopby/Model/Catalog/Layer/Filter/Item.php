<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item{

    protected $_helper;

    protected function _helper(){
        if ($this->_helper === null) {
            $this->_helper = Mage::helper('sm_shopby');
        }
        return $this->_helper;
    }

    public function getUrl(){
        if (!$this->_helper()->isEnabled()) {
            return parent::getUrl();
        }

        //$values = $this->getFilter()->getValues();
//        if (!empty($values)) {
//            $tmp = array_merge($values, array($this->getValue()));
//            asort($tmp);
//            $values = implode(Sm_Shopby_Helper_Data::MULTIPLE_FILTERS_DELIMITER, $tmp);
//        } else {
            $values = $this->getValue();
        //}

        if ($this->_helper()->isCatalogSearch()) {
            $query = array(
                'isLayerAjax' => null,
                $this->getFilter()->getRequestVar() => $values,
                Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null 
            );
            return Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true, '_query' => $query));
        }

        return $this->_helper()->getFilterUrl(array(
            $this->getFilter()->getRequestVar() => $values
        ));
    }

    public function getRemoveUrl(){
        if (!$this->_helper()->isEnabled()) {
            return parent::getRemoveUrl();
        }

        $values = $this->getFilter()->getValues();
        if (!empty($values)) {
            $tmp = array_diff($values, array($this->getValue()));
            if (!empty($tmp)) {
                $values = implode(Sm_Shopby_Helper_Data::MULTIPLE_FILTERS_DELIMITER, $tmp);
            } else {
                $values = null;
            }
        } else {
            $values = null;
        }
        if ($this->_helper()->isCatalogSearch()) {
            $query = array(
                'isLayerAjax' => null,
                $this->getFilter()->getRequestVar() => $values
            );
            $params['_current'] = true;
            $params['_use_rewrite'] = true;
            $params['_query'] = $query;
            $params['_escape'] = true;
            return Mage::getUrl('*/*/*', $params);
        }

        return $this->_helper()->getFilterUrl(array(
            $this->getFilter()->getRequestVar() => $values
        ));
    }

    public function isSelected(){
        $values = $this->getFilter()->getValues();
        if (in_array($this->getValue(), $values)) {
            return true;
        }
        return false;
    }

}
