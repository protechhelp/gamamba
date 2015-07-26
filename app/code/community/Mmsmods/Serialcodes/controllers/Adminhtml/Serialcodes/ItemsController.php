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

class Mmsmods_Serialcodes_Adminhtml_Serialcodes_ItemsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales/serialcodes_items')
            ->_addBreadcrumb(Mage::helper('serialcodes')->__('Serial Code Items'),Mage::helper('serialcodes')->__('Serial Code Items'));
        return $this;
    }   

    public function indexAction() {
        $this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items'));
        $this->renderLayout();
    }

    public function editAction() {
        $itemsId = $this->getRequest()->getParam('id');
        $itemsModel  = Mage::getModel('sales/order_item')->load($itemsId);
        if ($itemsModel->getId()) {
            Mage::register('serialcodes_items_data', $itemsModel);
            $this->loadLayout();
            $this->_setActiveMenu('sales/serialcodes_items');
            $this->_addBreadcrumb(Mage::helper('serialcodes')->__('Serial Code Order Items'), Mage::helper('serialcodes')->__('Serial Code Order Items'));
            $this->_addBreadcrumb(Mage::helper('serialcodes')->__('Edit Codes'), Mage::helper('serialcodes')->__('Edit Codes'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items_edit'))
                 ->_addLeft($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

	public function saveAction() {
		if ($this->getRequest()->getPost()) {
			try {
				$source = 'controller';
				$pid = $this->getRequest()->getParam('id');
				$postData = $this->getRequest()->getPost();
				$issued = round($postData['serial_codes_issued']);
				$pcodes = explode("\n",$postData['serial_codes']);
				$pcodes = array_map('trim', $pcodes);
				$pcodes = array_filter($pcodes);
				$item = Mage::getSingleton('sales/order_item')->load($pid);
				$sku = '';
				$icodes = Mage::getSingleton('serialcodes/serialcodes')->bindCodePool($item, $source, $pcodes, $issued, $sku);
				$item
					->setSerialCodeType(trim($postData['serial_code_type']))
					->setSerialCodes(implode("\n",$pcodes))
					->setSerialCodeIds(implode(',',$icodes))
					->setSerialCodesIssued($issued)
					->setSerialCodePool($sku)
					->save();
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

    public function savepopupAction() {
		if ($this->getRequest()->getPost()) {
			try {
				$source = 'controller';
				$pid = $this->getRequest()->getParam('id');
				$postData = $this->getRequest()->getPost();
				$issued = round($postData['sc_issued_'.$pid]);
				$pcodes = explode("\n",$postData['serial_codes_'.$pid]);
				$pcodes = array_map('trim', $pcodes);
				$pcodes = array_filter($pcodes);
				$item = Mage::getSingleton('sales/order_item')->load($pid);
				$sku = '';
				$icodes = Mage::getSingleton('serialcodes/serialcodes')->bindCodePool($item, $source, $pcodes, $issued, $sku);
				$item
					->setSerialCodeType(trim($postData['sc_type_'.$pid]))
					->setSerialCodes(implode("\n",$pcodes))
					->setSerialCodeIds(implode(',',$icodes))
					->setSerialCodesIssued($issued)
					->setSerialCodePool($sku)
					->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('Successfully saved.'));
				Mage::getSingleton('adminhtml/session')->setSerialcodesData(false);
				$this->_redirect('adminhtml/sales_order/view', array('order_id' => $item->getOrderId()));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setSerialcodesData($this->getRequest()->getPost());
				$this->_redirect('adminhtml/sales_order/view', array('order_id' => $item->getOrderId()));
				return;
			}
		}
    }

	public function issueAction() {
		$pid = $this->getRequest()->getParam('id');
		$order = Mage::getSingleton('sales/order')->load($pid);
		$source = 'controller';
		$itemids = explode(',',$this->getRequest()->getPost('sc_items'));
		$items = Mage::getSingleton('sales/order_item')->getCollection()->addFieldToFilter('item_id', array('in' => $itemids));
		Mage::getSingleton('serialcodes/serialcodes')->issueSerialCodes($order, $source, $items);
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $pid));
	}

	public function emailAction() {
		$pid = $this->getRequest()->getParam('id');
		$order = Mage::getSingleton('sales/order')->load($pid);
		$source = 'controller';
		$itemids = explode(',',$this->getRequest()->getPost('sc_items'));
		$items = Mage::getSingleton('sales/order_item')->getCollection()->addFieldToFilter('item_id', array('in' => $itemids));
		Mage::getSingleton('serialcodes/serialcodes')->sendDeliveryEmail($order, $source, $items);
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $pid));
	}

    public function exportCsvAction()
    {
		$filename = 'serialcodes_order_items.csv';
		$content = $this->loadLayout()->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items_grid')->getCsv();
		$content = str_replace('<br />', "\n", $content);
		$content = str_replace('&nbsp;', ' ', $content);
		$content = strip_tags($content);
        $this->_sendUploadResponse($filename, $content);
    }

    public function exportXmlAction()
    {
        $filename = 'serialcodes_order_items.xml';
		$content = (string)$this->loadLayout()->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items_grid')->getXml();
		$content = str_replace('<items>', "\n".'<items>'."\n", $content);
		$content = str_replace('increment_id', 'order', $content);
		$content = str_replace('status', 'order_status', $content);
		$content = str_replace('name', 'product_name', $content);
		$content = str_replace('fullproduct', 'customer', $content);
		$tagname = 'item';
		preg_match_all("/<$tagname>(.*?)<\/$tagname>/s", $content, $items);
		foreach($items[1] as $item) {
			$tagname = 'serial_code_pool';
			preg_match("/<$tagname>(.*?)<\/$tagname>/s", $item, $codepool);
			$pool = str_replace('<serial_code_pool><![CDATA[', '', $codepool[0]);
			$pool = str_replace(']]></serial_code_pool>', '', $pool);
			$tagname = 'serial_codes';
			preg_match("/<$tagname>(.*?)<\/$tagname>/s", $item, $serialcodes);
			$temp = str_replace('<![CDATA[', '<code><value><![CDATA[', $serialcodes[0]);
			$temp = str_replace(']]>', ']]></value><status><![CDATA[]]></status></code>', $temp);
			$temp = str_replace("\n", ']]></value><status><![CDATA[]]></status></code><code><value><![CDATA[', $temp);
			$temp = str_replace('<code>', "\n".'<code>', $temp);
			$temp = str_replace('</code></serial_codes>', '</code>'."\n".'</serial_codes>', $temp);
			$tagname = 'code';
			preg_match_all("/<$tagname>(.*?)<\/$tagname>/s", $temp, $codes);
			foreach($codes[1] as $code) {
				$tagname = 'value';
				preg_match("/<$tagname>(.*?)<\/$tagname>/s", $code, $codevalue);
				$value = str_replace('<value><![CDATA[', '', $codevalue[0]);
				$value = str_replace(']]></value>', '', $value);
				$collection = Mage::getModel('serialcodes/serialcodes')->getCollection()
					->addFieldToFilter('sku',array('like' => $pool))
					->addFieldToFilter('code',array('like' => $value));
				If ($collection->getSize()) {
					$status = $collection->getFirstItem()->getStatus();
						switch ($status) {
							case 0:
								$statlabel = 'Available';
								break;
							case 1:
								$statlabel = 'Used';
								break;
							case 2:
								$statlabel = 'Pending';
						}
					$codetemp = str_replace('<status><![CDATA[]]></status>', '<status><![CDATA['.$statlabel.']]></status>', $code);
					$pos = strpos($temp,$code);
					$temp = substr_replace($temp,$codetemp,$pos,strlen($code));
				}
			}
			$pos = strpos($item,$serialcodes[0]);
			$itemtemp = substr_replace($item,$temp,$pos,strlen($serialcodes[0]));
			$pos = strpos($content,$item);
			$content = substr_replace($content,$itemtemp,$pos,strlen($item));
		}
        $this->_sendUploadResponse($filename, $content);
    }

	public function gridAction() {
		$this->getResponse()->setBody(
			$this->loadLayout()->getLayout()->createBlock('importedit/adminhtml_serialcodes_items_grid')->toHtml()
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