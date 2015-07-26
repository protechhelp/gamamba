<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute{

    protected $_values = array();

    public function getValues(){
        return $this->_values;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock){
        if (!Mage::helper('sm_shopby')->isEnabled()) {
            return parent::apply($request, $filterBlock);
        }

        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }

        if (empty($filter)) {
            return $this;
        }

        $this->_values = explode(Sm_Shopby_Helper_Data::MULTIPLE_FILTERS_DELIMITER, $filter);

        if (!empty($this->_values)) {
            $attrUrlKeyModel = Mage::getResourceModel('sm_shopby/attribute_urlkey');
            $this->_getResource()->applyFilterToCollection($this, $this->_values);
            foreach ($this->_values as $filter) {
                $optionId = $attrUrlKeyModel->getOptionId($this->getAttributeModel()->getId(), $filter);
                $text = $this->_getOptionText($optionId);
                $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
                if (!Mage::helper('sm_shopby')->isMultipleChoiceFiltersEnabled()) {
                    $this->_items = array();
                }
            }
        }

        return $this;
    }

    protected function _getItemsData(){
        if (!Mage::helper('sm_shopby')->isEnabled()) {
            return parent::_getItemsData();
        }

        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $key = $this->getLayer()->getStateKey() . '_' . $this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $attrUrlKeyModel = Mage::getResourceModel('sm_shopby/attribute_urlkey');
            $options = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);
            $data = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $attrUrlKeyModel->getUrlKey($attribute->getId(), $option['value']),
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    } else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $attrUrlKeyModel->getUrlKey($attribute->getId(), $option['value']),
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        
        return $data;
    }

}