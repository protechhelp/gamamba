<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_PatchController extends Mage_Adminhtml_Controller_Action
{
    public function instructionAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_title(Mage::helper('aitsys')->__('Aitoc Modules Manager'))
            ->_title(Mage::helper('aitsys')->__('Aitoc Manual Patch Instructions'));
        $this->renderLayout();
    }
    
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_title(Mage::helper('aitsys')->__('Aitoc Modules Manager'))
            ->_title(Mage::helper('aitsys')->__('Customized Templates'));
        $this->renderLayout();
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/aitsys');
    }
}