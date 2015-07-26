<?php

class Aitoc_Aitsys_Model_System_Config_Source_Interest
{
    const PROMO                         = 'PROMO';
    const EXTENSION_UPDATE_CUSTOMER     = 'EXTENSION_UPDATE_CUSTOMER';
    const EXTENSION_UPDATE              = 'EXTENSION_UPDATE';
    const NEW_EXTENSION                 = 'NEW_EXTENSION';
    const NEWS                          = 'NEWS';


    public function toOptionArray()
    {
        $helper = Mage::helper('aitsys');
        return array(
            array('value' => self::PROMO,                       'label' => $helper->__('Promotions/Discounts')),
            array('value' => self::EXTENSION_UPDATE_CUSTOMER,   'label' => $helper->__('My extensions updates')),
            array('value' => self::EXTENSION_UPDATE,            'label' => $helper->__('All extensions updates')),
            array('value' => self::NEW_EXTENSION,               'label' => $helper->__('New Extension')),
            array('value' => self::NEWS,                        'label' => $helper->__('Other information'))
        );
    }

    public function toArray()
    {
        return array(
            self::PROMO                     => 'Promotion/Discount',
            self::EXTENSION_UPDATE_CUSTOMER => 'My extensions updates',
            self::EXTENSION_UPDATE          => 'All extensions updates',
            self::NEW_EXTENSION             => 'New Extension',
            self::NEWS                      => 'Other information',
        );
    }
}
