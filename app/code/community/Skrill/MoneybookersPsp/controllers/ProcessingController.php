<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Skrill
 * @package    Skrill_MoneybookersPsp
 * @copyright  Copyright (c) 2012 Skrill Holdings Ltd. (http://www.skrill.com)
 */
class Skrill_MoneybookersPsp_ProcessingController extends Mage_Core_Controller_Front_Action {

    protected $_responseBlockType = 'moneybookerspsp/response';
    protected $_quote;
    protected $_order;
    protected $_paymentInst;
    protected $_trustedIps = array('217.196.147.137', '213.131.239.51', '212.111.45.51');

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Payment has been canceled at moneybookerspsp.
     * Cancel order and redirect user to the shopping cart.
     */
    protected function _processCancel() {
        // cancel order

        //if ($this->_order->canCancel()) {
            if ($this->_order->hasInvoices())
            {
                $invoices = $this->_order->getInvoiceCollection();
                foreach ($invoices as $invoice)
                {
                    $invoice->cancel();
                    $invoice->save();
                }
            }
            $this->_order->cancel();
            $this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED,
                                              Mage::helper('moneybookerspsp')->__(
                                                    'The moneybookerspsp transaction has been canceled.'));
            $this->_order->setData('state', Mage_Sales_Model_Order::STATE_CANCELED);
            $this->_order->save();
        //}

        // set quote to active
        if ($quoteId = $this->_order->getQuoteId()) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            if ($quote->getId()) {
                $quote->setIsActive(true)->save();
            }
        }
    }

    protected function _getPendingPaymentStatus() {
        return Mage::helper('moneybookerspsp')->getPendingPaymentStatus();
    }

    public function ccformAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function registerSuccessAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function registerFailedAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function successAction() {
        $statusCheck = Mage::getConfig()->getNode("global/customsettings/status_check");

        if ($lastQuoteId = $this->_getCheckout()->getMoneybookersPspLastSuccessQuoteId()) {
            $this->_getCheckout()->setLastSuccessQuoteId($lastQuoteId);
            
            if ($statusCheck) {
                $realOrderId = $this->_getCheckout()->getLastRealOrderId();

                $order = Mage::getModel('sales/order')->loadByIncrementId($realOrderId);
                $state = $order->getState();
                if ($state != Mage_Sales_Model_Order::STATE_PROCESSING &&
                    $state != Mage_Sales_Model_Order::STATE_COMPLETE){
                    $this->_order = $order;
                    $this->_processCancel();
                    //$this->_redirect('moneybookerspsp/processing/failed');
                    $this->_redirect('checkout/cart');
                }
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function failedAction() {
        if ($quoteId = $this->_getCheckout()->getMoneybookersPspQuoteId()) {
            $this->_getCheckout()->setQuoteId($quoteId);
        }

        // check if there is order generated and it's canceled, if YES go to the shopping cart
        $realOrderId = $this->_getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($realOrderId);
        if ($order && $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED){
            $this->_redirect('checkout/cart');
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Process transaction status messages
     */
    public function statusAction() {
        try {
            if ($this->getRequest()->getPost('response')) {
                return $this->_process3dsResponse();
            }
            /** verify call */
            $data = $this->_getTransactionRequest();
            if ($this->_getApi()->getConfigData('debug')) {
                Mage::log('_getTransactionRequest():');
                Mage::log($data);
            }

            die($this->_processPaymentAction($data));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Mage::logException($e);
            Mage::log(Mage::helper('moneybookerspsp')->__('MoneybookersPSP transaction status update failed: %s', $msg));
            if (isset($this->_quote)) {
                $paymentMethod = $this->_quote->getPayment()->getMethodInstance();
                die($this->_getRegisterFailedRedirectUrl());
            } elseif (isset($this->_order) &&
                    $this->_order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                $this->_processCancel();

                die($this->_getFailedRedirectUrl());
            }
            die($this->_getFailedRedirectUrl());
        }

        $this->norouteAction();
        return;
    }

    protected function _processPaymentAction($data) {
        $paymentCode = explode('.', $this->_getPaymentCode($data));
        $paymentAction = $paymentCode[1];
        $redirectUrl = $this->_getRedirectUrl();

        switch ($paymentAction) {
            case 'RG':
                $paymentData = $this->_getPaymentData($data);
                
                $this->_quote->getPayment()->setQuote(Mage::registry('current_quote'));
                $this->_quote->getPayment()->importData($paymentData);
                $this->_quote->save();
                $redirectUrl = $this->_getRegisterSuccessRedirectUrl();
                break;
            case 'DB':
                $payment = $this->_order->getPayment()->getMethodInstance();
                
                $this->_order->setState(
                        Mage_Sales_Model_Order::STATE_PROCESSING, $payment->getConfigData('order_status', $this->_order->getStoreId()), Mage::helper('moneybookerspsp')->__('Payment debited successfully')
                );
                $paymentData = $this->_getPaymentData($data);

                // set transaction ID
                try {
                    $this->_order->getPayment()
                            ->setLastTransId($paymentData['po_number'])
                            ->setCcTransId($paymentData['po_number'])
                            ->setAmountAuthorized($this->_order->getTotalDue())
                            ->setBaseAmountAuthorized($this->_order->getBaseTotalDue())
                            ->capture(null);

                    if ($paymentCode[0] == 'VA') // forcefully set the invoice to PAID
                    {
                        $invoices = $this->_order->getInvoiceCollection();
                        foreach ($invoices as $invoice)
                        {
                            $invoice->setTransactionId($paymentData['po_number']);
                            $invoice->pay();
                        }
                    }
                } catch (Mage_Core_Exception $e) {
                    //Do nothing if the Magento version is below 1.4.0
                    if (!version_compare(Mage::getVersion(), '1.4.0', '<')) {
                        throw $e;
                    }
                }

                // send new order email to customer
                $this->_order->sendNewOrderEmail()->setEmailSent(true)->save();
                $redirectUrl = $this->_getSuccessRedirectUrl();
                break;
            case 'PA' :
                $payment = $this->_order->getPayment()->getMethodInstance();
                $paymentData = $this->_getPaymentData($data);
                
                if ($paymentCode[0] == 'VA') // PaySafe Card fix
                {
                    $this->_order->setState(
                            Mage_Sales_Model_Order::STATE_PROCESSING,
                            $payment->getConfigData('order_status',
                                                    $this->_order->getStoreId()),
                                                    Mage::helper('moneybookerspsp')->__('Payment processed successfully with PaySafe Card')
                    );
                
                    $this->_order->getPayment()
                            ->setLastTransId($paymentData['po_number'])
                            ->setCcTransId($paymentData['po_number'])
                            ->setAmountAuthorized($this->_order->getTotalDue())
                            ->setBaseAmountAuthorized($this->_order->getBaseTotalDue())
                            ->capture(null);

                    $invoices = $this->_order->getInvoiceCollection();
                    foreach ($invoices as $invoice)
                    {
                        $invoice->pay();
                    }
                }
                else
                {
                    $order = Mage::getModel('sales/order');
                    $order->loadByIncrementId($session->getLastRealOrderId());
                    if (!$order->getId()) {
                        Mage::throwException(Mage::helper('moneybookerspsp')->__('An error occured during the payment process: Order not found.'));
                    }

                    $order->setTotalPaid(0);
                    $order->setBaseTotalPaid(0);
                    $order->getPayment()
                                ->setLastTransId($paymentData['po_number'])
                                ->setCcTransId($paymentData['po_number'])
                                ->setAmountAuthorized($order->getTotalDue())
                                ->setBaseAmountAuthorized($order->getBaseTotalDue())
                                ->setBaseAmountPaid(0)
                                ->setAmountPaid(0);
                    $order->save();
                }
                $this->_order->sendNewOrderEmail()->setEmailSent(true)->save();
                $redirectUrl = $this->_getSuccessRedirectUrl();

                break;
            default:
        }
        return $redirectUrl;
    }

    protected function _getPaymentCode($data) {
        if ($data instanceof SimpleXmlElement) {
            return (string) $data->Transaction->Processing->Status->attributes()->{'code'};
        } else {
            return $data['PAYMENT_CODE'];
        }
    }

    protected function _process3dsOrder ($xml)
    {
        if ($this->_getApi()->getConfigData('debug')) {
            Mage::log('_process3dsOrder():');
            Mage::log($this->getRequest()->getPost());
        }
	$paymentCode = current($xml->xpath('Transaction/Payment'));
	$paymentCode = (string) $paymentCode->attributes()->{'code'};
	$paymentCode = explode('.', $paymentCode);

	try {
	    $session = $this->_getCheckout();

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException(Mage::helper('moneybookerspsp')->__('An error occured during the payment process: Order not found.'));
            }

	    $payment = $order->getPayment()->getMethodInstance();
    	    $paymentData = $this->_getPaymentData($xml);
            
            $order->setTotalPaid(0);
            $order->setBaseTotalPaid(0);
    	    $order->getPayment()
                	->setLastTransId($paymentData['po_number'])
            		->setCcTransId($paymentData['po_number'])
                	->setAmountAuthorized($order->getTotalDue())
                	->setBaseAmountAuthorized($order->getBaseTotalDue())
                        ->setBaseAmountPaid(0)
                        ->setAmountPaid(0);
            $order->save();
            
            if ($order->hasInvoices())
            {
                $invoices = $order->getInvoiceCollection();
                foreach ($invoices as $invoice)
                {
                    $invoice->pay();
                    $invoice->save();
                }
            }
            
            $message = ($paymentCode[1] == Skrill_MoneybookersPsp_Model_Abstract::PAYMENT_TYPE_PREAUTHORIZE) ?
                                        Mage::helper('moneybookerspsp')->__('Payment has been preauthorized.') :
                                        Mage::helper('moneybookerspsp')->__('Payment debited successfully');
            $order->setState(
        	    Mage_Sales_Model_Order::STATE_PROCESSING,
        	    $payment->getConfigData('order_status', $order->getStoreId()),
		    $message);
    	    $order->sendNewOrderEmail()->setEmailSent(true);
            $order->save();
	} catch (Exception $e) {
	    Mage::log(Mage::helper('moneybookerspsp')->__('Order not found!'));
	}

	return $this->_getSuccessRedirectUrl();
    }

    protected function _process3dsResponse() {
        if ($this->_getApi()->getConfigData('debug')) {
            Mage::log('_process3dsResponse():');
            Mage::log($this->getRequest()->getPost());
        }
        $hlp = Mage::helper('moneybookerspsp');
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('moneybookerspsp_processing_threeds_redirect');
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $root = $this->getLayout()->getBlock('root');
        try {
            $xml = $this->_validate3dsResponse();

            $root->setRedirectUrl($this->_process3dsOrder($xml));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Mage::logException($e);
            Mage::log(Mage::helper('moneybookerspsp')->__('MoneybookersPSP transaction status update failed: %s', $msg));
            $root->setRedirectUrl($this->_getFailedRedirectUrl());
            $this->_processCancel();
        }
        $this->renderLayout();
        return;
    }

    protected function _validate3dsResponse() {
        $response = urldecode($this->getRequest()->getPost('response'));
        $xml = $this->_getApi()->processXmlResponse($response);

        if (!$xml->xpath('Transaction/Identification/TransactionID')
                || !$xml->xpath('Transaction/Processing/Result')) {
            Mage::throwException(Mage::helper('moneybookerspsp')->__('MoneybookersPSP 3DS: Wrong XML request received'));
        }

        $transactionId = (string) current($xml->xpath('Transaction/Identification/TransactionID'));
        $transactionDetails = explode('_', $transactionId);
        if (count($transactionDetails) != 3) {
            Mage::throwException(Mage::helper('moneybookerspsp')->__('MoneybookersPSP 3DS: Wrong transaction ID'));
        }
        $orderId = $transactionDetails[0];
        $this->_order = Mage::getModel('sales/order')->load($orderId);
        if (!$this->_order->getId()) {
            Mage::throwException(Mage::helper('moneybookerspsp')->__('MoneybookersPSP 3DS: Order not found'));
        }

        $result = (string)current($xml->xpath('Transaction/Processing/Result'));
        if ($result != Skrill_MoneybookersPsp_Model_Abstract::PROCESSING_RESULT_OK) {
            Mage::throwException(Mage::helper('moneybookerspsp')->__('3DS Transaction failed'));
        }
        return $xml;
    }

    /**
     * Checking POST variables and sets order ($order) and payment instance ($paymentInst).
     *
     * @return	array	Checked POST variables.
     */
    protected function _getTransactionRequest() {
        if (!$this->getRequest()->isPost()) {
            Mage::throwException('MoneybookersPSP: Wrong request type.');
        }

        // validate request ip coming from Moneybookers subnet
        $helper = Mage::helper('core/http');
        if (method_exists($helper, 'getRemoteAddr')) {
            $remoteAddr = $helper->getRemoteAddr();
        } else {
            $request = $this->getRequest()->getServer();
            $remoteAddr = $request['REMOTE_ADDR'];
        }

        if (!in_array($remoteAddr, $this->_trustedIps)) {
            Mage::throwException('IP can\'t be validated as Moneybookers-IP.');
        }

        $data = $this->getRequest()->getPost();
        // SKIPPING validate response hash
        if (!is_array($data) || !isset($data['HASH']) || $this->_getApi()->generateHash($data) != $data['hash']) {
            //Mage::throwException('MoneybookersPSP: Invalid response hash.');
        }

        if (!isset($data['IDENTIFICATION_TRANSACTIONID']) || !isset($data['PROCESSING_RESULT'])) {
            Mage::throwException('MoneybookersPSP: Invalid response data.');
        }

        $quoteId = explode('_', $data['IDENTIFICATION_TRANSACTIONID'], 2);
        if (!count($quoteId) == 2) {
            Mage::throwException('MoneybookersPSP: Invalid response data.');
        }

        $method = $quoteId[1];
        $quoteId = $quoteId[0];

        if (in_array($method, $this->_getRegisterMethods())) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            if (!$quote->getId()) {
                Mage::throwException("MoneybookersPSP: unable to load quote #$quoteId.");
            }
            $quote->getPayment()->setMethod($method);
            $this->_quote = $quote;
            Mage::register('current_quote', $quote);
        } elseif (in_array($method, $this->_getDebitMethods())) {
            $order = Mage::getModel('sales/order')->load($quoteId);
            if (!$order->getId()) {
                Mage::throwException("MoneybookersPSP: unable to load order #$quoteId.");
            }
            //$order->getPayment()->setMethod($method);
            $this->_order = $order;
            Mage::register('current_order', $order);
        } else {
            Mage::throwException('MoneybookersPSP: Unknown payment method.');
        }
        if (($data['PROCESSING_RESULT']) != 'ACK') {
            Mage::throwException('MoneybookersPSP: Transaction falied.');
        }

        return $data;
    }

    protected function _getApi() {
        return Mage::getSingleton('moneybookerspsp/api');
    }

    protected function _getPaymentData($data) {
        if ($data instanceof SimpleXMLElement) {
            return $this->_getXmlPaymentData($data);
        } else {
            return $this->_getPostPaymentData($data);
        }
    }

    protected function _getPostPaymentData($data) {
        $quoteId = explode('_', $data['IDENTIFICATION_TRANSACTIONID'], 2);
        $paymentData = array(
            'quote_id' => $quoteId[0],
            'method' => $quoteId[1]);

        $paymentDataMap = array(
            'cc_type' => 'ACCOUNT_BRAND',
            'cc_owner' => 'ACCOUNT_HOLDER',
            'cc_last4' => 'ACCOUNT_NUMBER',
            'cc_number_enc' => 'ACCOUNT_BANK',
            'cc_exp_month' => 'ACCOUNT_EXPIRY_MONTH',
            'cc_exp_year' => 'ACCOUNT_EXPIRY_YEAR',
            'po_number' => 'IDENTIFICATION_UNIQUEID',
        );
        foreach ($paymentDataMap as $paymentKey => $dataKey) {
            $paymentData[$paymentKey] = isset($data[$dataKey]) ? $data[$dataKey] : '';
        }
        $method = explode('.', $data['PAYMENT_CODE']);
        $paymentData['cc_number_enc'] = $method[0] .
                (!empty($paymentData['cc_number_enc']) ? '_' . $paymentData['cc_number_enc'] : '');

        return $paymentData;
    }

    protected function _getXmlPaymentData($data) {
        $transactionId = (string) current($data->xpath('Transaction/Identification/TransactionID'));
        $quoteId = explode('_', $transactionId);
        $paymentData = array(
            'quote_id' => $quoteId[0],
            'method' => $quoteId[1]);

        $paymentDataMap = array(
            'cc_type' => 'ACCOUNT_BRAND',
            'cc_owner' => 'ACCOUNT_HOLDER',
            'cc_last4' => 'ACCOUNT_NUMBER',
            'cc_number_enc' => 'ACCOUNT_BANK',
            'cc_exp_month' => 'ACCOUNT_EXPIRY_MONTH',
            'cc_exp_year' => 'ACCOUNT_EXPIRY_YEAR',
            'po_number' => 'Transaction/Identification/UniqueID',
        );
        foreach ($paymentDataMap as $paymentKey => $dataKey) {
            $value = $data->xpath($dataKey);
            $paymentData[$paymentKey] = $value ? current($value) : '';
        }
        $method = explode('.', $this->_getPaymentCode($data));
        $paymentData['cc_number_enc'] = $method[0] .
                (!empty($paymentData['cc_number_enc']) ? '_' . $paymentData['cc_number_enc'] : '');

        return $paymentData;
    }

    public function agentAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _getSuccessRedirectUrl() {
        return Mage::getUrl('moneybookerspsp/processing/success');
    }

    protected function _getFailedRedirectUrl() {
        return Mage::getUrl('moneybookerspsp/processing/failed');
    }

    protected function _getRegisterSuccessRedirectUrl() {
        return Mage::getUrl('moneybookerspsp/processing/registerSuccess');
    }

    protected function _getRegisterFailedRedirectUrl() {
        return Mage::getUrl('moneybookerspsp/processing/registerFailed');
    }

    protected function _getRedirectUrl() {
        return Mage::getUrl('moneybookerspsp/processing/ccform');
    }

    /**
     * Show orderPlaceRedirect page which contains the Moneybookers iframe.
     */
    public function redirectAction() {
        try {
            $session = $this->_getCheckout();

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException(Mage::helper('moneybookerspsp')->__('An error occured during the payment process: Order not found.'));
            }

            $update = $this->getLayout()->getUpdate();

            $update->addHandle('default');
            $this->addActionLayoutHandles();

            if ($this->getRequest()->getParam('3ds') &&
                    $order->getPayment()->getAdditionalInformation()) {
                $redirectMessage = Mage::helper('moneybookerspsp')->__('Customer was redirected to 3D-Secure server.');
                $update->addHandle('moneybookerspsp_processing_redirect_3ds_form');

                $invoices = $order->getInvoiceCollection();
                foreach ($invoices as $invoice)
                {
                    $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
                }
            } else {
                $redirectMessage = Mage::helper('moneybookerspsp')->__('Customer was redirected to Moneybookers.');
            }
            if ($order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                $order->setState(
                        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $this->_getPendingPaymentStatus(), $redirectMessage
                )->save();

                if ($order->hasInvoices())
                {
                    $invoices = $order->getInvoiceCollection();
                    foreach ($invoices as $invoice)
                    {
                        $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
                        $invoice->save();
                    }
                }
            }
            Mage::register('current_order', $order);

            if ($session->getQuoteId() && $session->getLastSuccessQuoteId()) {
                $session->setMoneybookersPspQuoteId($session->getQuoteId());
                $session->setMoneybookersPspLastSuccessQuoteId($session->getLastSuccessQuoteId());
                $session->setMoneybookersPspRealOrderId($session->getLastRealOrderId());
                $session->getQuote()->setIsActive(false)->save();
                $session->clear();
            }

            $this->loadLayoutUpdates();
            $this->generateLayoutXml()
                    ->generateLayoutBlocks();

            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    public function threedsAction() {
        try {
            $session = $this->_getCheckout();

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException(Mage::helper('moneybookerspsp')->__('An error occured during the payment process: Order not found.'));
            }
            Mage::register('current_order', $order);
            $this->loadLayout();
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    protected function _getRegisterMethods() {
        return array('moneybookerspsp_cc', 'moneybookerspsp_elv');
    }

    protected function _getDebitMethods() {
        return array('moneybookerspsp_va',
    		    'moneybookerspsp_va_idl',
    		    'moneybookerspsp_va_sft',
    		    'moneybookerspsp_va_pwy',
    		    'moneybookerspsp_va_ent',
    		    'moneybookerspsp_va_ebt',
    		    'moneybookerspsp_va_csi',
    		    'moneybookerspsp_va_did',
    		    'moneybookerspsp_va_psp',
    		    'moneybookerspsp_va_dnk',
    		    'moneybookerspsp_va_gir',
    		    'moneybookerspsp_va_pli',
    		    'moneybookerspsp_va_obt',
    		    'moneybookerspsp_va_so2',
    		    'moneybookerspsp_va_npy',
    		    'moneybookerspsp_va_mae',
    		    'moneybookerspsp_va_lsr',
    		    'moneybookerspsp_va_gcb',
    		    'moneybookerspsp_va_ent',
    		    'moneybookerspsp_va_amx',
    		    'moneybookerspsp_va_jcb',
    		    'moneybookerspsp_va_fbe',
                    'moneybookerspsp_va_psc');
    }

}