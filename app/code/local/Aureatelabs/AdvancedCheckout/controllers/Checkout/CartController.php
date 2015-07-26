<?php
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'CartController.php');
class Aureatelabs_AdvancedCheckout_Checkout_CartController extends Mage_Checkout_CartController
{
    public function checkoutAction()
    {
        $params = $this->getRequest()->getParams();
        $email = $params['email'];
        if(!$email) {
            return $this->_redirect('checkout/cart', array('_secure'=>true));
        }
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($websiteId);
        $customer->setStore($store);
        $customer->loadByEmail($email);
        if(!$customer->getId()){
            $CustomerName = explode('@',$email);
            $customer->setFirstname($CustomerName[0])
                ->setLastname($CustomerName[0])
                ->setEmail($email)
                ->setPassword('123456');
            try{
                $customer->save();
            }
            catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
        $customerId = $customer->getId();
        Mage::getSingleton('customer/session')->setCustomerId($customerId);
        $cart = $this->_getCart();
        $cart->getQuote()->setCustomerId($customerId);
        $this->_redirect('checkout/onepage', array('_secure'=>true));
    }

}