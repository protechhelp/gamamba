<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Webmoney
 * @version    1.0.1
 * @copyright  Copyright (c) 2010-2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Webmoney_Block_Info extends Mage_Payment_Block_Info
{
    protected $_order;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mp_webmoney/info.phtml');
    }

    public function getOrder()
    {
        if ($orderId = $this->getRequest()->getParam('order_id')){
            return Mage::getModel('sales/order')->load($orderId);
        }
        return null;
    }

    /**
     * Render as PDF
     *
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('mp_webmoney/pdf/info.phtml');
        return $this->toHtml();
    }

    protected function _getOrder()
    {
        if (!$this->_order){

            $order = false;

            $orderId = $this->getRequest()->getParam('order_id');
            $invoiceId = $this->getRequest()->getParam('invoice_id');
            $creditMemoId = $this->getRequest()->getParam('creditmemo_id');

            $orderId = $this->getRequest()->getParam('order_id');
            if ($orderId){

                /** @var Mage_Sales_Model_Order $order */
                $order = Mage::getModel('sales/order')->load($orderId);


            } elseif ($invoiceId) {

                /** @var Mage_Sales_Model_Order_Invoice $invoice */
                $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);

                if ($invoice->getId()){
                    $order = $invoice->getOrder();
                }

            } elseif ($creditMemoId) {

                /** @var Mage_Sales_Model_Order_Creditmemo $creditMemo */
                $creditMemo = Mage::getModel('sales/order_creditmemo')->load($creditMemoId);

                if ($creditMemo->getId()){
                    $order = $creditMemo->getOrder();
                }
            }

            if ($order && $order->getId()){

                $this->_order = $order;
            }
        }

        return $this->_order;
    }

    protected function _getTransactionData($key)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->_getOrder();
        if ($order){
            $lastTransId = $order->getPayment()->getLastTransId();

            $transaction = $order->getPayment()->getTransaction($lastTransId);
            if ($transaction){
                return $transaction->getAdditionalInformation($key);
            }
        }
        return false;
    }

    public function getTransactionId()
    {
        return $this->_getTransactionData('LMI_SYS_TRANS_NO');
    }

    public function getInvoiceId()
    {
        return $this->_getTransactionData('LMI_SYS_INVS_NO');
    }

    public function getPayerWmId()
    {
        return $this->_getTransactionData('LMI_PAYER_WM');
    }

    public function getPayerPurse()
    {
        return $this->_getTransactionData('LMI_PAYER_PURSE');
    }

    public function getPassportUrl()
    {
        if ($wmid = $this->getPayerWmId()){
            return sprintf("https://passport.wmtransfer.com/asp/certview.asp?wmid=%s", $wmid);
        }
        return false;
    }



}