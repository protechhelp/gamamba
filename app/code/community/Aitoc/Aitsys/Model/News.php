<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_News extends Aitoc_Aitsys_Abstract_Model
{
    protected function _construct()
    {
        $this->_init('aitsys/news');
    }
    
    /**
     * @return bool
     */
    public function isOld()
    {
        return strtotime($this->getDateAdded()) < time()-86400;
    }
}