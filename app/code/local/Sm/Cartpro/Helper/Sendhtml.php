<?php
class Sm_Cartpro_Helper_Sendhtml   extends Varien_Object
{	
	public static $_nameitem=null;
	public $_ISWISHLIST=null;
	public function sendResponse($cart, $link){
        //echo"pipi";die();
		$options="0";
		$wishlist="";
		$wishtitle="";
		$addwhat="0";
		//echo"<pre>";var_dump($this->getAddwhat());die();
		//$addwhat=(Mage::getSingleton('checkout/session')->getAddwhat()!='0')?Mage::getSingleton('checkout/session')->getAddwhat():"0";
		$addwhat=(Mage::helper('cartpro/Sendhtml')->getAddwhat()!='0')?Mage::helper('cartpro/Sendhtml')->getAddwhat():"0";
		//Mage::getSingleton('checkout/session')->setAddwhat("0");
		if ($product = Mage::registry('current_product'))
        {
			$options=$product->getHasOptions();
			if($product->getTypeId()=='grouped'){
				$options="1";
			}
	    }
		//$iswishlist=($this->_ISWISHLIST)?$this->_ISWISHLIST:"";
		//$iswishlist=Mage::getSingleton('checkout/session')->getIswishlist();
		$nameitem=($this->_NAMEITEM)?$this->_NAMEITEM:"";
		$this->_NAMEITEM='';//reset
		//echo $nameitem.":".$iswishlist;die(); 
		//$isfirst=Mage::getSingleton('checkout/session')->getIsfirst(); //cho phep nhan biet la` san pham se dc delete khoi wishlist khi lan dau an nut add to cart
		
		header('content-type: text/javascript');
		echo '{"r":"'.$addwhat.'", "cart":' . json_encode($cart) . ', "links":"'.$link.'","options":'.$options.', "nameitem":'.json_encode($nameitem).'}';
		die();		
		
		//$nameitem=$this->getNameitem();
		//$nameitem=$product->getProductName();
		//echo"break3";die();

	}
}