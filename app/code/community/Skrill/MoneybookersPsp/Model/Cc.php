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

class Skrill_MoneybookersPsp_Model_Cc extends Skrill_MoneybookersPsp_Model_Abstract
{
    protected $_code = 'moneybookerspsp_cc';

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

    protected $_paymentMethod           = 'CC';
    protected $_defaultLocale		= 'en';
    protected $_supportedLocales        = array('de', 'en');
    //protected $_availableMethods      = array('DD', 'CT', 'PP', 'IV', 'CC', 'DC');
    protected $_availableMethods        = array('CC');

    protected $_formBlockType = 'moneybookerspsp/form_cc';
    protected $_infoBlockType = 'moneybookerspsp/info_cc';

    protected static $_orderPlaceRedirectUrl = null;

    protected function _initRequestParams($isOrderPlaced = true)
    {
        $params = parent::_initRequestParams($isOrderPlaced);
        $params['TRANSACTION.RESPONSE'] = 'ASYNC';
        return $params;
    }

    protected function _beforeProcessResponse($request, $result, Varien_Object $payment)
    {
        try {
            $action = explode('.',$request['PAYMENT.CODE']);
            if (!isset($action[1])) {
                Mage::throwException(Mage::helper('moneybookerspsp')->__('MoneybookersPSP: Wrong response data, method is missing'));
            }
            $method = strtoupper($action[0]);
            $action = strtoupper($action[1]);

            if ((string)$result->Transaction->attributes()->{'response'} == 'ASYNC'
                    && (string)$result->Transaction->Processing->Result == self::PROCESSING_RESULT_OK
                    && (string)$result->Transaction->Processing->Status->attributes()->{'code'} == self::PROCESSING_STATUS_CODE_WAITING
                    && !empty($result->Transaction->Processing->Redirect)) {
                $this->setOrderPlaceRedirectUrl(Mage::getUrl('moneybookerspsp/processing/threeds'));
                $params = array();
                foreach ($result->Transaction->Processing->Redirect->Parameter as $parameter) {
                    $params[(string)$parameter->attributes()->{'name'}] = (string)$parameter;
                }
                $params['redirect_url'] = (string)$result->Transaction->Processing->Redirect->attributes()->{'url'};
                $payment->setAdditionalInformation($params);
            }else {
                return parent::_beforeProcessResponse($request, $result, $payment);
            }
        }catch(Exception $e) {
            Mage::logException($e);
        }
    }

    public function setOrderPlaceRedirectUrl($url)
    {
        self::$_orderPlaceRedirectUrl = $url;
        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return self::$_orderPlaceRedirectUrl;
    }
    
    protected function _isAvailable($quote = null)
    {
        $session = Mage::getSingleton("core/session");
        $isAvailable = $session->getIsAvailableCC();
        $isAvailableTS = $session->getIsAvailableCCTS();
        $payment = $quote->getPayment();

        if (isset($isAvailable) &&
            time() - $isAvailableTS
            >= Skrill_MoneybookersPsp_Model_Abstract::REFRESH_PMETHOD_AVAILABILITY_PERIOD)
        {
            $isAvailable = (bool)$this->getWPFRegisterFormUrl();
            $isAvailableTS = time();
            $payment->setAdditionalInformation('isAvailableCC', $isAvailable);
            $payment->setAdditionalInformation('isAvailableCCTS', $isAvailableTS);
            $session->setIsAvailableCC($isAvailable);
            $session->setIsAvailableCCTS($isAvailableTS);
        }

        if (null === $isAvailable || !isset($isAvailable))
        {
            $isAvailable = $payment->getAdditionalInformation('isAvailableCC');
            $isAvailableTS = $payment->getAdditionalInformation('isAvailableCCTS');
            if (null === $isAvailable || !isset($isAvailable))
            {
                $isAvailable = (bool)$this->getWPFRegisterFormUrl();
                $isAvailableTS = time();
                $payment->setAdditionalInformation('isAvailableCC', $isAvailable);
                $payment->setAdditionalInformation('isAvailableCCTS', $isAvailableTS);
            }
            $session->setIsAvailableCC($isAvailable);
            $session->setIsAvailableCCTS($isAvailableTS);
        }

        return $isAvailable;
    }
}