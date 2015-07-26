<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Block_Catalog_Product_List_Pager extends Mage_Page_Block_Html_Pager{

    public function getPagerUrl($params = array()){
        if (!Mage::helper('sm_shopby')->isEnabled()) {
            return parent::getPagerUrl($params);
        }

        if ($this->helper('sm_shopby')->isCatalogSearch()) {
            $params['isLayerAjax'] = null;
            return parent::getPagerUrl($params);
        }

        return $this->helper('sm_shopby')->getPagerUrl($params);
    }

}