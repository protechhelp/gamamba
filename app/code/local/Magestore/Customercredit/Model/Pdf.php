<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Customercredit_Model_Pdf extends Mage_Sales_Model_Order_Pdf_Total_Default {

    public function getTotalsForDisplay() {
        $invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
        $creditmemoId = Mage::app()->getRequest()->getParam('creditmemo_id');
        if($invoiceId){
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            $amount = $this->getOrder()->formatPriceTxt($invoice->getCustomercreditDiscount());
        } else if ($creditmemoId){
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
            $amount = $this->getOrder()->formatPriceTxt($creditmemo->getCustomercreditDiscount());
        } else {
            $amount = $this->getOrder()->formatPriceTxt($this->getOrder()->getCustomercreditDiscount());
        }
        
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix() . $amount;
        }
        $total = array(array(
                'label' => 'Customer Credit',
                'amount' => '-' . $this->getAmountPrefix() . $amount,
                'font_size' => $fontSize,
        ));
        return $total;
    }

}