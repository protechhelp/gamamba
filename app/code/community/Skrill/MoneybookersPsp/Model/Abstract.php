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

abstract class Skrill_MoneybookersPsp_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_TYPE_PREAUTHORIZE = 'PA';
    const PAYMENT_TYPE_CREDIT       = 'CD';
    const PAYMENT_TYPE_DEBIT        = 'DB';
    const PAYMENT_TYPE_CAPTURE      = 'CP';
    const PAYMENT_TYPE_REFUND       = 'RF';
    const PAYMENT_TYPE_REBILL       = 'RB';
    const PAYMENT_TYPE_REVERSAL     = 'RV';
    const PAYMENT_TYPE_CHARGEBACK   = 'CB';
    const PAYMENT_TYPE_RECIEPT      = 'RC';

    const PAYMENT_TYPE_REGISTRATION = 'RG';

    const PROCESSING_RESULT_OK = 'ACK';
    const PROCESSING_RESULT_NOK = 'NOK';

    const PROCESSING_STATUS_CODE_NEW = 90;
    const PROCESSING_STATUS_CODE_WAITING = 80;
    const PROCESSING_STATUS_CODE_REJECTED_VALIDATION = 70;
    const PROCESSING_STATUS_CODE_REJECTED_RISK = 65;
    const PROCESSING_STATUS_CODE_REJECTED_BANK = 60;
    const PROCESSING_STATUS_CODE_NEUTRAL = 40;
    const PROCESSING_STATUS_CODE_SUCCESS = 00;
    
    const REFRESH_PMETHOD_AVAILABILITY_PERIOD   = 900;

    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     **/
    protected $_code;

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = false;
    protected $_canRefundInvoicePartial = true;

    protected $_paymentMethod;
    protected $_defaultLocale		= 'en';
    protected $_supportedLocales        = array('de', 'en');
    protected $_availableMethods        = array();

    protected $_formBlockType = 'moneybookerspsp/form';
    protected $_infoBlockType = 'moneybookerspsp/info';

    protected $_registerFormUrl;
    protected $_debitFormUrl;

    protected $_formJsPath = 'skrill/moneybookerspsp/init.js';
    protected $_formCssPath = 'css/skrill/moneybookerspsp/form.css';

    /**
     * Retrieve information from payment configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        
        $code = $this->getCode();
        $path = 'moneybookerspsp/';
        if ($code == 'moneybookerspsp_va_psc') // PaySafe Card configuration
    	    $path = 'paysafecard/';
        $path .= $code . '/' . $field;
        
        return Mage::getStoreConfig($path, $storeId);
    }

    public function getCommonConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }

        $path = 'moneybookerspsp/settings/'.$field;
        return Mage::getStoreConfig($path, $storeId);
    }
    
    public function getStoreWebSiteConfigData ($field, $store = null)
    {
        if (!$store)
            $store = $this->getStore();
        $website = $store->getWebsite();
        
        return $website->getConfig('moneybookerspsp/settings/' . $field);
    }

    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)){
            return false;
        }
        return $this->_isAvailable($quote);
    }

    protected function _isAvailable($quote = null)
    {
        return (bool)$this->getWPFRegisterFormUrl();
    }

    protected function _getStatusUrl()
    {
        return Mage::getUrl('moneybookerspsp/processing/status');
    }

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * @return Skrill_MoneybookersPsp_Model_Api
     */
    protected function _getApi()
    {
        return Mage::getSingleton('moneybookerspsp/api');
    }

    protected function _initRequestParams($isOrderPlaced = true)
    {
        if (!$isOrderPlaced)
        {
            if (!($dataObject = Mage::registry('current_quote'))){
                $dataObject = Mage::getSingleton('checkout/session')->getQuote();
            }
            $info = $dataObject->getPayment();
        }else{
            $info = $this->getInfoInstance();
            $dataObject = $info->getOrder();
        }
        $billingAddress = $dataObject->getBillingAddress();

        $this->setStore($dataObject->getStore());
        
        $sendbaseamount = $this->getCommonConfigData('sendbaseamount');

        $params = array(
                //'autoCapture'           =>  ($this->getConfigData('payment_action') == self::ACTION_AUTHORIZE_CAPTURE) ? 'true' : 'false',
                'REQUEST.VERSION'       =>  '1.0',
                'SECURITY.SENDER'       =>  $sendbaseamount ?   $this->getStoreWebSiteConfigData('sender', $dataObject->getStore()) :
                                                                $this->getCommonConfigData('sender'),
                'SECURITY.TOKEN'        =>  $sendbaseamount ?   $this->getStoreWebSiteConfigData('token', $dataObject->getStore()) :
                                                                $this->getCommonConfigData('token'),
                'USER.LOGIN'            =>  $sendbaseamount ?   $this->getStoreWebSiteConfigData('login', $dataObject->getStore()) :
                                                                $this->getCommonConfigData('login'),
                'USER.PWD'              =>  $sendbaseamount ?   $this->getStoreWebSiteConfigData('password', $dataObject->getStore()) :
                                                                $this->getCommonConfigData('password'),
                'TRANSACTION.CHANNEL'   =>  $sendbaseamount ?   $this->getStoreWebSiteConfigData('channel', $dataObject->getStore()) :
                                                                $this->getCommonConfigData('channel'),
                'TRANSACTION.RESPONSE'  =>  'SYNC',
                'TRANSACTION.MODE'      =>  ($sendbaseamount ?   $this->getStoreWebSiteConfigData('test_mode', $dataObject->getStore()) :
                                            $this->getCommonConfigData('test_mode')) ? 'CONNECTOR_TEST' : 'LIVE',
                
                'IDENTIFICATION.TRANSACTIONID'  =>  $dataObject->getId() . '_' . $this->getCode(),

                'PRESENTATION.USAGE'    =>  $dataObject->getIncrementId(),
                // Alternative send web site base amount and currency instead the one for the specific store view
                'PRESENTATION.AMOUNT'   =>  $sendbaseamount ?   round($dataObject->getBaseGrandTotal(), 2) :
                                                                round($dataObject->getGrandTotal(),2),
                'PRESENTATION.CURRENCY' =>  $sendbaseamount ?   $dataObject->getStore()->getBaseCurrencyCode() :
                                                                $dataObject->getStore($dataObject->getSoreId())->getCurrentCurrencyCode(),
                'NAME.SALUTATION'       =>  null,
                'NAME.TITLE'            =>  null,
                'NAME.COMPANY'          =>  $billingAddress->getCompany(),
                'NAME.GIVEN'            =>  $billingAddress->getFirstname(),
                'NAME.FAMILY'           =>  $billingAddress->getLastname(),
                'CONTACT.EMAIL'         =>  $dataObject->getCustomerEmail(),
                'CONTACT.MOBILE'        =>  $billingAddress->getTelephone(),
                'CONTACT.PHONE'         =>  $billingAddress->getFax(),
                'CONTACT.IP'            =>  $dataObject->getRemoteIp(),
                'ADDRESS.STREET'        =>  $billingAddress->getStreet(-1),
                'ADDRESS.CITY'          =>  $billingAddress->getCity(),
                'ADDRESS.STATE'         =>  $billingAddress->getRegion(),
                'ADDRESS.COUNTRY'       =>  $billingAddress->getCountry(),
                'ADDRESS.ZIP'           =>  $billingAddress->getPostcode(),
                //'ACCOUNT.NUMBER'        =>  $info->decrypt($this->getInfoInstance()->getCcNumberEnc());
                //'ACCOUNT.HOLDER'        =>  $info->getCcOwner(),
                'ACCOUNT.REGISTRATION'  =>  $info->getPoNumber(),
                'ACCOUNT.ID'            =>  $dataObject->getCustomerEmail(),

                'FRONTEND.ENABLED'      =>  'false',
                'FRONTEND.POPUP'        =>  'false',
                'FRONTEND.MODE'         =>  'DEFAULT',
                'FRONTEND.STATUSBAR_VISIBLE'    =>  'false',
                'FRONTEND.RETURN_ACCOUNT'   =>  'true',
                'FRONTEND.REDIRECT_TIME'    =>  '0',
                'FRONTEND.LANGUAGE'     =>  $this->getLocale($dataObject->getStoreId()),
                'FRONTEND.RESPONSE_URL' =>  $this->_getStatusUrl(),
                'FRONTEND.SESSION_ID'   => $this->_getCheckout()->getSessionId(),
                'FRONTEND.JSCRIPT_PATH' => $dataObject->getStore()->getStoreConfig('general/country/default') == 'JP' ?
                                    	    Mage::helper('core/js')->getJsUrl('skrill/moneybookerspsp/init_jp.js') :
                                            Mage::helper('core/js')->getJsUrl($this->_formJsPath), 
                'FRONTEND.CSS_PATH'     =>  $this->_formCssPath ? Mage::getDesign()->getSkinUrl($this->_formCssPath) : '',
                'CRITERION.MONEYBOOKERS_hide_login'     =>   '1',
                'FRONTEND.COLLECT_DATA' =>  'true',
                'CRITERION.MONEYBOOKERS_recipient_description'  => $dataObject->getStore($dataObject->getSoreId())->getName(),
//                'Timestamp'     =>  Mage::app()->getLocale()->date(time())->toString('YYYY-MM-ddTHH:mm:ss'),
        );

        if (!empty($this->_availableMethods)){
            $params['FRONTEND.PM.DEFAULT_DISABLE_ALL'] = 'true';
            $i=1;
            foreach ($this->_availableMethods as $key => $method){
                $params["FRONTEND.PM.$i.ENABLED"] = 'true';
                if (is_array($method)){
                    $params["FRONTEND.PM.$i.SUBTYPES"] = implode(',', $method);
                    $method = $key;
                }
                $params["FRONTEND.PM.$i.METHOD"] = $method;
                $i++;
            }
        }

        return $params;
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);

        if (!$payment->getPoNumber()){
            Mage::throwException(Mage::helper('moneybookerspsp')->__('Moneybookers transaction failed: account data is missing.'));
        }

        $params = $this->_initRequestParams();
        $params['PAYMENT.CODE'] = $this->_getPaymentCode(self::PAYMENT_TYPE_PREAUTHORIZE);
        $params['TRANSACTION.RESPONSE'] = 'ASYNC';

        // make API call
        $response = $this->_getApi()->request($params);
        $this->_processResponse($params, $response, $payment);

        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $sendbaseamount = $this->getCommonConfigData('sendbaseamount');
        if (!$sendbaseamount)
        {
            // Multiple currencies, multiple channels fix
            $amount = $amount * $payment->getOrder()->getBaseToOrderRate();
        }
        parent::capture($payment, $amount);

        if (!$payment->getPoNumber() && $payment->getLastTransId()){
            Mage::throwException(Mage::helper('moneybookerspsp')->__('Moneybookers transaction failed: account data is missing.'));
        }

        $params = $this->_initRequestParams();
        
        if ($sendbaseamount)
        {
            $amount = $amount >= $payment->getOrder()->getBaseGrandTotal() ?
                                $payment->getOrder()->getBaseGrandTotal() : $amount;
            $params['PRESENTATION.CURRENCY'] = $payment->getOrder()->getBaseCurrencyCode();
        }
        else
        {
            $amount = $amount >= $payment->getOrder()->getGrandTotal() ?
                                $payment->getOrder()->getGrandTotal() : $amount;
            // Multiple currencies, multiple channels fix
            $params['PRESENTATION.CURRENCY'] = $payment->getOrder()->getOrderCurrencyCode();
        }
        $params['PRESENTATION.AMOUNT'] = round($amount,2);
        
        $params['TRANSACTION.RESPONSE'] = 'ASYNC';

        if ($payment->getLastTransId()) {
            $params['PAYMENT.CODE'] = $this->_getPaymentCode(self::PAYMENT_TYPE_CAPTURE);
            $params['IDENTIFICATION.REFERENCEID'] = $payment->getLastTransId();
        }else{
            $params['PAYMENT.CODE'] = $this->_getPaymentCode(self::PAYMENT_TYPE_DEBIT);
        }

        $this->_getApi()->setStore($this->getStore());
        $response = $this->_getApi()->request($params);
        $this->_processResponse($params, $response, $payment);

        return $this;
    }

    /**
     * Refund money
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $sendbaseamount = $this->getCommonConfigData('sendbaseamount');
        if (!$sendbaseamount)
        {
            // Multiple currencies, multiple channels fix
            $amount = $amount * $payment->getOrder()->getBaseToOrderRate();
        }
        parent::refund($payment, $amount);

        $params = $this->_initRequestParams();
        
        if ($sendbaseamount)
        {
            $amount = $amount >= $payment->getOrder()->getBaseGrandTotal() ?
                                $payment->getOrder()->getBaseGrandTotal() : $amount;
            $params['PRESENTATION.CURRENCY'] = $payment->getOrder()->getBaseCurrencyCode();
        }
        else
        {
            $amount = $amount >= $payment->getOrder()->getGrandTotal() ?
                                $payment->getOrder()->getGrandTotal() : $amount;
            // Multiple currencies, multiple channels fix
            $params['PRESENTATION.CURRENCY'] = $payment->getOrder()->getOrderCurrencyCode();
        }
        $params['PRESENTATION.AMOUNT'] = round($amount,2);
        
        $params['PAYMENT.CODE'] = $this->_getPaymentCode(self::PAYMENT_TYPE_REFUND);
        $params['IDENTIFICATION.REFERENCEID'] = $payment->getLastTransId();

        $this->_getApi()->setStore($this->getStore());
        $response = $this->_getApi()->request($params);
        $this->_processResponse($params, $response, $payment);

        return $this;
    }

    /**
     * Cancel authorisation (void payment)
     */
    public function void(Varien_Object $payment)
    {
        parent::void($payment);

        // if same amount has already been captured, refund it
        if ($amount = $payment->getBaseAmountPaid()) {
            return $this->refund($payment, $amount);
        }

        if ($payment->getLastTransId()){
            $params = $this->_initRequestParams();
            $params['PAYMENT.CODE'] = $this->_getPaymentCode(self::PAYMENT_TYPE_REBILL);
            $params['IDENTIFICATION.REFERENCEID'] = $payment->getLastTransId();

            $response = $this->_getApi()->request($params);
            $this->_processResponse($params, $response, $payment);
        }

        return $this;
    }

    /**
     * Cancel payment (order)
     *
     * @param   Varien_Object
     * @return  Mage_Payment_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        return $this->void($payment);
    }

    /**
     * Return locale of frontend
     *
     * @return string
     */
    public function getLocale($storeId)
    {
        if (!is_null($storeId)){
            $locale = Mage::getModel('core/locale', Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId));
        }else{
            $locale = Mage::app()->getLocale();
        }
        $locale = explode('_', $locale->getLocaleCode());
        if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales)) {
            return $locale[0];
        }
        return $this->_defaultLocale;
    }

    public function getWPFDebitFormUrl($isOrderPlaced = true)
    {
        if (!isset($this->_debitFormUrl)) {
            $this->_debitFormUrl = $this->getWPFFormUrl(self::PAYMENT_TYPE_DEBIT, $isOrderPlaced);
        }
        return $this->_debitFormUrl;
    }

    public function getWPFRegisterFormUrl()
    {
        if (!isset($this->_registerFormUrl)) {
            $this->_registerFormUrl = $this->getWPFFormUrl(self::PAYMENT_TYPE_REGISTRATION);
        }
        return $this->_registerFormUrl;
    }

    public function getWPFFormUrl($action, $isOrderPlaced = false)
    {
        try {
            $params = $this->_initRequestParams($isOrderPlaced);
            $params['PAYMENT.CODE'] = $this->_getPaymentCode($action);
            $params['FRONTEND.ENABLED'] = 'true';
            $params['ACCOUNT.REGISTRATION'] = '';

            $this->_getApi()->setStore($this->getStore());
            $result = $this->_getApi()->processWPFRequest($params);
            $result = $this->_getApi()->parseWPFResponse($result);
            if (isset($result['POST.VALIDATION'])
                    && $result['POST.VALIDATION'] == 'ACK'
                    && isset($result['FRONTEND.REDIRECT_URL'])) {
                return $result['FRONTEND.REDIRECT_URL'];
            }else {
                return false;
                //Mage::throwException('Bad MoneybookersPSP server response');
            }
        }catch(Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    /**
    * Processes the web service response
    *
    * @param	string	request function that has been called
    * @param	mixed	result of the request
    * @return	boolean	true on "success", false if the request result shoudl be regarded as "failed"
    */
    protected function _processResponse($request, $result, Varien_Object $payment)
    {
        $this->_beforeProcessResponse($request, $result, $payment);
        $action=explode('.',$result->Transaction->Payment['code']);
        if (!isset($action[1])){
            Mage::throwException(Mage::helper('moneybookerspsp')->__('MoneybookersPSP: wrong payment system response'));
        }
        $method=strtoupper($action[0]);
        $action=strtoupper($action[1]);
        switch ($action)
        {
            case self::PAYMENT_TYPE_REGISTRATION:
                return;
            case self::PAYMENT_TYPE_DEBIT:
	    case self::PAYMENT_TYPE_PREAUTHORIZE:
            case self::PAYMENT_TYPE_CAPTURE:
            case self::PAYMENT_TYPE_REFUND:
            case self::PAYMENT_TYPE_REVERSAL:
            case self::PAYMENT_TYPE_REBILL:
                if ((string)$result->Transaction->Processing->Result == 'ACK') {
                    $payment->setLastTransId((string)$result->Transaction->Identification->UniqueID);
                    return true;
                } else{
                    Mage::throwException($result->Transaction->Processing->Return.' ('.$result->Transaction->Processing->Return['code'].')');
                }
                break;
            case 'ER':
                Mage::throwException($result->Transaction->Processing->Return.' ('.$result->Transaction->Processing->Return['code'].')');
                break;
            default:
                Mage::log(Mage::helper('moneybookerspsp')->__('MoneybookersPSP unhandled web service response %s.',print_r($result,1)));
        }
        return false;
    }

    /**
     * Success response processor
     *
     * @param string $request
     * @param mixed $result
     * @param Varien_Object $payment
     *
     */
    protected function _beforeProcessResponse($request, $result, Varien_Object $payment)
    {
        $action = explode('.',$request['PAYMENT.CODE']);
        if (!isset($action[1])){
            Mage::throwException(Mage::helper('moneybookerspsp')->__('MoneybookersPSP: Wrong response data, method is missing'));
        }
        $method = strtoupper($action[0]);
        $action = strtoupper($action[1]);
        switch ((string)$result->Transaction->Processing->Result){
            case 'ACK':
                switch ($action){
                    case self::PAYMENT_TYPE_PREAUTHORIZE:
			if ((string)$result->Transaction->Processing->Result == 'ACK') {
			    $payment->setLastTransId((string)$result->Transaction->Identification->UniqueID);
			    $payment->setCcTransId((string)$result->Transaction->Identification->UniqueID)
				    ->setAmountAuthorized($payment->getOrder()->getTotalDue())
				    ->setBaseAmountAuthorized($payment->getOrder()->getBaseTotalDue())
				    ->setBaseAmountPaid(0)
				    ->setAmountPaid(0);
			    $payment->save();
			    $payment->getOrder()->setCustomerNote(Mage::helper('moneybookerspsp')->__('Payment has been preauthorized.'));
			    return true;
			} else{
			    Mage::throwException($result->Transaction->Processing->Return.' ('.$result->Transaction->Processing->Return['code'].')');
			}
                        break;
                    case self::PAYMENT_TYPE_DEBIT:
			if ((string)$result->Transaction->Processing->Result == 'ACK') {
			    $payment->setLastTransId((string)$result->Transaction->Identification->UniqueID);
			    $payment->setCcTransId((string)$result->Transaction->Identification->UniqueID);
			    $payment->save();
			    $payment->getOrder()->setCustomerNote(Mage::helper('moneybookerspsp')->__('Payment has been authorized and captured.'));
			    return true;
			} else{
			    Mage::throwException($result->Transaction->Processing->Return.' ('.$result->Transaction->Processing->Return['code'].')');
			}
                        break;
                    case self::PAYMENT_TYPE_CAPTURE:
                        $payment->getOrder()->setCustomerNote(Mage::helper('moneybookerspsp')->__('Payment has been captured.'));
                        break;
                    case self::PAYMENT_TYPE_REFUND:
                        $payment->getOrder()->setCustomerNote(Mage::helper('moneybookerspsp')->__('Payment has been refunded.'));
                        break;
                    case self::PAYMENT_TYPE_REBILL:
                        $payment->getOrder()->setCustomerNote(Mage::helper('moneybookerspsp')->__('Payment has been reversed.'));
                        break;
                }
                break;
            case 'NOK':
                switch ($action){
                    case self::PAYMENT_TYPE_PREAUTHORIZE:
                        break;
                    case self::PAYMENT_TYPE_DEBIT:
                        break;
                }
                break;
        }
    }

    public function getWPFRegisterFormHtml()
    {
        if ($url = $this->getWPFRegisterInnerFormUrl()){
            return "<iframe id='frontend_data' src='$url' allowtransparency='true' frameborder='0' scrolling='no' width='100%'></iframe>";
        }
    }

    public function getWPFRegisterInnerFormUrl()
    {
        if ($ulr = $this->getWPFRegisterFormUrl()) {
            $response = $this->_getApi()->processRequest(urldecode($ulr));
            if ($response) {
                preg_match('/<iframe([^>]*)>(.*)<\/iframe>/mis', $response, $matches);
                $parsedUrl = parse_url($ulr);
                $hostUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";
                if (isset($matches[0]) && preg_match('/src="([^"]*)"/', $matches[1], $matches)) {
                    return $hostUrl.$matches[1];
                }
            }
        }
        return '';
    }

    protected function _getPaymentCode($paymentType)
    {
        return $this->_paymentMethod. '.' . $paymentType;
    }

    public function get3DSFormUrl()
    {
        return Mage::getUrl('moneybookerspsp/processing/redirect/3ds/form');
    }
}