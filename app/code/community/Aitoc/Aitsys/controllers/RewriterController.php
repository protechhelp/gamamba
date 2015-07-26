<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_RewriterController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        $result = parent::preDispatch();

        if (true === $this->_getSession()->getData('aitsys_rewriter_require_rebuild')) {
            $this->_getSession()->unsetData('aitsys_rewriter_require_rebuild');
            Mage::getModel('aitsys/rewriter')->prepare();
        }

        return $result;
    }
    
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_title(Mage::helper('aitsys')->__('Aitoc Modules Manager'))
            ->_title(Mage::helper('aitsys')->__('Rewrites Manager'));
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        $currentExtension = Mage::app()->getRequest()->getParam('extension');
        
        $classOrder = Mage::app()->getRequest()->getPost('rewrites');
        $excludeClasses = Mage::app()->getRequest()->getPost('exclude_classes');
        if (!$classOrder) {
            $classOrder = array();
        }
        
        $failed = false;
        foreach ($classOrder as $baseClass => $rewriteClasses) {
            $usedOrders = array();
            foreach ($rewriteClasses as $class => $order) {
                if (!is_numeric($order)) {
                    $failed = true;
                    $this->_getSession()->addError(Mage::helper('aitsys')->__('Please make sure the numeric values only are entered.'));
                    break(2);
                }
                if (in_array($order, $usedOrders)) {
                    $failed = true;
                    $this->_getSession()->addError(Mage::helper('aitsys')->__('Please make sure the order numbers are not duplicated.'));
                    break(2);
                }
                $usedOrders[] = $order;
            }
        }
        
        if (!$failed) {
            if (!$currentExtension) {
                Mage::helper('aitsys/rewriter')->saveOrderConfig($classOrder);
            } else  {
                Mage::helper('aitsys/rewriter')->mergeOrderConfig($classOrder);
            }

            /* Save excluded base classes */
            Mage::helper('aitsys/rewriter')->saveExcludeClassesConfig($excludeClasses);
            $this->_getSession()->setData('aitsys_rewriter_require_rebuild', true);
            $this->_getSession()->addSuccess(Mage::helper('aitsys')->__('Rewrites changes saved successfully.'));
        }
        $this->_redirect('*/*', array('_current'=>true));
    }
    
    public function resetAction()
    {
        Mage::helper('aitsys/rewriter')->removeOrderConfig();
        $this->_getSession()->setData('aitsys_rewriter_require_rebuild', true);
        $this->_getSession()->addSuccess(Mage::helper('aitsys')->__('Rewrites order resetted successfully.'));
        $this->_redirect('*/*');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/aitsys');
    }


    public function disableAction()
    {
        Mage::getModel('core/config')->saveConfig('aitsys/rewriter_status', 0 );
        Mage::app()->cleanCache();
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('compiler')->__('Rewriter is disabled.')
        );
        $this->_redirect('*/*/');
    }

    public function enableAction()
    {
        Mage::getModel('core/config')->saveConfig('aitsys/rewriter_status', 1 );
        Mage::app()->cleanCache();
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('compiler')->__('Rewriter is enabled.')
        );
        //flash rewriter cache
        $rewriter = new Aitoc_Aitsys_Model_Rewriter();
        $rewriter->prepare();

        $this->_redirect('*/*/');
    }

}