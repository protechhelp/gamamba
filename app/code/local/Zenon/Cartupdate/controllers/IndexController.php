<?php
class Zenon_Cartupdate_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("updatecart"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));
      $breadcrumbs->addCrumb("updatecart", array(
                "label" => $this->__("updatecart"),
                "title" => $this->__("updatecart")
		   ));
      $this->renderLayout(); 
	  
    }
	
	    public function updateProductQtyAction() {  
            $item_id = Mage::app()->getRequest()->getParam('item_id');  
            $qty = Mage::app()->getRequest()->getParam('qty');          
            Mage::getSingleton('core/session', array('name'=>'frontend'));      
            $simbol= Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();       
            $store=Mage::app()->getStore()->getCode();     
            $cart = Mage::getSingleton('checkout/cart');  
            $items = $cart->getItems();        
            foreach ($items as $item) {     
                if($item->getId()==$item_id){  
                    $item->setQty($qty);
                    $cart->save();   
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);  
                    break;  
                }  
            }  
        $data = array();  
	    $layout=$this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_cart_index');
		$layout->generateXml();
		$layout->generateBlocks();	
		$output = $layout->getBlock('checkout.cart')->toHtml();		
		$htmlmini =  $this->getLayout()->CreateBlock('page/html')->getChildHtml('mini-cartpro');
        $data['cart_header'] = $htmlmini;  
        $data['big_cart'] = $output;  
		$cart = Mage::getModel('checkout/cart')->getQuote()->getData();  
		$qty = $cart['items_qty'];  
		$data['cart_header_qty'] = $qty;  
            echo json_encode($data);  
        }  
	  public function couponupdateAction()  
        {  
    		 $couponCode = (string) $this->getRequest()->getParam('coupon_code');
	
            if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
             }
	     $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

            Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                ->collectTotals()
                ->save();
	
			   if ($codeLength) {
                if ($isCodeLengthValid && $couponCode ==  Mage::getSingleton('checkout/cart')->getQuote()->getCouponCode()) {
                  $message =  $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode));
                    
                } else {
                  $message = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
                  
                }
            } else {
               $message = $this->__('Coupon code was canceled.');
            }
		
			$layout=$this->getLayout();
			$update = $layout->getUpdate();
			$update->load('checkout_cart_index');
			$layout->generateXml();
			$layout->generateBlocks();	
			$output = $layout->getBlock('checkout.cart')->toHtml();		
				$data['big_cart'] = $output;  
				$data['message'] = $message;  
				echo json_encode($data);  
        }  

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
}