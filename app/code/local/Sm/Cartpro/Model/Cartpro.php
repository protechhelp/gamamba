<?php

class  Sm_Cartpro_Model_Cartpro extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('cartpro/cartpro');
    }

}