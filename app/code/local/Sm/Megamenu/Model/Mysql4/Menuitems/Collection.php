<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_Mysql4_Menuitems_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenu/menuitems');
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        // if (
              // Mage::getSingleton('adminhtml/url')->getRequest()->getModuleName() == 'your_module'
              // &amp;&amp; (Mage::getSingleton('adminhtml/url')->getRequest()->getActionName() == 'your_action')
                // ) {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->from('', 'COUNT(DISTINCT main_table.id)');
            $countSelect->resetJoinLeft();
        // }
        // else

        // {
            // $countSelect->columns('COUNT(*)');
        // }
        return $countSelect;
    }	
}