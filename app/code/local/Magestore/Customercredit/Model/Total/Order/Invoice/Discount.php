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
 * Customercredit Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */

class Magestore_Customercredit_Model_Total_Order_Invoice_Discount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    { 
        $order = $invoice->getOrder();

        if ($order->getCustomercreditDiscount() < 0.0001) {
            return ;
        }
        if ($invoice->isLast()) {
            $baseDiscount   = $order->getBaseCustomercreditDiscount() ;
            $discount       = $order->getCustomercreditDiscount() ;
            
            foreach ($order->getInvoiceCollection() as $existedInvoice) {
                if ($baseDiscount > 0.0001) {
                    $baseDiscount   -= $existedInvoice->getBaseCustomercreditDiscount();
                    $discount       -= $existedInvoice->getCustomercreditDiscount();
                }
            };
        } else {
            $orderData = $order->getData();
            $invoiceData = $invoice->getData();
            if($invoiceData['shipping_incl_tax']){
                $ratio = ($invoiceData['subtotal_incl_tax']+$invoiceData['shipping_incl_tax']) / ($orderData['subtotal_incl_tax']+$orderData['shipping_incl_tax']);
            }else{
                $ratio = $invoiceData['subtotal_incl_tax'] / ($orderData['subtotal_incl_tax']+$orderData['shipping_incl_tax']);
            }
            $baseDiscount   = $order->getBaseCustomercreditDiscount() * $ratio;
            $discount       = $order->getCustomercreditDiscount() * $ratio;
            
            $maxBaseDiscount   = $order->getBaseCustomercreditDiscount();
            $maxDiscount       = $order->getCustomercreditDiscount();
            foreach ($order->getInvoiceCollection() as $existedInvoice) {
                if ($maxBaseDiscount > 0.0001) {
                    $maxBaseDiscount    -= $existedInvoice->getBaseCustomercreditDiscount();
                    $maxDiscount        -= $existedInvoice->getCustomercreditDiscount();
                }
            }
            if ($baseDiscount > $maxBaseDiscount) {
                $baseDiscount   = $maxBaseDiscount;
                $discount       = $maxDiscount;
            }
        }
       
        if ($baseDiscount > 0.0001) {
            if ($invoice->getBaseGrandTotal() <= $baseDiscount) {
                $invoice->setBaseCustomercreditDiscount($invoice->getBaseGrandTotal());
                $invoice->setCustomercreditDiscount($invoice->getGrandTotal());
                $invoice->setBaseGrandTotal(0.0);
                $invoice->setGrandTotal(0.0);
            } else {
                $invoice->setBaseCustomercreditDiscount($baseDiscount);
                $invoice->setCustomercreditDiscount($discount);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseDiscount);
                $invoice->setGrandTotal($invoice->getGrandTotal() - $discount);
            }
        }
    }
        
}