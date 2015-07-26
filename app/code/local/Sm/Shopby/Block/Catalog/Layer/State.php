<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State{

    public function getClearUrl(){
        if (!$this->helper('sm_shopby')->isEnabled()) {
            return parent::getClearUrl();
        }
        
        if ($this->helper('sm_shopby')->isCatalogSearch()) {
            $filterState = array('isLayerAjax' => null);
            foreach ($this->getActiveFilters() as $item) {
                $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
            }
            $params['_current'] = true;
            $params['_use_rewrite'] = true;
            $params['_query'] = $filterState;
            $params['_escape'] = true;
            return Mage::getUrl('*/*/*', $params);
        }

        return $this->helper('sm_shopby')->getClearFiltersUrl();
    }

}
