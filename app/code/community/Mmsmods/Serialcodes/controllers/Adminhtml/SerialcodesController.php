<?php
/*
* ModifyMage Solutions (http://ModifyMage.com)
* Serial Codes - Serial Numbers, Product Codes, PINs, and More
*
* NOTICE OF LICENSE
* This source code is owned by ModifyMage Solutions and distributed for use under the
* provisions, terms, and conditions of our Commercial Software License Agreement which
* is bundled with this package in the file LICENSE.txt. This license is also available
* through the world-wide-web at this URL: http://www.modifymage.com/software-license
* If you do not have a copy of this license and are unable to obtain it through the
* world-wide-web, please email us at license@modifymage.com so we may send you a copy.
*
* @category		Mmsmods
* @package		Mmsmods_Serialcodes
* @author		David Upson
* @copyright	Copyright 2013 by ModifyMage Solutions
* @license		http://www.modifymage.com/software-license
*/

class Mmsmods_Serialcodes_Adminhtml_SerialcodesController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/serialcodes')
            ->_addBreadcrumb(Mage::helper('serialcodes')->__('Manage Serial Codes'),Mage::helper('serialcodes')->__('Manage Serial Codes'));
        return $this;
    }   

    public function indexAction()
	{
		if ($codepool = $this->getRequest()->getParam('codepool')) {
			Mage::getSingleton('admin/session')->setScGridPool($codepool);
		}
		$this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $sc_id = $this->getRequest()->getParam('id');
        $sc_model = Mage::getModel('serialcodes/serialcodes')->load($sc_id);
        if ($sc_model->getId() || $sc_id == 0) {
            Mage::register('serialcodes_data', $sc_model);
            $this->loadLayout();
            $this->_setActiveMenu('catalog/serialcodes');
            $this->_addBreadcrumb(Mage::helper('serialcodes')->__('Manage Serial Codes'), Mage::helper('serialcodes')->__('Manage Serial Codes'));
            $this->_addBreadcrumb(Mage::helper('serialcodes')->__('Edit Code'), Mage::helper('serialcodes')->__('Edit Code'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_edit'))
                 ->_addLeft($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
    public function newAction()
    {
        $this->_forward('edit');
    }
   
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $sc_model = Mage::getModel('serialcodes/serialcodes');
				$pid = $this->getRequest()->getParam('id');
				$sku = $postData['sku'];
				if ($ctime = $sc_model->load($pid)->getCreatedTime()) {
					$ptime = now();
				} else {
					$ctime = now();
					$ptime = null;
				}
				$codes = explode("\n",$this->getRequest()->getParam('code'));
				foreach ($codes as $code) {
					$code = trim($code);
					if ($code <> '') {
						$sc_model->setId($pid)
							->setType(trim($postData['type']))
							->setSku(trim($sku))
							->setCode($code)
							->setStatus($postData['status'])
							->setNote(trim($postData['note']))
							->setCreatedTime($ctime)
							->setUpdateTime($ptime)
							->save();
					}
				}
				$sc_model->updateInventoryStock($sku);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('Successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setSerialcodesData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSerialcodesData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $sc_model = Mage::getModel('serialcodes/serialcodes');
                $sc_model->load($this->getRequest()->getParam('id'));
				$sku = $sc_model->getSku();
				$sc_model->delete();
				$sc_model->updateInventoryStock($sku);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

	public function massDeleteAction()
	{
		$codeIds = $this->getRequest()->getParam('serialcodes_id');
		if(!is_array($codeIds))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Please select records.'));
		} else {
			try {
				$sc_model = Mage::getModel('serialcodes/serialcodes');
				foreach ($codeIds as $codeId) {
					$sc_model->load($codeId);
					$skus[$sc_model->getSku()] = $sc_model->getSku();
					$sc_model->delete();
				}
				foreach($skus as $sku) {
					$sc_model->updateInventoryStock($sku);
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('serialcodes')->__('Total of %d record(s) were deleted.', count($codeIds)));
			} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massStatusAction()
	{
		$codeIds = $this->getRequest()->getParam('serialcodes_id');
		$status = (int)$this->getRequest()->getParam('status');
		$skus = array();
		if(!is_array($codeIds))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Please select records.'));
		} else {
			try
			{
				$sc_model = Mage::getModel('serialcodes/serialcodes');
				foreach ($codeIds as $codeId) {
					$sc_model->load($codeId)->setStatus($status)->setUpdateTime(now())->save();
					$skus[$sc_model->getSku()] = $sc_model->getSku();
				}
				foreach($skus as $sku) {
					$sc_model->updateInventoryStock($sku);
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('serialcodes')->__('Total of %d record(s) were changed.', count($codeIds))
			);
			}	catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
		}
		$this->_redirect('*/*/index');
	}

	public function massTypeAction()
	{
		$codeIds = $this->getRequest()->getParam('serialcodes_id');
		$type = $this->getRequest()->getParam('type');
		if(!is_array($codeIds))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Please select records.'));
		} else {
			try
			{
				$sc_model = Mage::getModel('serialcodes/serialcodes');
				foreach ($codeIds as $codeId) {
					$sc_model->load($codeId)->setType(trim($type))->setUpdateTime(now())->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('serialcodes')->__('Total of %d record(s) were changed.', count($codeIds))
			);
			}	catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
		}
		$this->_redirect('*/*/index');
	}

	public function massSkuAction()
	{
		$codeIds = $this->getRequest()->getParam('serialcodes_id');
		$sku = $this->getRequest()->getParam('sku');
		if(!is_array($codeIds))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Please select records.'));
		} else {
			try
			{
				$sc_model = Mage::getModel('serialcodes/serialcodes');
				foreach ($codeIds as $codeId) {
					$sc_model->load($codeId)->setSku(trim($sku))->setUpdateTime(now())->save();
				}
				$sc_model->updateInventoryStock($sku);
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('serialcodes')->__('Total of %d record(s) were changed.', count($codeIds))
			);
			}	catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
		}
 
		$this->_redirect('*/*/index');
	}

    public function exportCsvAction()
    {
        $filename = 'manage_serialcodes.csv';
        $content = $this->loadLayout()->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_grid')->getCsv();
        $this->_sendUploadResponse($filename, $content);
    }

    public function exportXmlAction()
    {
        $filename = 'manage_serialcodes.xml';
        $content = $this->loadLayout()->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_grid')->getXml();
        $this->_sendUploadResponse($filename, $content);
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
			$this->loadLayout()->getLayout()->createBlock('importedit/adminhtml_serialcodes_grid')->toHtml()
        );
    }
    protected function _sendUploadResponse($filename, $content, $contentType='application/octet-stream')
    {
        $this->getResponse()
			->setHeader('HTTP/1.1 200 OK','')
			->setHeader('Pragma', 'public', true)
			->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
			->setHeader('Content-Disposition', 'attachment; filename='.$filename)
			->setHeader('Last-Modified', date('r'))
			->setHeader('Accept-Ranges', 'bytes')
			->setHeader('Content-Length', strlen($content))
			->setHeader('Content-type', $contentType)
			->setBody($content)
			->sendResponse();
		die;
    }
}