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
class Aitoc_Aitconfcheckout_Model_Observer 
{
   protected $_needSave;
    protected $_importFromBilling = array(
                        'company','firstname','lastname','middlename','prefix','suffix',
                        'country_id','region','region_id','city','street','street2','postcode','telephone'
                    );

   protected function clearCode($data, $replace = '') {
       $this->_needSave = false;
       foreach ($data as $key => $value) {
           if (is_object($value)) continue;
           $value = (string) $value;
           if ($value == Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE || $value == Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE_INT) {
               $data[$key] = is_object($replace) ? $replace->getData($key) : $replace;
               $this->_needSave = true;
           }
       }
       return $data;
   }
   
   public function customerAddressSaveAfter($observer) {
       $data = $this->clearCode($observer->getCustomerAddress()->getData());
       if ($this->_needSave) {
           $observer->getCustomerAddress()->setData($data);
           $observer->getCustomerAddress()->save();
       }
   }

   /*
    * For PayPal payment, because it can use shippingAddress before it's saved by 'customerAddressSaveAfter'
    */
    public function salesQuotePaymentSaveBefore($observer) {
        //using this method only when ExpressCheckout method is running already
        if(Mage::registry('_singleton/paypal/express_checkout')) {
            //and when it have set shipping as overriden
            if ($observer->getPayment()->getAdditionalInformation( Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN ) ) {
                $quote = $observer->getPayment()->getQuote();
                $address = $quote->getShippingAddress();
                $billing = $quote->getBillingAddress();
                //if whole shipping info method was disabled - take required data from billing, can't use billing because it don't have some shipping values and data will be incorrect
                if(false === Mage::helper('aitconfcheckout')->checkStepActive($quote, 'shipping')) {
                    foreach($this->_importFromBilling as $key) {
                        $address->setData($key, $billing->getData($key));
                    }
                } else {
                    //checking all fields for default value and setting it anew from billing if it was found
                    $data = $this->clearCode($address->getData(), $billing);
                    if ($this->_needSave) {
                        $address->setData($data);
                    }
                }
            }
        }
    }

    /*
     * For Paypal standart payment redirect fix - if some required fields on shipping method was disabled and empty|incorrect - take them from billing
     */
    public function predispatchPaypalStandardRedirect()
    {
        //use observer below only when standart payment redirect method is applied
        $this->_enablePrepareLineItemsObserver = true;
    }

    public function paypalPrepareLineItems($observer) {
        if(!isset($this->_enablePrepareLineItemsObserver)) {
            //observer is not allowed to run
            return false;
        }
        if($observer->getPaypalCart()) {//1.4.2+
            $order = $observer->getPaypalCart()->getSalesEntity();
        } else {//1.4.1.1
            $order = $observer->getSalesEntity();
        }
        if($order->getIsVirtual()) {
            //no changing for virtuals orders required
            return false;
        }
        $shipping = $order->getShippingAddress();
        $billing = $order->getBillingAddress();
        foreach($this->_importFromBilling as $key) {
            $value = $shipping->getData($key);
            if($value == '' || $value == 2147483647 /*region_id*/) {
                $shipping->setData($key, $billing->getData($key));
            }
        }
    }

    public function predispatchPaypalExpressReview()
    {
        //temporary fix for express review page
        $this->_enableQuoteAddressCollectionSelect = true;
    }

    public function quoteAddressCollectionLoadAfter($observer)
    {
        //temporary fix for express review page
        if(!isset($this->_enableQuoteAddressCollectionSelect)) {
            return true;
        }
        $collection = $observer->getQuoteAddressCollection();
        foreach($collection as $item) {
            $data = $this->clearCode($item->getData());
            if ($this->_needSave) {
                $item->setData($data);
            }
        }
    }

    public function convertQuoteAddressToCustomerAddress( $observer )
    {
        $source = $observer->getSource();
        if($source->getAddressType() != Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            /*if(!Mage::getStoreConfig('aitconfcheckout/shipping/active')) {
                //if shipping method is disabled - mask it as 'same as billing' and magento will create default one based on billing
                $source->getQuote()->getShippingAddress()->setSameAsBilling(true);
            }*/
            return false;
        }
        $target = $observer->getTarget();
        $data = $this->clearCode($target->getData(), $source->getQuote()->getBillingAddress());
        $target->setData( $data );
    }

    /**
     * THIS METHOD IMMEDIATELY FORWARDS TO THE SAVE ORDER ACTION AFTER THE PAYMENT METHOD ACTION
     *
     * Save the order after having saved the payment method
     *
     * @event controller_action_postdispatch_checkout_onepage_savePayment
     *
     * @param $observer Varien_Event_Observer
     */
    public function saveOrderAfterPayment($observer)
    {
        /** @var $controllerAction Mage_Checkout_OnepageController */
        $controllerAction = $observer->getEvent()->getControllerAction();
        /** @var $response Mage_Core_Controller_Response_Http */
        $response = $controllerAction->getResponse();

        /**
         * jsonDecode is used because the response of the XHR calls of onepage checkout is always formatted as a json
         * string. jesonEncode is used after the response is manipulated.
         */
        $paymentResponse = Mage::helper('core')->jsonDecode($response->getBody());
        if (!isset($paymentResponse['error']) || !$paymentResponse['error']) {
            /**
             * If there were no payment errors, immediately forward to saving the order as if the user had confirmed it
             * on the review page.
             */
            $controllerAction->getRequest()->setParam('form_key', Mage::getSingleton('core/session')->getFormKey());

            /**
             * Implicitly agree with the terms and conditions by confirming the order
             */
            $controllerAction->getRequest()->setPost('agreement', array_flip(Mage::helper('checkout')->getRequiredAgreementIds()));

            $controllerAction->saveOrderAction();
            /**
             * jsonDecode is used because the response of the XHR calls of onepage checkout is always formatted as a json
             * string. jesonEncode is used after the response is manipulated.
             *
             * $response has here become the response of the saveOrderAction()
             */
            $orderResponse = Mage::helper('core')->jsonDecode($response->getBody());
            if ($orderResponse['error'] === false && $orderResponse['success'] === true) {
                /**
                 * Check for redirects here. If there are redirects than a module such as Adyen wants to redirect to a
                 * payment page instead of the success page after saving the order.
                 */
                if (!isset($orderResponse['redirect']) || !$orderResponse['redirect']) {
                    $orderResponse['redirect'] = Mage::getUrl('*/*/success');
                }
                $controllerAction->getResponse()->setBody(Mage::helper('core')->jsonEncode($orderResponse));
            }
        }
    }
}