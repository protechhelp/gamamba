<?php

class MageWorx_SeoSuite_Model_Mysql4_Template_Store extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
            $this->_init('seosuite/template_store', 'entity_id');
    }
}
