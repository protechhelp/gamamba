<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price{

    public function __construct(){
        parent::__construct();
        if ($this->helper('sm_shopby')->isEnabled()
            && $this->helper('sm_shopby')->isPriceSliderEnabled()) {
			$this->setTemplate('sm/shopby/catalog/layer/price.phtml');
        }
    }

    public function getMaxPriceFloat(){
        return $this->_filter->getMaxPriceFloat();
    }

    public function getMinPriceFloat(){
        return $this->_filter->getMinPriceFloat();
    }

    public function getCurrentMinPriceFilter(){
        list($from, $to) = $this->_filter->getInterval();
        $from = floor((float) $from);

        if ($from < $this->getMinPriceFloat()) {
            return $this->getMinPriceFloat();
        }
        return $from;
    }

    public function getCurrentMaxPriceFilter() {
        list($from, $to) = $this->_filter->getInterval();
        $to = floor((float) $to);

        if ($to == 0 || $to > $this->getMaxPriceFloat()) {
            return $this->getMaxPriceFloat();
        }
		
        return $to;
    }

    public function getUrlPattern(){
        $item = Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this->_filter)
            ->setValue('__PRICE_VALUE__')
            ->setCount(0);

        return $item->getUrl();
    }

    public function isSubmitTypeButton(){
        $type = $this->helper('sm_shopby')->getPriceSliderSubmitType();

        if ($type == Sm_Shopby_Model_System_Config_Source_Slider_Submit_Type::SUBMIT_BUTTON) {
            return true;
        }

        return false;
    }

    public function getItemsCount(){
        if ($this->helper('sm_shopby')->isEnabled()
            && $this->helper('sm_shopby')->isPriceSliderEnabled()) {
            return 1;
        }

        return parent::getItemsCount();
    }

}
