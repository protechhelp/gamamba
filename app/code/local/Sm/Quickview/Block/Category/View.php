<?php
/*------------------------------------------------------------------------
 # SM QuickView - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Quickview_Block_Category_View extends Mage_Catalog_Block_Category_View {
    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }
}