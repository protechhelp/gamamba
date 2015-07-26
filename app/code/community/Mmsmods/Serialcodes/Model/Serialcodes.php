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

class Mmsmods_Serialcodes_Model_Serialcodes extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('serialcodes/serialcodes');
    }

	public function isDeferred($order)
	{
		$payment = $order->getPayment()->getMethodInstance()->getCode();
		$deferred =
			$payment == 'checkmo' || 
			$payment == 'cashondelivery' || 
			$payment == 'banktransfer' || 
			$payment == 'purchaseorder' || 
			$payment == 'directdeposit_au' || 
			$payment == 'msp_banktransfer' || 
			$payment == 'msp_directdebit';
		return $deferred;
	}

	public function checkOrderStatus($order)
	{
		$status = $order->getStatus();
		$issue =
			$status == 'pending' || 
			$status == 'processing' ||
			$status == 'complete';
		return $issue;
	}

	public function getAvailableCount($sku)
	{
		return count($this->getCollection()
			->addFieldToFilter('sku',array('like' => $sku))
			->addFieldToFilter('status',array('like' => 0))->load());
	}

	public function updateInventoryStock($sku, $count = NULL)
	{
		if ($count === NULL) {$count = $this->getAvailableCount($sku);}
		if (is_numeric($count)) {
			$updates = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToFilter('serial_code_update_stock',1)
				->addAttributeToFilter(array(
					array('attribute'=>'serial_code_pool','eq'=>$sku),
					array('attribute'=>'sku','eq'=>$sku)));
			foreach ($updates as $update) {
				if($update->getSerialCodeUpdateStock()) {
					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($update->getId());
					if ($stock->getManageStock()) {
						$stock->setQty(floatval($count));
						$stock->setIsInStock($count > $stock->getMinQty());
						$stock->save();
					}
				}
			}
		}
	}

	public function checkCustomerGroup($order, $product, $source)
	{
		$groupid = 0;
		if (Mage::helper('customer')->isLoggedIn()) {
			$groupid = Mage::helper('customer')->getCustomer()->getGroupId();
		} elseif ($order->getCustomerGroupId() && $source == 'pending') {
			$groupid = $order->getCustomerGroupId();
		}
		return in_array($groupid, $product->getSerialCodeCustomerGroups());
	}

	public function getPendingStatus($order, $item, $product, $i, $source = NULL)
	{
		$pending = ($i >= round($item->getQtyInvoiced()) && 
				(($product->getSerialCodeInvoiced() && !$product->getSerialCodeSerialized() && !($product->getSerialCodeUseCustomer() && $this->checkCustomerGroup($order, $product, $source))) || 
				($product->getSerialCodeSerialized() && $this->isDeferred($order)))) ||
				($product->getSerialCodeUseCustomer() && !$this->checkCustomerGroup($order, $product, $source));				
		return $pending;
	}

	public function hidePendingCodes($order, $item, $product, $codeid, $i)
	{
		$hide = (isset($codeid) && $this->load($codeid)->getStatus() == 2) || 
				(empty($codeid) && $this->getPendingStatus($order, $item, $product, $i));
		if ($item->getProductType() == 'configurable') {
			$product = Mage::getModel('catalog/product')->setStoreId($order->getStoreId())->load($product->getIdBySku($item->getProductOptionByCode('simple_sku')));
			$hide = $hide || (empty($codeid) && $this->getPendingStatus($order, $item, $product, $i));
		}
		return $hide;
	}

	public function getOrderTest($order, $source)
	{
		switch ($source) {
			case 'pending':
				$test = TRUE;
				break;
			case 'invoicing':
				$test = TRUE;
				break;
			case 'checkout':
				$test = $this->checkOrderStatus($order);
				break;
			case 'controller':
				$test = TRUE;
				break;
			default:
				$test = FALSE;
		}
		return $test;
	}

	public function getProductTest($order, $product, $source)
	{
		switch ($source) {
			case 'pending':
				$test = ($this->isDeferred($order) && ($product->getSerialCodeInvoiced() || $product->getSerialCodeSerialized())) || $product->getSerialCodeUseCustomer();
				break;
			case 'invoicing':
				$test = $product->getSerialCodeInvoiced();
				break;
			case 'checkout':
				$test = $product->getSerialCodeSerialized();
				break;
			case 'controller':
				$test = TRUE;
				break;
			default:
				$test = FALSE;
		}
		return $test;
	}

	public function getInvoiceStates($order, $sku)
	{
		$states = array();
		if ($order->hasInvoices()) {
			foreach ($order->getInvoiceCollection() as $invoice) {
				foreach ($invoice->getAllItems() as $item) {
					if ($item->getSku() == $sku) {
						for ($i=0; $i<$item->getQty(); $i++) {
							$states[] = $invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID;
						}
					}
				}
			}
		}
		return $states;
	}

	public function getCodePool($item, $source, &$product, &$sku)
	{
		if (!$sku = trim($product->getSerialCodePool())) {$sku = trim($product->getSku());}
		$codes = $this->getCollection()->addFieldToFilter('sku', array('like' => $sku))->load();
		if ($source == 'controller') {
			if ($codes->getData() == null && $item->getProductType() != 'bundle') {
				if ($item->getProductType() == 'configurable') {
					$storeid = Mage::getSingleton('sales/order')->load($item->getOrderId())->getStoreId();
					$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($product->getIdBySku($item->getProductOptionByCode('simple_sku')));
					if (!$sku = trim($product->getData('serial_code_pool'))) {$sku = trim($product->getSku());}
					$codes = $this->getCollection()->addFieldToFilter('sku',array('like' => $sku))->load();
				} elseif (trim($item->getSku()) != trim($product->getSku())) {
					$sku = trim($item->getSku());
					$codes = $this->getCollection()->addFieldToFilter('sku',array('like' => $sku))->load();
				}
			}
		} else {
			if ($codes->getData() == NULL && $item->getProductType() != 'bundle' && $item->getProductType() != 'configurable' && trim($item->getSku()) != trim($product->getSku())) {
				$sku = trim($item->getSku());
				$codes = $this->getCollection()->addFieldToFilter('sku', array('like' => $sku))->load();
			}
		}
		return $codes;
	}

	public function bindCodePool($item, $source, $pcodes, &$issued, &$sku)
	{
		$icodes = array_pad(explode(',',$item->getSerialCodeIds()),$issued,'');
		if (!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && version_compare(Mage::getVersion(),'1.4.1.1') < 0) {
			$storeid = Mage::getSingleton('sales/order')->load($item->getOrderId())->getStoreId();
		} else {
			$storeid = $item->getStoreId();
		}
		$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
		$codes = $this->getCodePool($item, $source, $product, $sku);
		if (!count($codes)) {$sku = '';}
		if ($issued > count($pcodes)) {$issued = count($pcodes);}
		for ($i=0; $i<$issued; $i++) {
			$icodes[$i] = -1;
			foreach ($codes as $code) {
				if ($code->getCode() == $pcodes[$i]) {
					$icodes[$i] = $code->getSerialcodesId();
					break;
				}
			}
		}
		return array_slice($icodes, 0, $issued);
	}

	public function addStatusText($qty, $icodes, $pcodes)
	{
		for ($i=0; $i<$qty; $i++) {
			$status = NULL;
			if (is_numeric($icodes[$i]) && $icodes[$i] != -1) {$status = $this->load($icodes[$i])->getStatus();}
			if (is_numeric($status)) {
				switch ($status) {
					case 0:
						$pcodes[$i] = $pcodes[$i].'&nbsp;<span style="color:red;">'.Mage::helper('serialcodes')->__('Warning!').'</span>';
						break;
					case 1:
						break;
					case 2:
						$pcodes[$i] = $pcodes[$i].'&nbsp;<span style="color:green;">'.Mage::helper('serialcodes')->__('Pending').'</span>';
						break;
					default:
						$pcodes[$i] = $pcodes[$i].'&nbsp;<span style="color:red;">'.Mage::helper('serialcodes')->__('Error!').'</span>';
				}
			} elseif ($icodes[$i] == -1) {
				$pcodes[$i] = $pcodes[$i].'&nbsp;<span style="color:blue;">'.Mage::helper('serialcodes')->__('Manual').'</span>';
			}
		}
		return implode('<br />',$pcodes);
	}

	public function issueSerialCodes($order, $source, $items = NULL, $paid = NULL)
	{
		$orderid = $order->getIncrementId();
		$storeid = $order->getStoreId();
		$backend = Mage::app()->getStore()->isAdmin();
		$admin = Mage::getSingleton('adminhtml/session');
		if ($items === NULL) {$items = $order->getAllItems();}
		if ($this->getOrderTest($order, $source)) {
			foreach ($items as $item) {
				$configured = 0;
				$issued = 0;
				$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
				$sku = '';
				if ($this->getProductTest($order, $product, $source)) {
					if ($parentitem = $item->getParentItem()) {
						$parentproduct = Mage::getModel('catalog/product')->load($parentitem->getProductId());
						if ($parentitem->getProductType() == 'configurable' && !$this->getProductTest($order, $parentproduct, $source)) {$item = $parentitem;}
						$parentproduct->clearInstance();
					}
					if ($source == 'invoicing') {
						$qty = round($item->getQtyInvoiced());
					} else {
						$qty = round($item->getQtyOrdered());
					}
					$pcodes = explode("\n", $item->getSerialCodes());
					$icodes = array_pad(explode(',',$item->getSerialCodeIds()),$qty,'');
					$issueoptions = new Varien_Object();
					$issueoptions->setOption('');
					Mage::dispatchEvent('sc_issue_get_codepool_before', array('item' => $item,'source' => $source,'issue_options' => $issueoptions));
					if ($issueoptions->getOption() != 'halt_issue') {
						$codes = $this->getCodePool($item, $source, $product, $sku);
						if (!$codetype = $product->getSerialCodeType()) {$codetype = Mage::helper('serialcodes')->__('Serial Code');}
						$states = $this->getInvoiceStates($order, $item->getSku());
						$issuecustomer = $source == 'controller'
								&& $product->getSerialCodeUseCustomer()
								&& $item->getSerialCodesIssued() == $item->getQtyOrdered();
						for ($i=0; $i<$qty; $i++) {
							$saved = 0;
							if ($i < $item->getQtyInvoiced() || $issuecustomer) {
								$sc_status = Mage::getModel('serialcodes/serialcodes');
								if ((is_numeric($icodes[$i])
										&& $icodes[$i] > -1
										&& $sc_status->load($icodes[$i])->getStatus() != 1
										&& $issueoptions->getOption() != 'status_pending')
										&& ((!empty($states[$i])
										&& !($product->getSerialCodeUseCustomer() && !$product->getSerialCodeInvoiced()))
										|| $issuecustomer)) {
									$sc_status->setStatus(1)->save();
									if ($backend && !$configured) {$admin->addNotice(Mage::helper('serialcodes')->__('Status of codes has been changed for %s.',$product->getName()));}
									$configured = 1;
									continue;
								}
							}
							if (empty($icodes[$i]) && $i >= $item->getSerialCodesIssued()) {
								foreach ($codes as $code) {
									$configured = 1;
									if ($code->getStatus() == 0) {
										$pcodes[$i] = $code->getCode();
										$icodes[$i] = $code->getSerialcodesId();
										$item->setSerialCodeType($codetype);
										$item->setSerialCodes(implode("\n",$pcodes));
										$item->setSerialCodeIds(implode(',',$icodes));
										$item->setSerialCodesIssued($item->getSerialCodesIssued() + 1);
										$item->setSerialCodePool($sku);
										$item->save();
										if (($this->getPendingStatus($order, $item, $product, $i, $source)
												|| ($source == 'invoicing' && !$paid)
												|| $issueoptions->getOption() == 'status_pending')
												&& $issueoptions->getOption() != 'status_used') {
											$code->setStatus(2);								
										} else {
											$code->setStatus(1);
										}
										$code->setNote($orderid);
										$code->setUpdateTime(now());
										$code->save();
										$saved = 1;
										if($backend && !$issued) {$admin->addSuccess(Mage::helper('serialcodes')->__('Codes issued for %s.',$product->getName()));}
										$issued = 1;
										break;
									}
								}
								if (!$saved && empty($icodes[$i]) && $codes->getFirstItem()->getCode()) {
									if(!trim($message = $product->getSerialCodeNotAvailable())) {$message = Mage::helper('serialcodes')->__('Oops! Not available.');}
									$item->setSerialCodeType($codetype);
									$pcodes = explode("\n", $item->getSerialCodes());
									$next = ''; if(count(array_filter($pcodes))) {$next = "\n";}
									if (count(array_filter($pcodes)) < $qty) {
										$item->setSerialCodes(implode("\n",$pcodes).$next.$message);
										$item->setSerialCodePool($sku);
										$item->save();
										$saved = 1;
									}
									if($backend && $i == $qty - 1) {$admin->addError(Mage::helper('serialcodes')->__('Ran out of codes for %s.',$product->getName()));}
								}
							}
						}
						if (isset($saved)) {$this->sendWarningLevelEmail($product, $order, $sku);}
						if ($backend && !$configured) {
							if (($source == 'controller' || $source == 'invoicing') && $item->getSerialCodesIssued() >= $item->getQtyOrdered()) {
								if ($source != 'invoicing') {$admin->addNotice(Mage::helper('serialcodes')->__('All codes have already been issued for %s.',$product->getName()));}
							} else {
								$admin->addError(Mage::helper('serialcodes')->__('Unable to issue codes for %s. Check configuration.',$product->getName()));
							}
							continue;
						}
					}
					Mage::dispatchEvent('sc_issue_after', array('item' => $item,'source' => $source));
				}
			}
		}
	}

	public function sendDeliveryEmail($order, $source = NULL, $items = NULL)
	{
		$templatearray = array();
		$storeid = $order->getStoreId();
		if ($items === NULL) {$items = $order->getAllItems();}
		foreach ($items as $item) {
			$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
			if ($source == 'controller' && $item->getProductType() == 'configurable' && !$product->getSerialCodeSendEmail()) {
				$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($product->getIdBySku($item->getProductOptionByCode('simple_sku')));
			}
			if (($product->getSerialCodeSendEmail() && !($source == 'invoicing' && $item->getQtyRefunded() > 0)) || $source == 'controller') {
				if ($parentitem = $item->getParentItem()) {
					if ($parentitem->getProductType() == 'configurable') {$item = $parentitem;}
				}
				$codes = explode("\n",$item->getSerialCodes());
				$count = count($codes);
				if ($item->getQtyInvoiced() > 0) {$count = $item->getQtyInvoiced();}
				if ($codes[0]) {
					$codeids = array_pad(explode(',',$item->getSerialCodeIds()),$count,'');
					$template = $product->getSerialCodeEmailTemplate();
					$templatearray[$template]['code'] = '';
					$templatearray[$template]['emailtype'] = $product->getSerialCodeEmailType();
					if (isset($templatearray[$template]['bcc'])) {
						$templatearray[$template]['bcc'] .= ' '.$product->getSerialCodeSendCopy();
					} else {
						$templatearray[$template]['bcc'] = $product->getSerialCodeSendCopy();
					}
					if (!$templatearray[$template]['codetype'] = $item->getSerialCodeType()) {$templatearray[$template]['codetype'] = Mage::helper('serialcodes')->__('Serial Code');}
					if (empty($templatearray[$template]['html'])) {$templatearray[$template]['html'] = '<div class="sc_items">';}
					if ($templatearray[$template]['html'] != '<div class="sc_items">') {$templatearray[$template]['html'] .= '<br /><br />';}
					$templatearray[$template]['html'] .= '<span class="sc_product">'.$product->getName().'</span>';
					for ($i=0; $i<$count; $i++) {
						$showmessage = $i == 0;
						if ($this->hidePendingCodes($order, $item, $product, $codeids[$i], $i)) {
							$codes[$i] = Mage::helper('serialcodes')->__('Issued when payment received.');
						}
						$templatearray[$template]['html'] .= '<br /><span class="sc_type">'.$templatearray[$template]['codetype'].':</span> <span class="sc_code">'.$codes[$i].'</span>';
						if ($product->getSerialCodeUseVoucher()) {
							$templatearray[$template]['code'] = $codes[$i];
							$this->dispatchDeliveryEmail($order, $templatearray, $showmessage);
							$templatearray[$template]['html'] = str_replace('<br /><span class="sc_type">'.$templatearray[$template]['codetype'].':</span> <span class="sc_code">'.$codes[$i].'</span>','',$templatearray[$template]['html']);
						}
					}
					if (round($item->getQtyInvoiced()) && $item->getQtyInvoiced() < $item->getQtyOrdered()) {
						$templatearray[$template]['html'] .= '<br /><span class="sc_message">'.Mage::helper('serialcodes')->__('Partial Invoice - Remaining to Issue: %d', round($item->getQtyOrdered() - $item->getQtyInvoiced())).'</span><br />';
					}
				} else {
					if (Mage::app()->getStore()->isAdmin() && $product->getSerialCodeSendEmail()) {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Email for %s not sent. No message or codes issued.',$product->getName()));
					}
				}
			}
		}
		if (!$product->getSerialCodeUseVoucher()) {
			$this->dispatchDeliveryEmail($order, $templatearray, TRUE);
		}
	}

	private function dispatchDeliveryEmail($order, $templatearray, $showmessage)
	{
		foreach ($templatearray as $template => $value) {
			if (isset($value['html'])) {
				$value['html'] .= '</div>';
				$itemstext = strip_tags(str_replace('<br />',"\n",$value['html']));
				if (is_numeric($template)) {
					$emailTemplate = Mage::getSingleton('core/email_template')->load($template);
				} else {
					$emailTemplate = Mage::getSingleton('core/email_template')->loadDefault($template);
				}
				$emailvars = array(
					'itemstext'	=> $itemstext,
					'itemshtml'	=> $value['html'],
					'codevalue'	=> $value['code'],
					'codetype'	=> $value['codetype'],
					'emailtype'	=> $value['emailtype'],
					'order'		=> $order
				);
				$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name'));
				$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));
				$emails = explode(' ',$value['bcc']);
				$emails = array_map('trim', $emails);
				$emails = array_filter($emails);
				if ($emails) {
					$emailTemplate->addBcc($emails);
				}
				$emailTemplate->send(
					$order->getCustomerEmail(),
					$order->getBillingAddress()->getName(),
					$emailvars);
				if ($showmessage) {
					if (Mage::app()->getStore()->isAdmin()) {
						if ($value['emailtype']) {
							Mage::getSingleton('adminhtml/session')
								->addSuccess(Mage::helper('serialcodes')
								->__('An email containing %s(s) and %s has been sent to %s.',$value['codetype'], $value['emailtype'], $order->getCustomerEmail()));
						} else {
							Mage::getSingleton('adminhtml/session')
								->addSuccess(Mage::helper('serialcodes')
								->__('An email containing %s(s) has been sent to %s.',$value['codetype'], $order->getCustomerEmail()));
						}
					} else {
						if ($value['emailtype']) {
							Mage::getSingleton('checkout/session')
								->addNotice(Mage::helper('serialcodes')
								->__('Your %s(s) and %s have been emailed to %s.',$value['codetype'], $value['emailtype'], $order->getCustomerEmail()));
						} else {
							Mage::getSingleton('checkout/session')
								->addNotice(Mage::helper('serialcodes')
								->__('Your %s(s) have been emailed to %s.',$value['codetype'], $order->getCustomerEmail()));
						}
					}
				}
			}
		}
	}

	public function sendWarningLevelEmail($product, $order, $sku = NULL)
	{
		if ($product->getSerialCodeLowWarning() && ($email = $product->getSerialCodeSendWarning())) {
			if (!$sku) {
				if (!$sku = trim($product->getSerialCodePool())) {$sku = trim($product->getSku());}
			}
			if (!$codetype = $product->getSerialCodeType()) {$codetype = Mage::helper('serialcodes')->__('Serial Code');}
			$level = $product->getSerialCodeWarningLevel();
			$available = $this->getAvailableCount($sku);
			if ($available <= $level) {
				$emailvars = array(
					'product'	=> $product->getName(),
					'available'	=> $available,
					'none'		=> !$available,
					'codetype'	=> $codetype,
					'pool'		=> $sku,
					'order'		=> $order
				);
				if (is_numeric($template = $product->getSerialCodeWarningTemplate())) {
					$emailTemplate = Mage::getSingleton('core/email_template')->load($template);
				} else {
					$emailTemplate = Mage::getSingleton('core/email_template')->loadDefault($template);
				}
				$emails = explode(' ',$email);
				$emails = array_map('trim', $emails);
				$emails = array_filter($emails);
				$email = $emails[0];
				unset($emails[0]);
				$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name'));
				$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));
				if ($emails = array_values($emails)) {$emailTemplate->addBcc($emails);}
				$emailTemplate->send(
					$email,
					'Administrator',
					$emailvars);
			}
		}
	}
}