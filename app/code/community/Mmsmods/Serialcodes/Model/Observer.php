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

class Mmsmods_Serialcodes_Model_Observer extends Mage_Core_Controller_Varien_Action
{
    public function __construct()
    {
    }

	public function addInvoiceCodesToOrder($observer)
	{
		$invoice = $observer->getEvent()->getInvoice();
		$paid = $invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID;
		$order = $invoice->getOrder();
		$source = 'invoicing';
		$sc_model = Mage::getSingleton('serialcodes/serialcodes');
		$sc_model->issueSerialCodes($order, $source, NULL, $paid);
		if (Mage::app()->getStore()->isAdmin()) {
			$sc_model->sendDeliveryEmail($order);
		} else {
			$session = Mage::getSingleton('checkout/session');
			$sent = $session->getScEmailSentId();
			$orderid = $order->getId();
			if ($sent != $orderid) {
				$sc_model->sendDeliveryEmail($order);
				$session->setScEmailSentId($orderid);
			}
		}
		return $this;
	}

	public function addCheckoutCodesToOrder($observer)
	{
		$session = Mage::getSingleton('checkout/session');
		$order = Mage::getSingleton('sales/order')->load($session->getLastOrderId());
		if ($order->getData()) {
			$source = 'checkout';
			$sc_model = Mage::getSingleton('serialcodes/serialcodes');
			$sc_model->issueSerialCodes($order, $source);
			$sent = $session->getScEmailSentId();
			$orderid = $order->getId();
			if ($sent != $orderid) {
				$sc_model->sendDeliveryEmail($order);
				$session->setScEmailSentId($orderid);
			}
			$this->_initLayoutMessages('checkout/session');
		}
		return $this;
	}

	public function addPendingCodesToOrder($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$source = 'pending';
		Mage::getSingleton('serialcodes/serialcodes')->issueSerialCodes($order, $source);
		return $this;
	}

	public function updateInventory($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$storeid = $order->getStoreId();
		$items = $order->getAllItems();
		$sc_model = Mage::getSingleton('serialcodes/serialcodes');
		if ($order->getStatus() == 'canceled') {
			foreach ($items as $item) {
				$sku = '';
				$codes = explode("\n",$item->getSerialCodes());
				$count = count($codes);
				if ($codes[0]) {
					$codeids = array_pad(explode(',',$item->getSerialCodeIds()),$count,'');
					for ($i=0; $i<$count; $i++) {
						if (is_numeric($codeids[$i]) && $codeids[$i] > -1 && $code = $sc_model->load($codeids[$i])) {
							if ($code->getStatus() == 2) {
								$codes[$i] = '';
								$codeids[$i] = '';
								$item->setSerialCodesIssued($item->getSerialCodesIssued() - 1);
								if (!$item->getSerialCodesIssued()) {
									$item->setSerialCodeType(NULL);
								}
								$item->setSerialCodes(implode("\n",array_filter($codes)));
								$item->setSerialCodeIds(implode(',',array_filter($codeids)));
								$item->save();
								$code->setStatus(0)->save();
							}
						}
					}
				}
				$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
				if (!$sku = trim($product->getSerialCodePool())) {$sku = trim($product->getSku());}
				$code = $sc_model->getCollection()->addFieldToFilter('sku', array('like' => $sku))->load();
				if ($code->getData() == null) {$sku = trim($item->getSku());}
				$sc_model->sendWarningLevelEmail($product, $order, $sku);
			}
		}
		foreach ($items as $item) {
			$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
			if (!$sku = trim($product->getSerialCodePool())) {$sku = trim($product->getSku());}
			$sc_model->updateInventoryStock($sku);
		}
		return $this;
	}

	public function updateProductInventory($observer)
	{
		$product = $observer->getProduct();
		if (!$sku = trim($product->getSerialCodePool())) {$sku = trim($product->getSku());}
		Mage::getSingleton('serialcodes/serialcodes')->updateInventoryStock($sku);
		return $this;
	}
	
	public function disableSerialCodeAttributes($observer)
	{
		if(Mage::app()->getRequest()->getActionName() == 'edit' || Mage::app()->getRequest()->getParam('type'))
		{
			$attributes = $observer->getEvent()->getProduct()->getAttributes();
			foreach($attributes as $attribute)
			{
				if(strpos($attribute->getAttributeCode(), 'serial_code') !== FALSE)
				{
					if(Mage::getSingleton('admin/session')->isAllowed('catalog/serialcodes_attributes'))
					{
						$attribute->setIsVisible(1);
					} else {
						$attribute->setIsVisible(0);
					}
				}
			}
		}
		return $this;
	}

