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

class MageWorx_Adminhtml_Seosuite_Report_CategoryController extends Mage_Adminhtml_Controller_Action {    
    
    public function indexAction() {
        if (!Mage::helper('seosuite')->getCategoryReportStatus()) Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('seosuite')->__('You need to generate the report due to recent changes.'));
        
        $this->_title($this->__('SEO Suite'))->_title($this->__('Categories Report'));        
        $this->loadLayout()
            ->_setActiveMenu('report/seosuit')
            ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
            ->_addBreadcrumb($this->__('Categories Report'), $this->__('Categories Report'))        
            ->renderLayout();
    }
    
    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    public function generateAction() {
		Mage::getSingleton('customer/session')->setReportHash(array());
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function runGenerateAction() {                        
        $action = $this->getRequest()->getParam('action', '');
        $current = intval($this->getRequest()->getParam('current', 0));
        if (!$action) return false;
        
        $result = array();
        // 'start', 'preparation', 'calculation'
        switch ($action) {
            case 'start':
                // truncate report table
                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                $tablePrefix = (string)Mage::getConfig()->getTablePrefix();        
                $connection->truncate($tablePrefix.'seosuite_report_category');
                $action = 'preparation';
            case 'preparation':
                $total = $this->_getTotalCategoryCount();
                $result = array();                        
                $this->_prepareCategories();
                $current = $total;
                $action = 'calculation';
                $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current, round($current*100/$total, 2));
                $result['url'] = $this->getUrl('*/*/runGenerate/', array('action'=>$action, 'current'=>($action=='preparation'?$current:0)));
                break;
            case 'calculation': 
                $limit = 1;
                $stories = $this->_getStores();
                $total = count($stories)+1;
                $result = array();        
                if (($current<$total)) {
                    if (count($stories)>=$total) {
                        $storeId = 0;
                    } else {
                        if(isset($stories[$current])) {
                            $storeId = $stories[$current];
                        }
                    }       
                    $current += $limit;            
                    if ($current>=$total) {
                        $current = $total;
                        $result['stop'] = 1;
                        Mage::helper('seosuite')->setCategoryReportStatus(1);
                        $this->_getSession()->addSuccess(Mage::helper('seosuite')->__('Report has been successfully generated.'));
                        break;
                    }             
                    $this->_calculateCategories($storeId);
                    if ($current<=$limit) {
                        $result['text'] = $this->__('Starting to calculate store\'s category data...');
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total-1, $current, round(($current)*100/($total-1), 2));
                    } else {
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total-1, $current, round(($current)*100/($total-1), 2));
                    }    
                    $result['url'] = $this->getUrl('*/*/runGenerate/', array('action'=>$action, 'current'=>$current));
                }
                break;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    
    protected function _getCategoryCollection($storeId) {
        $collection = Mage::getResourceModel('catalog/category_collection')->addAttributeToSelect(array('name', 'url_path', 'meta_title', 'meta_description'));
        $collection->setStoreId($storeId)->addFieldToFilter('is_active', 1);     
  		return $collection;
    }
    
    
    protected function _getTotalCategoryCount() {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $select = $this->_getCategoryCollection(0)->getSelectCountSql();
        $total = $connection->fetchOne($select);        
        return $total;
    }
	
	protected $_categories = array();
	protected function _getChilds($parentId) {
            $collection = Mage::getModel('catalog/category')->getCategories($parentId);
            foreach ($collection as $category) {
                if(isset($this->_categories[$category->getId()])) continue;
                $this->_categories[$category->getId()] = $category->getId();
                if($category->getChildrenCount()>0) {
                    $this->_getChilds($category->getId());
                }
            }
            return $this;
	}
    
	protected function _prepareCategories() {
            $stores = $this->_getStores();
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	    $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
	    $helper = Mage::helper('seosuite');
	    $connection->beginTransaction();
		foreach ($stores as $storeId) {
                    $parent     = Mage::app()->getStore($storeId)->getRootCategoryId();
                    $this->_getChilds($parent);
                    foreach($this->_categories as $category) {
                        $item = Mage::getModel('catalog/category')->load($category);
                        $connection->insert($tablePrefix.'seosuite_report_category', array(
                            'category_id' => $item->getId(),
                            'store_id' => $storeId,
                            'level' => $item->getLevel(),
                            'url_path' => $item->getUrlPath(),
                            'name' => $item->getName(),
                            'prepared_name' => $helper->_prepareText($item->getName()),
                            'meta_title' => $helper->_trimText($item->getMetaTitle()),
                            'prepared_meta_title' => $helper->_prepareText($item->getMetaTitle())!=='' ? $helper->_prepareText($item->getMetaTitle()) : $item->getMetaTitle() ?$item->getMetaTitle():"",
                            'meta_title_len' => strlen($helper->_trimText($item->getMetaTitle())),
                            'meta_descr_len' => strlen($helper->_trimText($item->getMetaDescription()))                    
                        ));
                    }
                    $this->_categories = array();
		}
		$connection->commit(); 
	}
            
    protected function _getStores() {
        return Mage::getModel('core/store')->getCollection()->load()->getAllIds();
    }
    
    protected function _calculateCategories($storeId) {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        
        $sql = "UPDATE `".$tablePrefix."seosuite_report_category` AS srp,
                    (SELECT `prepared_name`, `store_id`, COUNT(*) AS dupl_count FROM `".$tablePrefix."seosuite_report_category` WHERE `store_id`=".intval($storeId)." AND `prepared_name`!='' GROUP BY `prepared_name`) AS srpr
                    SET srp.`name_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_name`=srpr.`prepared_name` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_name`!='' AND srp.`store_id`=".intval($storeId);        
        $connection->query($sql);
        
        $sql = "UPDATE `".$tablePrefix."seosuite_report_category` AS srp,
                    (SELECT `prepared_meta_title`, `store_id`, COUNT(*) AS dupl_count FROM `".$tablePrefix."seosuite_report_category` WHERE `store_id`=".intval($storeId)." AND `prepared_meta_title`!='' GROUP BY `prepared_meta_title`) AS srpr
                    SET srp.`meta_title_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_meta_title`=srpr.`prepared_meta_title` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_meta_title`!='' AND srp.`store_id`=".intval($storeId);        
        $connection->query($sql);
        
    }
    
 
    public function duplicateViewAction() {
        if (!Mage::helper('seosuite')->getCategoryReportStatus()) Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('seosuite')->__('You need to generate the report due to recent changes.'));
        $this->_title($this->__('SEO Suite'))->_title($this->__('Categories Report'))->_title($this->__('View Duplicates'));
        $this->loadLayout()
            ->_setActiveMenu('report/seosuit')
            ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
            ->_addBreadcrumb($this->__('Categories Report'), $this->__('Categories Report'))
            ->_addBreadcrumb($this->__('View Duplicates'), $this->__('View Duplicates'))
            ->renderLayout();
    }       
    
    public function duplicateViewGridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
}