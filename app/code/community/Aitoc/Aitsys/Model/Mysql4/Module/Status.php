<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc.
 */
class Aitoc_Aitsys_Model_Mysql4_Module_Status extends Aitoc_Aitsys_Abstract_Mysql4
{
    protected function _construct()
    {
        $this->_init('aitsys/status', 'id');
    }
}