<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Seosuite_TemplateController extends Mage_Adminhtml_Controller_Action {
    
    private $_currentModel;
    private $_currentModelCode;
    
    protected function _getTotalCount($entity = 'product') {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        $select = $connection->select()->from($tablePrefix.'catalog_'.$entity.'_entity', 'COUNT(*)');
        $total = $connection->fetchOne($select);        
        return intval($total);
    }
    
    public function indexAction() {
        
        $this->_title($this->__('SEO Suite'))->_title($this->__('Manage SEO Templates'));        
        $this->loadLayout()
            ->_setActiveMenu('catalog/seo_templates')
            ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
            ->_addBreadcrumb($this->__('Templates'), $this->__('Templates'))        
            ->renderLayout();
    }
    
    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    public function editAction() {
        
        $templateId = $this->getRequest()->getParam('template_id');
        $storeId = $this->getRequest()->getParam('store',0);
        if (!$templateId)
        {
           return $this->_forward('grid');
        }
        $template = Mage::getModel('seosuite/template')->load($templateId);
        Mage::register('seosuite_template_edit', $template, true);
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function changeStatusAction() {
        $params = $this->getRequest()->getParams();
        $templateModel = Mage::getModel('seosuite/template')->load($params['template_id']);
        $status = 1;
        $statusLabel = Mage::helper('seosuite')->__('Enabled');
        if($templateModel->getStatus()) {
            $status = 0;
            $statusLabel = Mage::helper('seosuite')->__('Disabled');
        
        }
        try {
            $templateModel->setStatus($status)->save();
        } catch (Exception $e){
             Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('catalog')->__('%s',$e->getMessage()));
                $this->_redirect('*/*/');
                return;
        }
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Template '%s' %s",$templateModel->getTemplateName(),$statusLabel));
        return $this->_redirect('*/*/index');
    }


    public function saveAction() {
        $params = $this->getRequest()->getParams();
        $templateModel = Mage::getModel('seosuite/template')->load($params['template_id']);
        try {
            $templateModel->setLastUpdate(time())->setStatus($params['status'])->setStoreData($params)->save();
        } catch (Exception $e){
             Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('catalog')->__('%s',$e->getMessage()));
                $this->_redirect('*/*/');
                return;
        }
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Template successfully saved'));
//        return $this->_redirect('*/*/edit',array('store'=>$params['store_template']['store_id'],'template_id'=>$params['template_id']));
        return $this->_redirect('*/*/index');
    }
   
    public function applyAction() {
        $this->loadLayout();
        $templateId = $this->getRequest()->getParam('template_id');
        $model = Mage::getModel('seosuite/template')->load($templateId);
        if(!$model->isEnable()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Template is disabled.'));
            return $this->_redirect('*/*/index');
        }
        
        $storeModelCollection = Mage::getModel('seosuite/template_store')->getCollection()->filterTemplateId($templateId);
        if(!$storeModelCollection->getSize()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Template key is empty'));
            return $this->_redirect('*/*/index');
        }
        $canApply = false;
        
        foreach ($storeModelCollection as $item) {
            if(Mage::app()->getStore($item->getStoreId())->isAdmin()) {
                $canApply = true;
                continue;
            }
//            if ($this->getRequest()->getParam('store') == $item->getStoreId()) {
//                $canApply = true;
//                continue;
//            }
        }
        
        if(!$canApply) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Default Template key is empty. You can\'t continue.'));
            return $this->_redirect('*/*/index'); 
        }
        
        Mage::register('seosuite_template_current_model_id', $model->getId(),true);
        Mage::register('seosuite_template_current_model_name',$model->getTemplateName(),true);
        $this->renderLayout();
    }
    
    public function runApplyAction() {
//        @ini_set('max_execution_time', 1800);
//        @ini_set('memory_limit', 734003200);
        $storeId = $this->getRequest()->getParam('store',0);
        $model = Mage::getModel('seosuite/template')->load($this->getRequest()->getParam('model_id'));
        $templateCodeArray = explode('_',$model->getTemplateCode());
        $currentCode = array_shift($templateCodeArray)."_".join('',$templateCodeArray);
        Mage::register('seosuite_template_current_model_code', $currentCode,true);
        Mage::register('seosuite_template_current_model', $model,true);
       
        $aIndex = array(1,3,4,5,6,7,10);
        //$aIndex = array(1,2,3,4,5,6,7,8,9,10); # full list of index
        
        $limit = Mage::getStoreConfig('mageworx_seo/seosuite/template_limit',$storeId);        
        $current = intval($this->getRequest()->getParam('current', array_shift($aIndex)));
        $reindex = $this->getRequest()->getParam('reindex', '');        
        $result = array();
        
        if ($reindex) {
          //  exit;
            // make reindex
           
            if ($reindex=='start') {
                    $result['url'] = $this->getUrl('*/*/runApply/', array('reindex'=>'run'));                    
                    $result['text'] = $this->__('Starting reindex product data...'); 
            } elseif ($reindex=='run') {       
	     		$_index = Mage::getModel('index/process')->load($current);
                $current = $_index->getId() +1;
                while(!in_array($current,$aIndex) && $current<10)
                {
                    $current++;
                }
                
                if(!$_index->getId())
                {
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Template was successfully applied.'));
                    die($this->getUrl('*/*/index/',array('_secure'=>true)));
                } else {
					try {
                    $_index->reindexAll();
					$result['text'] = $_index->getIndexer()->getDescription().$this->__('... 100%. Done.');
					} catch(Mage_Core_Exception $e) {
						$result['text'] = $e->getMessage();
					}
                    
                    
                    $result['url'] = $this->getUrl('*/*/runApply/', array('reindex'=>'run', 'current'=>$current,'model_id'=>$model->getId()));
                }
            }
            
        } else {
            // applyUrl
            $entity = "product";
            if(strpos($model->getTemplateCode(),'category_')!== false)
            {
                $entity = 'category';
            }
            $total = $this->_getTotalCount($entity);
          //  $result['test'] = "$total += $current += $limit"; 
            if ($current<$total) {
               
                Mage::getSingleton('seosuite/template_adapter_'.$currentCode)->setModel($model)->apply($current, $limit);
                $current += $limit;            
                if ($current>=$total) {
                    $current = $total;                    
                    $result['url'] = $this->getUrl('*/*/runApply/', array('reindex'=>'start','model_id'=>$model->getId()));
                } else {
                    $result['url'] = $this->getUrl('*/*/runApply/', array('current'=>$current,'model_id'=>$model->getId()));
                }
                $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current, round($current*100/$total, 2));

            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
}