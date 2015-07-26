<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function errorAction()
    {
        $this->loadLayout()->_setActiveMenu('system/aitsys');
        $this->renderLayout();
    }
    
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_title(Mage::helper('aitsys')->__('Aitoc Modules Manager'));
        $this->renderLayout();
    }

    public function saveAction() {
        
        if ($data = $this->getRequest()->getPost('enable')) {
            if ($aErrorList = Mage::getModel('aitsys/aitsys')->saveData($data)) {
                $aModuleList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();
                
                foreach ($aErrorList as $aError) {
                    $this->_getSession()->addError($aError);
                }
                
                if ($notices = Mage::getModel('aitsys/aitpatch')->getCompatiblityError($aModuleList)) {
                    foreach ($notices as $notice) {
                        $this->_getSession()->addNotice($notice);
                    }
                }
            } else {
                $this->_getSession()->addSuccess(Mage::helper('aitsys')->__('Modules\' settings saved successfully'));
            }
        }
        
        $this->_redirect('*/*');
    }
    
    public function permissionsAction()
    {
        $mode = Mage::app()->getRequest()->getParam('mode');
        
        try {
            Aitoc_Aitsys_Abstract_Service::get()->filesystem()->permissonsChange($mode);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitsys')->__('Write permissions were changed successfully'));
            // Aitoc_Aitsys_Abstract_Service::get()->getCache()->remove('aitsys_db_config'); // removed from 2.20
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitsys')->__('There was an error while changing write permissions. Permissions were not changed.'));        
        }
        
        $this->_redirect('*/index');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/aitsys');
    }
}