	public function addProductFieldDependence($observer)
	{
		$block = $observer->getBlock();
		if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit) {
			$layout = $block->getLayout();
			if (!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && version_compare(Mage::getVersion(),'1.4.1.1') < 0) {
				$newblock = $layout->createBlock('core/text');
				$newblock->setText("
<script type=\"text/javascript\">
	function getAncestor(elem, tag) {
		elem = elem.parentNode;
		while (elem && elem.tagName.toLowerCase() != tag) {
			elem = elem.parentNode;
		}
		return elem;
	}
	function addProductFieldDependence() {
		if ($('serial_code_use_customer').value == 1) {
			getAncestor($('serial_code_customer_groups'),'tr').show();
		} else {
			getAncestor($('serial_code_customer_groups'),'tr').hide();
		}
		if ($('serial_code_send_email').value == 1) {
			getAncestor($('serial_code_use_voucher'),'tr').show();
			getAncestor($('serial_code_email_template'),'tr').show();
			getAncestor($('serial_code_email_type'),'tr').show();
			getAncestor($('serial_code_send_copy'),'tr').show();
		} else {
			getAncestor($('serial_code_use_voucher'),'tr').hide();
			getAncestor($('serial_code_email_template'),'tr').hide();
			getAncestor($('serial_code_email_type'),'tr').hide();
			getAncestor($('serial_code_send_copy'),'tr').hide();
		}
		if ($('serial_code_low_warning').value == 1) {
			getAncestor($('serial_code_warning_template'),'tr').show();
			getAncestor($('serial_code_warning_level'),'tr').show();
			getAncestor($('serial_code_send_warning'),'tr').show();
		} else {
			getAncestor($('serial_code_warning_template'),'tr').hide();
			getAncestor($('serial_code_warning_level'),'tr').hide();
			getAncestor($('serial_code_send_warning'),'tr').hide();
		}
		Event.observe('serial_code_use_customer', 'change', function(){
			if ($('serial_code_use_customer').value == 1) {
				getAncestor($('serial_code_customer_groups'),'tr').show();
			} else {
				getAncestor($('serial_code_customer_groups'),'tr').hide();
			}
		});
		Event.observe('serial_code_send_email', 'change', function(){
			if ($('serial_code_send_email').value == 1) {
				getAncestor($('serial_code_use_voucher'),'tr').show();
				getAncestor($('serial_code_email_template'),'tr').show();
				getAncestor($('serial_code_email_type'),'tr').show();
				getAncestor($('serial_code_send_copy'),'tr').show();
			} else {
				getAncestor($('serial_code_use_voucher'),'tr').hide();
				getAncestor($('serial_code_email_template'),'tr').hide();
				getAncestor($('serial_code_email_type'),'tr').hide();
				getAncestor($('serial_code_send_copy'),'tr').hide();
			}
		});
		Event.observe('serial_code_low_warning', 'change', function(){
			if ($('serial_code_low_warning').value == 1) {
				getAncestor($('serial_code_warning_template'),'tr').show();
				getAncestor($('serial_code_warning_level'),'tr').show();
				getAncestor($('serial_code_send_warning'),'tr').show();
			} else {
				getAncestor($('serial_code_warning_template'),'tr').hide();
				getAncestor($('serial_code_warning_level'),'tr').hide();
				getAncestor($('serial_code_send_warning'),'tr').hide();
			}
		});
	}
</script>");
			} else {
				$newblock = $layout->createBlock('adminhtml/widget_form_element_dependence')
					->addFieldMap('serial_code_use_customer', 'product[serial_code_use_customer]')
					->addFieldMap('serial_code_customer_groups', 'product[serial_code_customer_groups][]')
					->addFieldMap('serial_code_send_email', 'product[serial_code_send_email]')
					->addFieldMap('serial_code_use_voucher', 'product[serial_code_use_voucher]')
					->addFieldMap('serial_code_email_template', 'product[serial_code_email_template]')
					->addFieldMap('serial_code_email_type', 'product[serial_code_email_type]')
					->addFieldMap('serial_code_send_copy', 'product[serial_code_send_copy]')
					->addFieldMap('serial_code_low_warning', 'product[serial_code_low_warning]')
					->addFieldMap('serial_code_warning_template', 'product[serial_code_warning_template]')
					->addFieldMap('serial_code_warning_level', 'product[serial_code_warning_level]')
					->addFieldMap('serial_code_send_warning', 'product[serial_code_send_warning]')
					->addFieldDependence('product[serial_code_customer_groups][]','product[serial_code_use_customer]',1)
					->addFieldDependence('product[serial_code_use_voucher]','product[serial_code_send_email]',1)
					->addFieldDependence('product[serial_code_email_template]','product[serial_code_send_email]',1)
					->addFieldDependence('product[serial_code_email_type]','product[serial_code_send_email]',1)
					->addFieldDependence('product[serial_code_send_copy]','product[serial_code_send_email]',1)
					->addFieldDependence('product[serial_code_warning_level]','product[serial_code_low_warning]',1)
					->addFieldDependence('product[serial_code_send_warning]','product[serial_code_low_warning]',1)
					->addFieldDependence('product[serial_code_warning_template]','product[serial_code_low_warning]',1);
			}
			$hrule = $layout->createBlock('core/text');
			$hrule->setText("
<script type=\"text/javascript\">
	function addHRule() {
		var elem = document.getElementById('serial_code_not_available').parentNode.parentNode;
		var nodes = elem.childNodes;
		for(var i=0; i<nodes.length; i++) {
			if (nodes[i].nodeName.toLowerCase() == 'td') {
				nodes[i].style.cssText += ';padding-bottom:10px !important;border-bottom-style:solid !important;border-bottom-width:3px !important;border-bottom-color:#D6D6D6 !important;';
			}
		}
		elem = document.getElementById('serial_code_update_stock').parentNode.parentNode;
		nodes = elem.childNodes;
		for(i=0; i<nodes.length; i++) {
			if (nodes[i].nodeName.toLowerCase() == 'td') {
				nodes[i].style.cssText += ';padding-top:12px !important;';
			}
		}
		nodes = [
			'serial_code_customer_groups',
			'serial_code_use_voucher',
			'serial_code_email_template',
			'serial_code_email_type',
			'serial_code_send_copy',
			'serial_code_warning_template',
			'serial_code_warning_level',
			'serial_code_send_warning'];
		for(i=0; i<nodes.length; i++) {
			elem = document.getElementById(nodes[i]).parentNode;
			do elem = elem.previousSibling;
			while (elem && elem.nodeType != 1);
			if (elem.className.toLowerCase() == 'label') {
				elem.style.cssText += ';padding-left:30px !important;';
			}
		}
	}
	var events = new Array();
	var defEvent = window.onload;
	if (defEvent != null && typeof defEvent == 'function') {events.push(defEvent);}
	if (typeof addProductFieldDependence == 'function') {events.push(addProductFieldDependence);}
	events.push(addHRule);
	window.onload = function() {
		for(var i=0; i<events.length; i++)
		events[i]();
	}
</script>");
			$layout->getBlock('content')->append($newblock, 'sc_product_field_dependence');
			$layout->getBlock('content')->append($hrule, 'sc_product_hrule');
		}
		return $this;
	}

	public function addInstructionsMessage($observer)
	{
		$block = $observer->getBlock();
		if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit ||
			$block instanceof Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Grid ||
			$block instanceof Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Grid) {
			$textstart = "
<style type=\"text/css\">
	.sc_instructions_message {
		padding-top:8px;
		text-align:center;
	}
	.sc_instructions_message a {
		text-decoration:underline;
	}
	.sc_instructions_message a:hover {
		text-decoration:none;
	}
</style>
<script type=\"text/javascript\">
	function addInstructionsMessage() {";
			if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit) {
				$link = '<a href="http://www.modifymage.com/instructions/serial-codes#product_attributes" target="_blank">www.modifymage.com/instructions/serial-codes#product_attributes</a>';
				$textmid ="
		var elem = $('serial_code_invoiced').parentNode;
		while (elem && elem.tagName.toLowerCase() != 'table') {
			elem = elem.parentNode;
		}";
			} elseif ($block instanceof Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Grid) {
				$link = '<a href="http://www.modifymage.com/instructions/serial-codes#how_to_add_serial_codes" target="_blank">www.modifymage.com/instructions/serial-codes#how_to_add_serial_codes</a>';
				$textmid ="
		var elem = document.getElementById('serialcodesGrid');";
			} else {
				$link = '<a href="http://www.modifymage.com/instructions/serial-codes#ordered_items" target="_blank">www.modifymage.com/instructions/serial-codes#ordered_items</a>';
				$textmid ="
		var elem = document.getElementById('serialcode_itemsGrid');";
			}
			$message = Mage::helper('serialcodes')->__('Instructions available online at %s', $link);
			$textend = "
		var message = document.createElement('div');
		message.id = 'sc_instructions_message';
		message.className = 'sc_instructions_message';
		message.innerHTML = '{$message}';
		elem.parentNode.insertBefore(message, elem.nextSibling);
	}
	function addLoadEvent(func) {
		var oldonload = window.onload;
		if (typeof window.onload != 'function') {
			window.onload = func;
		} else {
			window.onload = function() {
				if (oldonload) {
					oldonload();
				}
				func();
			}
		}
	}
	addLoadEvent(addInstructionsMessage);
</script>";
			$layout = $block->getLayout();
			$newblock = $layout->createBlock('core/text');
			$newblock->setText($textstart.$textmid.$textend);
			$layout->getBlock('content')->append($newblock, 'sc_instructions_message');
		}
		return $this;
	}
}