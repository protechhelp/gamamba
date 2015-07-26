<?php

class Sm_Cartpro_Model_Mysql4_Cartpro extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cartpro_id refers to the key field in your database table.
        $this->_init('cartpro/cartpro', 'cartpro_id');
    }
}