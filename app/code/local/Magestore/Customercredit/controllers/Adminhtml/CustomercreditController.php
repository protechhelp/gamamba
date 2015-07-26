<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customercredit Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Adminhtml_CustomercreditController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Customercredit_Adminhtml_CustomercreditController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customercredit/customercredit')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }
    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'customercredit.csv';
        $content    = $this->getLayout()
                           ->createBlock('customercredit/adminhtml_customercredit_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'customercredit.xml';
        $content    = $this->getLayout()
                           ->createBlock('customercredit/adminhtml_customercredit_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customercredit');
    }
}