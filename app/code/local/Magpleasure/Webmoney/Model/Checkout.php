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

class Magpleasure_Webmoney_Model_Checkout extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'webmoney';
    protected $_formBlockType = 'webmoney/form';
    protected $_infoBlockType = 'webmoney/info';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = true;

    protected $_purseCode = array(
        'USD' => 'wmz',
        'EUR' => 'wme',
        'RUB' => 'wmr',
        'UAH' => 'wmu',
        'UZS' => 'wmy',
        'BYR' => 'wmb'
    );

    /**
     * Retrives helper
     * @return Magpleasure_Webmoney_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('webmoney');
    }

    /**
     * Checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getPurse($order)
    {
        $purse = $this->_validateCurrencyCode($order->getOrderCurrencyCode()) ?
            Mage::getStoreConfig('payment/webmoney/' . @$this->_purseCode[$order->getOrderCurrencyCode()]) :
            Mage::getStoreConfig('payment/webmoney/' . @$this->_purseCode[$order->getGlobalCurrencyCode()]);

        return $purse;
    }

    /**
     * Check purse
     *
     * @param $order
     * @param $purse
     * @return bool
     */
    public function checkPurse($order, $purse)
    {
        return $this->getPurse($order) == $purse;
    }

    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setIsCustomerNotified(true);

        return $this;
    }

    public function getOrderPlaceRedirectUrl($orderId = null)
    {
        $params = array('_secure' => true);

        if ($orderId) {
            $params['order_id'] = $orderId;
        }
        return Mage::getUrl('webmoney/redirect', $params);
    }

    public function getWebmoneyUrl()
    {
        $url = 'https://merchant.webmoney.ru/lmi/payment.asp';
        return $url;
    }

    /**
     * Retrieves order
     * @return Mage_Sales_Model_Order
     */
    public function getQuote()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        return $order;
    }

    protected function _validateCurrencyCode($currencyCode)
    {
        if (isset($this->_purseCode[$currencyCode]) && Mage::getStoreConfig('payment/webmoney/' . $this->_purseCode[$currencyCode])) {
            return true;
        }
        return false;
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->_validateCurrencyCode($currencyCode);
    }

    /**
     * MD5 Hash from POST
     *
     * @param array $post
     * @return string
     */
    public function getHash($post)
    {
        $keys = array(
            'LMI_PAYEE_PURSE',
            'LMI_PAYMENT_AMOUNT',
            'LMI_PAYMENT_NO',
            'LMI_MODE',
            'LMI_SYS_INVS_NO',
            'LMI_SYS_TRANS_NO',
            'LMI_SYS_TRANS_DATE',
            'LMI_SECRET_KEY',
            'LMI_PAYER_PURSE',
            'LMI_PAYER_WM',
        );

        $str = "";
        foreach ($keys as $key) {

            if ($key == 'LMI_SECRET_KEY') {
                $str .= Mage::getStoreConfig('payment/webmoney/secure_key');
            } else {
                if (isset($post[$key])) {
                    $str .= $post[$key];
                }
            }
        }

        return strtoupper(md5($str));
    }

    public function getWebmoneyCheckoutFormFields()
    {
        $requestOrderId = Mage::app()->getRequest()->getParam('order_id');
        if (!$requestOrderId) {
            $this->_helper()->setUpCustomerMutex();
        }

        $order_id = $requestOrderId ? $requestOrderId : $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

        $purse = $this->getPurse($order);
        $amount = trim(round($order->getGrandTotal(), 2));

        $params = array(
            'LMI_PAYMENT_NO' => $order_id,
            'LMI_PAYEE_PURSE' => $purse,
            'LMI_PAYMENT_DESC_BASE64' => base64_encode($this->_helper()->__('Payment for order #') . $order_id),
            'LMI_PAYMENT_AMOUNT' => $amount
        );
        return $params;
    }


    protected function _addTransaction(
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Invoice $invoice,
        Mage_Sales_Model_Order_Payment $payment,
        $transactionId
    )
    {
        /** @var Mage_Sales_Model_Order_Payment_Transaction $transaction */
        $transaction = Mage::getModel('sales/order_payment_transaction');

        $transaction
            ->setTxnId($transactionId)
            ->setOrderPaymentObject($payment)
            ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE)
            ->isFailsafe(1);

        if ($payment->hasIsTransactionClosed()) {
            $transaction->setIsClosed((int)$payment->getIsTransactionClosed());
        }

        //set transaction addition information
        if ($payment->getTransactionAdditionalInfo()) {
            foreach ($payment->getTransactionAdditionalInfo() as $key => $value) {
                $transaction->setAdditionalInformation($key, $value);
            }
        }

        $transaction->save();

        // link with sales entities
        $payment->setLastTransId($transactionId);
        $payment->setCreatedTransaction($transaction);
        $payment->getOrder()->addRelatedObject($transaction);

        return $this;
    }

    public function registerPaymentCapture(Mage_Sales_Model_Order $order, $transactionId, $amount, array $post)
    {
        $payment = $order->getPayment();

        foreach ($post as $key=>$value){
            $payment->setTransactionAdditionalInfo($key, $value);
        }

        $payment
            ->setTransactionId($transactionId)
            ->setShouldCloseParentTransaction(true)
            ->setIsTransactionClosed(true)
            ->setIsTransactionPending(false)
            ->save()
            ;

        $invoice = $order->prepareInvoice();
        $invoice->register();
        $order
            ->setIsCustomerNotified(false)
            ->setIsInProcess(true)
            ->save()
            ;


        $formattedAmount = $order->getOrderCurrency()->format($amount, null, false);

        $this->_addTransaction($order, $invoice, $payment, $transactionId);

        $order
            ->addStatusHistoryComment(
                $this->_helper()->__(
                    "Registered notification about captured amount of %s. Transaction ID: %s.",
                    $formattedAmount,
                    $transactionId
                )
            )
            ->setIsCustomerNotified(false)
            ->save();

        /** @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        $transactionSave
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save()
        ;



        # Notify customer

        if ($invoice && !$order->getEmailSent()) {
            $order
                ->sendNewOrderEmail()
                ->addStatusHistoryComment(
                    $this->_helper()->__('Notified customer about invoice #%s.', $invoice->getIncrementId())
                )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }
}
