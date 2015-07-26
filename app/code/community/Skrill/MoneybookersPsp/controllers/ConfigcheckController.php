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

class Skrill_MoneybookersPsp_ConfigcheckController extends Mage_Adminhtml_Controller_Action
{
    const PAYMENT_CC    = 'moneybookerspsp_cc';
    const PAYMENT_WLT   = 'moneybookerspsp_va';
    const PAYMENT_PSC   = 'moneybookerspsp_va_psc';
    
    const CC_RG         = 'CC.RG';
    const CC            = 'CC';
    const VA_DB         = 'VA.DB';
    const VA            = 'VA';
    const VA_PA         = 'VA.PA';
    
    const VALIDATION_EMPTY  = '2020';
    const VALIDATION_FAIL   = '4040';
    const VALIDATION_OK     = 'ACK';
    const VALIDATION_NOK    = 'NOK';
    
    const ENTITY_STORE      = 1;
    const ENTITY_WEBSITE    = 2;
    const ENTITY_DEFAULT    = 3;
    
    private $_entity;
    
    private function _getCurrentStore ()
    {
        $storeAbbr = $this->getRequest()->getParam('store');
        if (!$storeAbbr)
            return null;
        
        return Mage::getModel('core/store')->load($storeAbbr);
    }
    
    private function _getCurrentWebsite ()
    {
        $websiteAbbr = $this->getRequest()->getParam('website');
        if (!$websiteAbbr)
            return null;

        return Mage::getModel('core/website')->load($websiteAbbr);
    }

    private function _getCommonConfigData ($field, $entity, $etype)
    {
        if ($field == 'merchantclientid') // PSC field
            $path = 'paysafecard/moneybookerspsp_va_psc/' . $field;
        else
            $path = 'moneybookerspsp/settings/' . $field;
        
        switch ($etype)
        {
            case self::ENTITY_DEFAULT :
                //return Mage::getStoreConfig($path, $entity);
                return (string) Mage::app()->getConfig()->getNode('default/' . $path);
                break;
            case self::ENTITY_WEBSITE :
                return $entity->getConfig($path);
                break;
            case self::ENTITY_STORE :
                return Mage::getStoreConfig($path, $entity);
                break;
        }
        
        return null;
    }
    
    private function _getStoreWebSiteConfigData ($field, $entity, $etype)
    {
        $path = 'moneybookerspsp/settings/' . $field;
        $website = $entity;
        if ($etype == self::ENTITY_STORE)
        {
            $website = $entity->getWebsite();
        }
        elseif ($etype == self::ENTITY_DEFAULT)
        {
            //return Mage::getStoreConfig($path, $entity);
            return (string) Mage::app()->getConfig()->getNode('default/' . $path);
        }    
        return $website->getConfig('moneybookerspsp/settings/' . $field);
    }
    
    private function _selectMethod ($entity, $etype, $sendbaseamount = false)
    {
        $func = null;
        $website = null;
        switch ($etype)
        {
            case self::ENTITY_DEFAULT :
                $func = function ($param) { return (string) Mage::app()->getConfig()->getNode('default/' . $param); };
                break;
            case self::ENTITY_WEBSITE :
                $func = function ($param) use ($entity) { return $entity->getConfig($param); };
                break;
            case self::ENTITY_STORE :
                if ($sendbaseamount)
                {
                    $website = $entity->getWebsite();
                    $func = function ($param) use ($website) { return $website->getConfig($param); };
                    break;
                }
                $func = function ($param) use ($entity) { return $entity->getConfig($param); };
                break;
        }

        if (call_user_func($func, 'moneybookerspsp/moneybookerspsp_cc/active'))
            return self::PAYMENT_CC;
        if (call_user_func($func, 'paysafecard/moneybookerspsp_va_psc/active'))
            return self::PAYMENT_PSC;
        
        return self::PAYMENT_WLT;
    }
    
