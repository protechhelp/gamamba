<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Catalog_Layer extends Mage_Catalog_Model_Layer{

    public function getFilterableAttributes(){
        $collection = parent::getFilterableAttributes();

        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Attribute_Collection) {
            $attrUrlKeyModel = Mage::getResourceModel('sm_shopby/attribute_urlkey');
            $attrUrlKeyModel->preloadAttributesOptions($collection);
        }

        return $collection;
    }

}