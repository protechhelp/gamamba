<?php
/**
 * Configurable Checkout
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitconfcheckout
 * @version      2.1.27
 * @license:     2qI6oAD8hvvkjMutXwa7Vp1gcsMrnxn0DqZRLmfWz9
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Aitoc_Aitconfcheckout_OnepageController extends Mage_Checkout_OnepageController
{
    protected $_sectionUpdateNames = array(
        'shipping_method' => 'shipping-method',
        'payment' => 'payment-method',
        'review' => 'review',
    );

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        $this->getRequest()->setRouteName('checkout');

        return parent::preDispatch();
    }

    /**
     * @param array $result
     * @parem string $sGotoStep
     * @return array
     */
    private function _getGotoStepResult($result, $sGotoStep)
    {
        if (!$sGotoStep) return false;
        if($sGotoStep != 'shipping_method' && $sGotoStep != 'payment' && $sGotoStep != 'review')
            return $result;

        $result['goto_section'] = $sGotoStep;

        $name = $this->_sectionUpdateNames[$sGotoStep];
        $nameMethod = $this->_sectionUpdateFunctions[$name];

        if($sGotoStep == 'review')
        {
            $this->loadLayout('checkout_onepage_review');
        }

        $result['update_section'] = array(
            'name' => $name,
            'html' => $this->$nameMethod()
        );

        return $result;
    }

    public function progressAction()
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0', '>='))        
        {
            $this->getOnepage()->saveSkippedSata('progress');            
        }
        
        return parent::progressAction();
    }    

    /**
     * override parent
     */
    public function saveBillingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                
                // start aitoc                
                if($this->_isSetSkipData($result, $data))
                {
                    return;
                }
                // finish aitoc  
                
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result = $this->_getGotoStepResult($result, 'payment');
                }
                elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result = $this->_getGotoStepResult($result, 'shipping_method');
                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                }
                else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    /**
     * @param array $result
     * @return bool
     */
    protected function _isSetSkipData($result, $data)
    {
        if($this->_setSkipDataByName($result,'billing','shipping'))
        {
            return true;
        }
        else
        {
            if (!$this->getOnepage()->getQuote()->isVirtual() AND isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1 && $this->_setSkipDataByName($result,'shipping','shipping_method'))
            {
                return true;
            }
        }

        return false;

    }

    /**
     *
     * @param array $result
     * @param string $nameStep
     * @param string $nameSkip
     * @return bool
     */
    protected function _setSkipDataByName($result, $nameStep, $nameSkip)
    {
        $sSkipData = Mage::getModel('aitconfcheckout/aitconfcheckout')->getSkipStepData($nameStep, $this->getOnepage()->getQuote());

        if ($sSkipData AND $sSkipData != $nameSkip)
        {
            $this->_insertShippingInfoData($result,$sSkipData);
            return true;
        }

        return false;
    }

    /**
     * Set body by result
     * @param array $result
     * @param string $sSkipData
     * @return array
     */
    protected function _insertShippingInfoData($result, $sSkipData)
    {
        $result = $this->_getGotoStepResult($result, $sSkipData);
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return $result;
    }

    /**
     * override parent
     */
    public function saveShippingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost())
        {
            // Magento behaves very strange in shipping information section. It overwrites default country (with JavaScript, i guess) with country you`ve enetered previosly. This is the solution or "hack" for that.
            $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash('shipping');
            if (empty($aAllowedFieldHash['country']))
            {
                $_POST['shipping']['country_id'] = Mage::helper('aitconfcheckout')->getDefaultCountryId();               
            }
            
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                // start aitoc                
                if($this->_setSkipDataByName($result,'shipping','shipping_method'))
                    return;
                // finish aitoc
                $result = $this->_getGotoStepResult($result, 'shipping_method');
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    /**
     * override parent
     */
    public function saveShippingMethodAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
                $this->getResponse()->setBody(Zend_Json::encode($result));
                // start aitoc
                if($this->_setSkipDataByName($result,'shipping_method','payment'))
                    return;
                // finish aitoc
                $result = $this->_getGotoStepResult($result, 'payment');
            }
            if(version_compare(Mage::getVersion(),'1.6.1.0','ge'))
            {
                $this->getOnepage()->getQuote()->collectTotals()->save();
            } 
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }

    }
    public function indexAction()
    {
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));

        $method = Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER;
        $this->getOnepage()->saveCheckoutMethod($method);
        $customerId  = Mage::getSingleton('customer/session')->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $firstName = $customer->getFirstname();
        $LastName = $customer->getLastname();
        $email = $customer->getEmail();
        $data = array('firstname'=>$firstName,'lastname'=>$LastName,'email' =>$email,'country_id' => 'US','save_in_address_book' => 1,'use_for_shipping' => 1);
        $this->getOnepage()->saveBilling($data,'');
        $this->renderLayout();
    }
}