<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Mysql4_News_Collection extends Aitoc_Aitsys_Abstract_Mysql4_Collection
{
    protected function _construct()
    {
        $this->_init('aitsys/news');
    }
    
    /**
     * @param string $type [news|important]
     * 
     * @return Aitoc_Aitsys_Model_Mysql4_News_Collection
     */
    public function addTypeFilter( $type )
    {
        return $this->addFieldToFilter('type', array('=' => $type));
    }
}