<?php

class Sm_Cartpro_Model_Mysql4_Cartpro_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cartpro/cartpro');
    }
}