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

class Skrill_MoneybookersPsp_Model_Va extends Skrill_MoneybookersPsp_Model_Abstract
{
    protected $_code = 'moneybookerspsp_va';

    protected $_isGateway               = true;
    protected $_canAuthorize            = false;
    protected $_canCapture              = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = false;

    protected $_paymentMethod		= 'VA';
    protected $_defaultLocale		= 'en';
    protected $_supportedLocales        = array('de', 'en');
    protected $_availableMethods        = array('VA' => array('MONEYBOOKERS'));

    protected $_formBlockType = 'moneybookerspsp/form_va';
    protected $_infoBlockType = 'moneybookerspsp/info_cc';

    protected $_formJsPath = 'Skrill/moneybookerspsp/init/debit.js';
    protected $_formCssPath = 'css/Skrill/moneybookerspsp/form/debit.css';

    /**
     * Return url for redirection after order placed
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('moneybookerspsp/processing/redirect');
    }

    protected function _initRequestParams($isOrderPlaced = true)
    {
	$languages = array( 'SE' => 'SV',
			    'DK' => 'DA',
			    'ES' => 'ES',
			    'FR' => 'FR',
			    'IT' => 'IT',
			    'PL' => 'PL',
			    'GR' => 'GR',
			    'RO' => 'RO',
			    'RU' => 'RU',
			    'TR' => 'TR',
			    'CN' => 'CN',
			    'CZ' => 'CZ',
			    'NL' => 'NL',
			    'DE' => 'DE',
			    'FI' => 'FI',
			    'BG' => 'BG');

        $params = parent::_initRequestParams($isOrderPlaced);
        $params['FRONTEND.ENABLED'] = 'true';

        $params['FRONTEND.COLLECT_DATA'] = 'false';
        $params['ACCOUNT.BRAND'] = 'MONEYBOOKERS';

        if (!$isOrderPlaced)
        {
            if (!$dataObject = Mage::registry('current_quote'))
            {
                $dataObject = Mage::getSingleton('checkout/session')->getQuote();
            }
            $info = $dataObject->getPayment();
        }
        else
        {
            $info = $this->getInfoInstance();
            $dataObject = $info->getOrder();
        }
        $billingAddress = $dataObject->getBillingAddress();
        $billingAddress = $dataObject->getBillingAddress();
	$country = $billingAddress->getCountry();

        try {
            $iso3_country = Mage::getModel('directory/country')->loadByCode($country)->getIso3Code();
            $params['CRITERION.MONEYBOOKERS_country'] = $iso3_country;
            $params['CRITERION.MONEYBOOKERS_language'] = array_key_exists($country, $languages)? $languages[$country] : 'EN';
        } catch (Mage_Core_Exception $e) {
            // Used for requests that do not include customer data, initial payment options display/listing
        }
        return $params;
    }

    protected function _isAvailable($quote = null)
    {
	$session = Mage::getSingleton("core/session");
	
	$isAvailName = 'isAvailableVA';
	if (preg_match('/moneybookerspsp_va_(.*)/', $this->_code, $mcode))
	    $isAvailName = 'isAvailableVA' . strtoupper($mcode[1]);
	$isAvailNameTS = $isAvailName . 'TS';
	
	$isAvailable = $session->getData($isAvailName);
	$isAvailableTS = $session->getData($isAvailNameTS);
	$payment = $quote->getPayment();
	
	if (isset($isAvailable) &&
            time() - $isAvailableTS
            >= Skrill_MoneybookersPsp_Model_Abstract::REFRESH_PMETHOD_AVAILABILITY_PERIOD)
        {
            $isAvailable = (bool)$this->getWPFDebitFormUrl(false);
	    $isAvailableTS = time();
            $payment->setAdditionalInformation($isAvailName, $isAvailable);
            $payment->setAdditionalInformation($isAvailNameTS, $isAvailableTS);
            $session->setData($isAvailName, $isAvailable);
            $session->setData($isAvailNameTS, $isAvailableTS);
        }
	
        if (null === $isAvailable || !isset($isAvailable))
        {
            $isAvailable = $payment->getAdditionalInformation($isAvailName);
            $isAvailableTS = $payment->getAdditionalInformation($isAvailNameTS);
	    if (null === $isAvailable || !isset($isAvailable))
            {
		$isAvailable = (bool)$this->getWPFDebitFormUrl(false);
		$isAvailableTS = time();
                $payment->setAdditionalInformation($isAvailName, $isAvailable);
                $payment->setAdditionalInformation($isAvailNameTS, $isAvailableTS);
	    }
	    $session->setData($isAvailName, $isAvailable);
	    $session->setData($isAvailNameTS, $isAvailableTS);
        }

	return $isAvailable;
    }
}