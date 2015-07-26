<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_System_Config_Backend_Seo_Catalog extends Mage_Core_Model_Config_Data{

    protected function _afterSave(){
        if ($this->isValueChanged()) {
            $instance = Mage::app()->getCacheInstance();
            $instance->invalidateType('block_html');
        }

        return $this;
    }

}