    private function _initRequest ()
    {
        $entity = null;
        if (!($entity = $this->_getCurrentStore()))
        {
            if (!($entity = $this->_getCurrentWebsite()))
            {
                $entityType = self::ENTITY_DEFAULT;
            }
            else
            {
                $entityType = self::ENTITY_WEBSITE;
            }
        }
        else
        {
            $entityType = self::ENTITY_STORE;
        }
        $this->_entity = $entity;
        $this->_entityType = $entityType;
        
        $sendbaseamount = $this->_getCommonConfigData('sendbaseamount', $entity, $entityType);
        
        $paymentMethod = $this->_selectMethod($entity, $entityType, $sendbaseamount);
        
        $params = array(
                'REQUEST.VERSION'       =>  '1.0',
                'SECURITY.SENDER'       =>  $sendbaseamount ?   $this->_getStoreWebSiteConfigData('sender', $entity, $entityType) :
                                                                $this->_getCommonConfigData('sender', $entity, $entityType),
                'USER.LOGIN'            =>  $sendbaseamount ?   $this->_getStoreWebSiteConfigData('login', $entity, $entityType) :
                                                                $this->_getCommonConfigData('login', $entity, $entityType),
                'USER.PWD'              =>  $sendbaseamount ?   $this->_getStoreWebSiteConfigData('password', $entity, $entityType) :
                                                                $this->_getCommonConfigData('password', $entity, $entityType),
                'TRANSACTION.CHANNEL'   =>  $sendbaseamount ?   $this->_getStoreWebSiteConfigData('channel', $entity, $entityType) :
                                                                $this->_getCommonConfigData('channel', $entity, $entityType),
                'TRANSACTION.RESPONSE'  =>  'ASYNC',
                'TRANSACTION.MODE'      =>  ($sendbaseamount ?  $this->_getStoreWebSiteConfigData('test_mode', $entity, $entityType) :
                                                                $this->_getCommonConfigData('test_mode', $entity, $entityType)) ? 'CONNECTOR_TEST' : 'LIVE',
                
                'IDENTIFICATION.TRANSACTIONID'  =>  mt_rand() . '_' . $paymentMethod,
                'PRESENTATION.AMOUNT'   =>  1.0,
                'PRESENTATION.CURRENCY' =>  $sendbaseamount ?   Mage::app()->getStore()->getBaseCurrencyCode() :
                                                                Mage::app()->getStore()->getCurrentCurrencyCode(),
                'NAME.SALUTATION'       =>  null,
                'NAME.TITLE'            =>  null,
                'NAME.GIVEN'            =>  null,
                'NAME.FAMILY'           =>  null,
                'CONTACT.EMAIL'         =>  null,
                'CONTACT.MOBILE'        =>  null,
                'CONTACT.PHONE'         =>  null,
                'CONTACT.IP'            =>  Mage::helper('core/http')->getRemoteAddr(true),
                'ADDRESS.STREET'        =>  null,
                'ADDRESS.CITY'          =>  null,
                'ADDRESS.STATE'         =>  null,
                'ADDRESS.COUNTRY'       =>  null,
                'ADDRESS.ZIP'           =>  null,
                'ACCOUNT.ID'            =>  null,
                'ACCOUNT.REGISTRATION'  =>  null,

                'FRONTEND.ENABLED'      =>  'true',
                'FRONTEND.POPUP'        =>  'false',
                'FRONTEND.MODE'         =>  'DEFAULT',
                'FRONTEND.STATUSBAR_VISIBLE'    =>  'false',
                'FRONTEND.RETURN_ACCOUNT'   =>  'true',
                'FRONTEND.REDIRECT_TIME'    =>  '0',
                'FRONTEND.LANGUAGE'     =>  'EN',
                'FRONTEND.RESPONSE_URL' =>  Mage::getUrl('moneybookerspsp/configcheck/status'),
                'FRONTEND.SESSION_ID'   => Mage::getSingleton('core/session')->getSessionId(),
                'FRONTEND.COLLECT_DATA' =>  'false',
                'FRONTEND.PM.DEFAULT_DISABLE_ALL' => 'true',
                'FRONTEND.PM.1.ENABLED' => 'true',
            );
        
        switch ($paymentMethod)
        {
            case self::PAYMENT_CC :
                $params['FRONTEND.PM.1.METHOD'] = self::CC;
                $params['PAYMENT.CODE'] = self::CC_RG;
                $params['FRONTEND.COLLECT_DATA'] = 'true';
                break;
            case self::PAYMENT_PSC :
                $params['FRONTEND.PM.1.METHOD'] = self::VA;
                $params['PAYMENT.CODE'] = self::VA_DB;
                $params['ACCOUNT.BRAND'] = 'PAYSAFECARD';
                $params['FRONTEND.COLLECT_DATA'] = 'false';
                $params['FRONTEND.PM.DEFAULT_DISABLE_ALL'] = '';
                $params['FRONTEND.PM.1.ENABLED'] = '';
                $params['FRONTEND.PM.1.SUBTYPES'] = '';
                $params['FRONTEND.PM.1.METHOD'] = '';
                $params['IDENTIFICATION.SHOPPERID'] = $this->getConfigData('merchantclientid');
                break;
            default:
                $params['FRONTEND.PM.1.METHOD'] = self::VA;
                $params['PAYMENT.CODE'] = self::VA_DB;
                $params['FRONTEND.PM.1.SUBTYPES'] = 'MONEYBOOKERS';
                $params['ACCOUNT.BRAND'] = 'MONEYBOOKERS';
        }

        return $params;
    }
   
    public function checkAction()
    {
        $paymentApi = Mage::getSingleton('moneybookerspsp/api');
        $params = $this->_initRequest();
        
        $sendbaseamount = $this->_getCommonConfigData('sendbaseamount', $this->_entity, $this->_entityType);
        if ($sendbaseamount && $this->_entityType == self::ENTITY_STORE)
            $paymentApi->setStore($this->_entity->getWebsite());
        else
            $paymentApi->setStore($this->_entity);
        
        $result = $paymentApi->processWPFRequest($params);
        $result = $paymentApi->parseWPFResponse($result);
        if (isset($result['POST.VALIDATION']) &&
            $result['POST.VALIDATION'] == self::VALIDATION_OK &&
            isset($result['FRONTEND.REDIRECT_URL']))
        {
            $output['errno'] = 0;
            $output['message'] = 'Congratulations! Your channel settings are correct, you may now enable various payment options.';
        }
        elseif (isset($result['POST.VALIDATION']) &&
                $result['POST.VALIDATION'] == self::VALIDATION_OK &&
                !isset($result['FRONTEND.REDIRECT_URL']))
        {
            $output['errno'] = -1;
            if ($result['PROCESSING.STATUS'] == 'REJECTED_VALIDATION' &&
                preg_match('/missing or invalid sender id/i', $result['PROCESSING.RETURN']))
                $output['message'] = 'Missing or incorrect SENDER data, please fill in the mandatory field.';
        }
        elseif (isset($result['POST.VALIDATION']) &&
                $result['POST.VALIDATION'] == self::VALIDATION_FAIL)
        {
            $output['errno'] = -1;
            $output['message'] = <<<MSG
    Incorrect channel data, please verify that you are using correct CHANNEL, SENDER, LOGIN, PASSWORD setttings,
    and that your channel operates  in the required mode (test / live).
MSG;
        }
        else
        {
            $utput['errno'] = 0;
            $output['message'] = '';
        }
        
        Mage::app()->getResponse()->setBody(json_encode($output));
    }
}
 