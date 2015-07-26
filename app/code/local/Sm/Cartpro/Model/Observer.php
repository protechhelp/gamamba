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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * AdminNotification observer
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sm_Cartpro_Model_Observer
{
    /**
     * Predispath admin action controller
     *
     * @param Varien_Event_Observer $observer
     */
	public function preDispatch(Varien_Event_Observer $observer)
	{
		return;
		$params=Mage::app()->getFrontController()->getRequest()->getParams();
		Zend_Debug::dump(Mage::getSingleton('core/layout')->getUpdate()->asString());die;
	}

	/**
	 * Postdispath admin action controller
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function postDispatch(Varien_Event_Observer $observer)
	{
	
		$params= Mage::app()->getFrontController()->getRequest()->getParams();
		//Zend_Debug::dump(Mage::getSingleton('core/layout')->getUpdate()->asString());die;
		//Zend_Debug::dump(Mage::app()->getRequest());die;
		
		if(!$params["isajax"] AND !Mage::getSingleton('checkout/session')->getAjax()){
			return false;
		}
		
		if(!Mage::getSingleton('checkout/session')->getAjax()){
			Mage::getSingleton('checkout/session')->setAjax("1");
		}
		else{
			//Zend_Debug::dump($params);die;
		}
		if($params){
			Mage::getSingleton('checkout/session')->setAjaxParams($params);
		}
		else{
			$params = Mage::getSingleton('checkout/session')->getAjaxParams();
		}
		if($params["create"]=="false"){
				
			$layout = Mage::getSingleton('core/layout');
			$item_block = $layout->getBlock( $params["name"] );
			
			if($item_block){
				if($params["template"]){
					$item_block->setTemplate($params["template"]);
				}
				Mage::getSingleton('checkout/session')->setAjax("0");
				Mage::getSingleton('checkout/session')->setAjaxParams("0");
				echo $item_block->toHtml();die;
			}
			else{
				Mage::setIsDeveloperMode(true);
				Mage::helper("logger")->info("[create=false] not exist this block have name > ".$params["name"]);
				return false;				
			}
		}
		else {
			$item_layout = Mage::getSingleton('core/layout');
			if($item_block = $item_layout->createBlock($params["class_name"], $params["name"])){
				if($params["template"]){
					$item_block->setTemplate($params["template"]);
				}
			}
			else {
				Mage::setIsDeveloperMode(true);
				Mage::helper("logger")->info("[create=true] not exist this block have name > ".$params["name"]);
				return;
			}
		}
		if($params["onlychild"]=="true"){
			Zend_Debug::dump($item_block->getChild());
		}
		else{
			echo $item_block->renderView();
		}
		exit();		
	}
	
    protected function _initProduct()
    {
        $productId = (int) Mage::app()->getFrontController()->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }   
	/**
	 * @return Ambigous <boolean, Mage_Core_Model_Abstract, Mage_Core_Model_Abstract>
	 */
	public function getProduct(){
		return $this->_initProduct();
	}
    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

	public function getNameProduct($observer){
		Mage::getSingleton('checkout/session')->setNameitem($observer->getProduct()->getName());
		return;
	}
	public function Wishtocheckout(){	// be redirect from controler wishlist/index/cart
			if(Mage::getSingleton('checkout/session')->getIsajax()=='1'){
				if (Mage::getSingleton('checkout/session')->getIspage()!='1')
					// (!Mage::app()->getFrontController()->getRequest()->getParam('in_cart')) &&
					// (!Mage::app()->getFrontController()->getRequest()->getParam('is_checkout')) &&
					// (Mage::app()->getFrontController()->getRequest()->getParam('awacp'))
					// )
				{	
					$cart = Mage::helper('cartpro')->renderMiniCart();
					$text = Mage::helper('cartpro')->renderCartTitle();
					$cartpro = Mage::helper('cartpro')->renderMiniCartPro();
					Mage::helper('cartpro')->sendResponse($cart, $text, $cartpro);
				}
				else
				{	
					Mage::getSingleton('checkout/session')->setIspage("0");
					$cart = Mage::helper('cartpro')->renderBigCart();
					$text = Mage::helper('cartpro')->renderCartTitle();
					$cartpro = Mage::helper('cartpro')->renderMiniCartPro();					
					Mage::helper('cartpro')->sendResponse($cart, $text, $cartpro);
				}
				
			}	
	}

	public function addToCartWishList(){
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=""){ //request is ajax

				if(version_compare(Mage::getVersion(),'1.4.0.1','>=')){
					Mage::getSingleton('checkout/session')->setIsfirst("1");
				}else{
					$message=Mage::getSingleton('checkout/session')->getMessages();
 
					if($message->getItems('error')) { //echo"<pre>";var_dump($message->getItems('error'));die();	}	// item have error cause by not input options for item
						Mage::getSingleton('checkout/session')->setIsfirst("2");
					}
					else{ 
						Mage::getSingleton('checkout/session')->setIsfirst("1");
					}
				}
				if(Mage::app()->getFrontController()->getRequest()->getParam('isWishlist')){
					Mage::getSingleton('checkout/session')->setIswishlist("1");
				}
				else{
					Mage::getSingleton('checkout/session')->setIswishlist("2");
				}
				Mage::getSingleton('checkout/session')->setIsajax("1");	// isajax =1 allow when request go to controller product/view with only product type is group , can check this request is ajax
				Mage::getSingleton('catalog/session')->getData('messages')->clear();
				if(Mage::getSingleton('wishlist/session')->getMessages()->getItems('error')){
					Mage::getSingleton('wishlist/session')->getData('messages')->clear();
					$itemId     = (int)Mage::app()->getFrontController()->getRequest()->getParam('item');
					/* @var $item Mage_Wishlist_Model_Item */
					if(Mage::getModel('wishlist/item')->load($itemId)){
						$item       = Mage::getModel('wishlist/item')->load($itemId);
					}
					else{
						return;
					}
					$redirectUrl= $item->getProductUrl();
					//$item->delete();  //if this code do delete then fire error with only 1 sp special("The Only Children: Paisley T-Shirt"), when change size then price not changed
					
					//Mage::app()->getResponse()->setRedirect(Mage::getUrl("myrouter/mycontroller/noview")); //this code allow redirect direct in model
					//Mage::setIsDeveloperMode(true); 
					//Mage::helper("logger")->info($redirectUrl);
					return Mage::app()->getResponse()->setRedirect($redirectUrl);
				}
				return;		
		}
	}	
	public function addWishlist(){
		
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isWishlist')!=""){

				$product=$this->_initProduct();
				Mage::getSingleton('customer/session')->getData('messages')->clear();
				if(Mage::app()->getFrontController()->getRequest()->getParam('isWishlist')=='0'){
					$wishlist = Mage::helper('cartpro')->renderMiniWish();		
				}
				else{
					$wishlist= Mage::helper('cartpro')->renderWishlist();
				}
				$text = Mage::helper('cartpro')->renderWishlistTitle();
				//Mage::getSingleton('checkout/session')->setAddwhat("1");	//1 == add wishlist, 2 == add compare
				Mage::helper('cartpro/Sendhtml')->setAddwhat("1");
				Mage::helper('cartpro/Sendhtml')->_NAMEITEM=$product->getName();
				Mage::helper('cartpro/Sendhtml')->sendResponse($wishlist, $text);
		
		}
	}
	public function addProductCompare(){
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=""){
				$product=$this->_initProduct();
				Mage::getSingleton('catalog/session')->getData('messages')->clear();
				$productcompare = Mage::helper('cartpro')->renderProductCompare();
				$text = "";
				Mage::helper('cartpro/Sendhtml')->setAddwhat("2");
				Mage::helper('cartpro/Sendhtml')->_NAMEITEM=$product->getName();
				Mage::helper('cartpro/Sendhtml')->sendResponse($productcompare, $text);
		}
	}
	
	public function addToCart($observer)
	{ 
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=""){
			$product=$this->_initProduct();
			//$this->_getSession->getData('messages')->clear();
			//Mage::getSingleton('core/session')->getData('messages')->clear();

			$message=Mage::getSingleton('checkout/session')->getMessages();
			$hasnotice=false;

			if($message->getItems('notice')) { 
				$hasnotice=true;
			}
			
			Mage::getSingleton('checkout/session')->getData('messages')->clear(); 

			$ajax=Mage::app()->getFrontController()->getRequest()->getParams(); 

			if ($product->getTypeId() == 'grouped' AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=null AND !isset($ajax['related_product'])) {
					Mage::getSingleton('checkout/session')->setIsajax("1");	// isajax =1 allow when request go to controller product/view with only product type is group , can check this request is ajax
					return;
			}
			//var_dump($product->getHasOptions()); //var_dump($product->getTypeInstance(true)->hasRequiredOptions($product));die();

			if($product->getHasOptions() AND $hasnotice){
				$hasnotice=false;
				Mage::getSingleton('checkout/session')->setIsajax("1");
				Mage::getSingleton('checkout/session')->setIspage(Mage::app()->getFrontController()->getRequest()->getParam('isCart'));
				return;
			} 
			if(Mage::app()->getFrontController()->getRequest()->getParam('miniwishtocart')){
				Mage::getSingleton('checkout/session')->setIsfirst("1");
			}
			if (!Mage::app()->getFrontController()->getRequest()->getParam('isCart')
				// (!Mage::app()->getFrontController()->getRequest()->getParam('in_cart')) &&
				// (!Mage::app()->getFrontController()->getRequest()->getParam('is_checkout')) &&
				// )
			){
					Mage::helper('cartpro')->_NAMEITEM=$product->getName();
					$cart = Mage::helper('cartpro')->renderMiniCart($ajax);
					//$cart = Mage::helper('cartpro')->renderTopLink();
					$text = Mage::helper('cartpro')->renderCartTitle();
					$cartpro = Mage::helper('cartpro')->renderMiniCartPro($ajax);					
					Mage::helper('cartpro')->sendResponse($cart, $text, $cartpro);
			}else{
					Mage::helper('cartpro')->_NAMEITEM=$product->getName();
					$cart = Mage::helper('cartpro')->renderBigCart();
					$text = Mage::helper('cartpro')->renderCartTitle();
					$cartpro = Mage::helper('cartpro')->renderMiniCartPro($ajax);					
					Mage::helper('cartpro')->sendResponse($cart, $text, $cartpro);
			}
		}	
	}

	public function updateCart($observer){
		// Mage::setIsDeveloperMode(true); Mage::helper("logger")->log("this is logger");
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=""){
		
			$ajax=Mage::app()->getFrontController()->getRequest()->getParams(); 
			
			if (!Mage::app()->getFrontController()->getRequest()->getParam('isCart')){
					$cart = Mage::helper('cartpro')->renderMiniCart($ajax);
					$text = Mage::helper('cartpro')->renderCartTitle();
					$cartpro = Mage::helper('cartpro')->renderMiniCartPro($ajax);					
					Mage::helper('cartpro')->sendResponse($cart, $text, $cartpro);
			}else{
					$cart = Mage::helper('cartpro')->renderBigCart();
					$text = Mage::helper('cartpro')->renderCartTitle();
					$cartpro = Mage::helper('cartpro')->renderMiniCartPro($ajax);					
					Mage::helper('cartpro')->sendResponse($cart, $text, $cartpro);
			}			
		}
	}
	public function addOptionsWishList($observer){	//for version magento 1.5 and later because when addtocart 1 item in wishlist page will auto redirect to wishlist/index/configure
		$this->addOptions($observer);
	}
	public function addOptions($observer)
	{	
		if(Mage::getStoreConfig('cartpro_cfg/general/enable')){

			$ajax = Mage::app()->getFrontController()->getRequest()->getParams();//

			$isajax=Mage::getSingleton('checkout/session')->getIsajax();
			
			if(!isset($isajax)){
				Mage::getSingleton('checkout/session')->setIsajax('0');
				$isajax=0;
			}
			if (!isset($ajax['isCart']) AND $isajax=='0') return;

			Mage::getSingleton('checkout/session')->setIsajax('0');
			
			$cart = Mage::helper('cartpro')->renderOptions();
			$cartpro = Mage::helper('cartpro')->renderMiniCartPro();			
			Mage::helper('cartpro')->sendResponse($cart,"", $cartpro);
		}
	}
	public function removeProduct() {
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=""){
			Mage::getSingleton('checkout/session')->getData('messages')->clear();
			if(!Mage::app()->getFrontController()->getRequest()->getParam('isCart')){
				$cart = Mage::helper('cartpro')->renderMiniCart();
			}else{
				$cart = Mage::helper('cartpro')->renderBigCart();
			}
			$text = Mage::helper('cartpro')->renderCartTitle();
			$cartpro = Mage::helper('cartpro')->renderMiniCartPro();			
			Mage::helper('cartpro')->sendResponse($cart, $text, $cartpro);
			exit();
		}
	}
	public function removeWish(){
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isWishlist')!=""){
			Mage::getSingleton('customer/session')->getData('messages')->clear();
			Mage::helper('cartpro/Sendhtml')->setAddwhat("1");
			if(!Mage::app()->getFrontController()->getRequest()->getParam('isWishlist')){
				$wish=Mage::helper('cartpro')->renderMiniWish();
			}else{
				$wish=Mage::helper('cartpro')->renderWishlist();
			}
			$text=Mage::helper('cartpro')->renderWishlistTitle();
			Mage::helper('cartpro/Sendhtml')->sendResponse($wish, $text);
			exit();			
		}
	}
	public function removeProductCompare(){
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=""){
				Mage::getSingleton('catalog/session')->getData('messages')->clear();
				$productcompare = Mage::helper('cartpro')->renderProductCompare();
				$text = "";
				Mage::helper('cartpro/Sendhtml')->setAddwhat("2");
				Mage::helper('cartpro/Sendhtml')->sendResponse($productcompare, $text);
				exit();			
		}		
	}
	public function clearProductCompare(){
		if(Mage::getStoreConfig('cartpro_cfg/general/enable') AND Mage::app()->getFrontController()->getRequest()->getParam('isCart')!=""){
				Mage::getSingleton('catalog/session')->getData('messages')->clear();
				$productcompare = Mage::helper('cartpro')->renderProductCompare();
				$text = "";
				Mage::helper('cartpro/Sendhtml')->setAddwhat("2");
				Mage::helper('cartpro/Sendhtml')->sendResponse($productcompare, $text);
				exit();			
		}		
	}
}
