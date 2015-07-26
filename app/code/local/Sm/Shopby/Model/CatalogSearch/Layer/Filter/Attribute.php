<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_CatalogSearch_Layer_Filter_Attribute extends Sm_Shopby_Model_Catalog_Layer_Filter_Attribute{

    protected function _getIsFilterableAttribute($attribute)
    {
        return $attribute->getIsFilterableInSearch();
    }

}