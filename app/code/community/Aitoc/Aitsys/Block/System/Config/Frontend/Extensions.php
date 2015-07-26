<?php

class Aitoc_Aitsys_Block_System_Config_Frontend_Extensions
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<h3 style="margin:15px 0;">'.Mage::helper('aitsys')->__('To Enabe/Disable AITOC extensions click here').' <a href="'.Mage::helper("adminhtml")->getUrl("aitsys/index/index").'" >'.Mage::helper('aitsys')->__('Manage Aitoc Modules').'</a></h3>';

        return $html;
    }
}