<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute {

    public function __construct(){
        parent::__construct();

        if ($this->helper('sm_shopby')->isEnabled() && $this->helper('sm_shopby')->isMultipleChoiceFiltersEnabled()){
			$this->setTemplate('sm/shopby/catalog/layer/filter.phtml');
        }
    }

}