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

class Magestore_Customercredit_Model_Total_Order_Creditmemo_Discount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getCustomercreditDiscount() < 0.0001) {
            return ;
        }
        if ($this->isLast($creditmemo)) {
            $baseDiscount   = $order->getBaseCustomercreditDiscount();
            $discount       = $order->getCustomercreditDiscount();
            foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
                if ($baseDiscount > 0.0001) {
                    $baseDiscount   -= $existedCreditmemo->getBaseCustomercreditDiscount();
                    $discount       -= $existedCreditmemo->getCustomercreditDiscount();
                }
            }
        } else {
            
            $orderData = $order->getData();
            $creditmemoData = $creditmemo->getData();
            if($creditmemoData['shipping_incl_tax']){
                $ratio = ($creditmemoData['subtotal_incl_tax']+$creditmemoData['shipping_incl_tax']) / ($orderData['subtotal_incl_tax']+$orderData['shipping_incl_tax']);
            }else{
                $ratio = $creditmemoData['subtotal_incl_tax'] / ($orderData['subtotal_incl_tax']+$orderData['shipping_incl_tax']);
            }
           
            $baseDiscount   = $order->getBaseCustomercreditDiscount() * $ratio;
            $discount       = $order->getCustomercreditDiscount() * $ratio;
            
            $maxBaseDiscount   = $order->getBaseCustomercreditDiscount();
            $maxDiscount       = $order->getCustomercreditDiscount();
            foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
                if ($maxBaseDiscount > 0.0001) {
                    $maxBaseDiscount    -= $existedCreditmemo->getBaseCustomercreditDiscount();
                    $maxDiscount        -= $existedCreditmemo->getCustomercreditDiscount();
                }
            }
            if ($baseDiscount > $maxBaseDiscount) {
                $baseDiscount   = $maxBaseDiscount;
                $discount       = $maxDiscount;
            }
        }
      
        if ($baseDiscount > 0.0001) {
            if ($creditmemo->getBaseGrandTotal() <= $baseDiscount) {
                $creditmemo->setBaseCustomercreditDiscount($creditmemo->getBaseGrandTotal());
                $creditmemo->setCustomercreditDiscount($creditmemo->getGrandTotal());
                $creditmemo->setBaseGrandTotal(0.0);
                $creditmemo->setGrandTotal(0.0);
            } else {
                $creditmemo->setBaseCustomercreditDiscount($baseDiscount);
                $creditmemo->setCustomercreditDiscount($discount);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseDiscount);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $discount);
            }
            $creditmemo->setAllowZeroGrandTotal(true);
        }
    }
    
    /**
     * check credit memo is last or not
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
}
?>