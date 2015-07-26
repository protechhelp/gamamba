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

class Magpleasure_Webmoney_RedirectController extends Mage_Core_Controller_Front_Action {

    protected $_order;

    /**
     * Retrives helper
     * @return Magpleasure_Webmoney_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('webmoney');
    }

    public function indexAction()
    {
        $this->getResponse()
                ->setHeader('Content-type', 'text/html; charset=utf8')
                ->setBody($this->getLayout()->createBlock('webmoney/redirect')->toHtml());
    }

    protected function _getOrderId($incId)
    {
        return $this->_getOrder($incId) ? $this->_getOrder($incId)->getId() : false;
    }

    /**
     * Order
     *
     * @param $incId
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder($incId)
    {
        if ($incId){
            return Mage::getModel('sales/order')->loadByIncrementId($incId);
        }
        return false;
    }

    public function successAction()
    {
        if($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $session = Mage::getSingleton('checkout/session');
            if ($this->_helper()->getCustomerMutex()){
                $session->getQuote()->setIsActive(false)->save();
                $this->_redirect('checkout/onepage/success', array('_secure'=>true));
            } else {
                $this->_redirect('sales/order/view', array('order_id' => $this->_getOrderId(@$post['LMI_PAYMENT_NO'])));
            }
        }
    }



    public function resultAction()
    {
        if ($this->getRequest()->isPost()){
            $post = $this->getRequest()->getPost();
            $session = Mage::getSingleton('checkout/session');
            $order = $this->_getOrder(@$post['LMI_PAYMENT_NO']);
            /** @var Magpleasure_Webmoney_Model_Checkout $webmoney  */
            $webmoney = Mage::getModel('webmoney/checkout');
            # Is prerequest
            if (isset($post['LMI_PREREQUEST']) && $post['LMI_PREREQUEST']){
                if ($webmoney->checkPurse($order, $post['LMI_PAYEE_PURSE'])){
                    $this->getResponse()->setBody("YES");
                }
            } else {
                                                
                $session = Mage::getSingleton('checkout/session');
                $session->setQuoteId(@$post['LMI_PAYMENT_NO']);

                $hash = $post['LMI_HASH'];
                $amount = $post['LMI_PAYMENT_AMOUNT'];
                $chAmount   = trim(round($order->getGrandTotal(), 2));

                if ($amount != $chAmount){
                    $this->_addErrorToOrder($order, $this->_helper()->__('Order Amount is wrong.'));
                } elseif ($hash != $webmoney->getHash($post)) {
                    $this->_addErrorToOrder($order, $this->_helper()->__('Secure check failure.'));
                } elseif (!$webmoney->checkPurse($order, $post['LMI_PAYEE_PURSE'])) {
                    $this->_addErrorToOrder($order, $this->_helper()->__('Wrong Merchant Purse.'));
                } else {

                    try {

                        # Create invoice

                        if ($order && $order->canInvoice()){

                            $orderAmount = $order->getOrderCurrency()->format(@$post['LMI_PAYMENT_AMOUNT'], null, false);
                            $transactionId = @$post['LMI_SYS_TRANS_NO'];
                            if ($transactionId && $orderAmount){

                                $webmoney->registerPaymentCapture($order, $transactionId, $amount, $post);
                            } else {

                                Mage::throwException("Transactional data isn't complete.");
                            }
                        }

                    } catch (Exception $e){

                        $this->_helper()->getCommon()->getException()->logException($e);
                    }
                }
            }
        }
    }

    public function failureAction()
    {
        if($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $order = $this->_getOrder(@$post['LMI_PAYMENT_NO']);
            $order->cancel();
            $this->_addErrorToOrder($order, $this->_helper()->__('WebMoney payment failure.'));
        }

        $this->_helper()->getCustomerMutex();
        $this->_redirect('customer/account', array('_secure'=>true));
    }

    protected function _addErrorToOrder($order, $message)
    {
        if ($order){
            $order->addStatusToHistory(
                    $order->getStatus(),
                    $message
            )->save();
        }
    }
}
