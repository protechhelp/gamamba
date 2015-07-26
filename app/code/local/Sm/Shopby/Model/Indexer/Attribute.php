<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Shopby_Model_Indexer_Attribute extends Mage_Index_Model_Indexer_Abstract{

    protected $_matchedEntities = array(
        Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
        ),
    );

    protected function _construct(){
        $this->_init('sm_shopby/indexer_attribute');
    }

    protected function _processEvent(Mage_Index_Model_Event $event){
        $this->callEventHandler($event);
    }

    protected function _registerEvent(Mage_Index_Model_Event $event){
        return $this;
    }

    public function getName(){
        return Mage::helper('sm_shopby')->__('Sm Shopby');
    }

    public function getDescription(){
        return Mage::helper('sm_shopby')->__('Index attribute options for layered navigation filters');
    }

}