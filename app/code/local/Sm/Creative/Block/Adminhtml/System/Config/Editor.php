<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Block_Adminhtml_System_Config_Editor 
	extends Mage_Adminhtml_Block_System_Config_Form_Field 
    implements Varien_Data_Form_Element_Renderer_Interface {
	
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $element->setWysiwyg(true);
        $element->setConfig(Mage::getSingleton('cms/wysiwyg_config')->getConfig());
        return parent::_getElementHtml($element);
    }	
}
