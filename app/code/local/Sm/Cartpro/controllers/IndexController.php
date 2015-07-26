<?php
//require_once 'Mage/Checkout/controllers/CartController.php';   
//class Sm_Cartpro_IndexController extends Mage_Checkout_CartController 
class Sm_Cartpro_IndexController extends Mage_Core_Controller_Front_Action
{
	// public function noRouteAction($coreRoute = null){
		// echo"norooute";die();
	// }
	public function getblockAction(){
		$param = Mage::app()->getFrontController()->getRequest()->getParams();
		//Mage::helper("logger")->info($param);
		/* Zend_Debug::dump($param);die; */
		//Mage::app()->getFrontController()->getRequest()->getParam('block_class_name, block_name, block_template ')
		if($param["create"]!=="false"){
			
			$this->loadLayout();
		
			if($item_block = $this->getLayout()->getBlock($param["name"])){
				if($param["template"]){
					$item_block->setTemplate($param["template"]);
				}			
			}
		}
		else {
			$item_layout = Mage::getSingleton('core/layout');
			if($item_block = $item_layout->createBlock($param["class_name"], $param["name"])){
				if($param["template"]){
					$item_block->setTemplate($param["template"]);
				}
			}
			else {
				echo"not exist this class ".$param["class_name"];
				die;
			}
		}
		if($param["onlychild"]=="true"){
			Zend_Debug::dump($item_block->getChild());
		}
		else{
			echo $item_block->renderView();
		}
		exit();		
	}
	public function testAction()
	{
		//Zend_Debug::dump(Mage::getStoreConfig("cartpro/config/enabled"));die;;
		$this->loadLayout();
		$this->renderLayout();
		// $block=mage::getsingleton('core/layout');
		// $wishlist=	$block	->createblock('wishlist/customer_wishlist')
							// ->settemplate('wishlist/view.phtml');
		$block = $this->getLayout()->createBlock('wishlist/customer_wishlist','name',array('template'=>'wishlist/view.phtml'));
		$this->getLayout()->addBlock($block);
		
		echo $this->getLayout()->getBlock('top.links')->toHtml();
	}
    public function indexAction()
    {
		//echo"aaaa";die();
    	//parent::indexAction();
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/cartpro?id=15 
    	 *  or
    	 * http://site.com/cartpro/id/15 	
    	 */
    	/* 
		$cartpro_id = $this->getRequest()->getParam('id');

  		if($cartpro_id != null && $cartpro_id != '')	{
			$cartpro = Mage::getModel('cartpro/cartpro')->load($cartpro_id)->getData();
		} else {
			$cartpro = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($cartpro == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$cartproTable = $resource->getTableName('cartpro');
			
			$select = $read->select()
			   ->from($cartproTable,array('cartpro_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$cartpro = $read->fetchRow($select);
		}
		Mage::register('cartpro', $cartpro);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function getproductidAction(){
		$request_url=$this->getRequest()->getPost('str');
		//$request_url="http://127.0.0.1/mage1411/index.php/apparel/hoodies/the-get-up-kids-band-camp-pullover-hoodie.html";
		//$request_url="http://127.0.0.1/mage1411/index.php/apparel/cn-clogs-beach-garden-clog.html";
		//$request_url="http://127.0.0.1/mage1411/index.php/checkout/cart/add/uenc/aHR0cDovLzEyNy4wLjAuMS9tYWdlMTQxMS9pbmRleC5waHAvY2F0YWxvZy9jYXRlZ29yeS92aWV3L3MvYXBwYXJlbC9pZC8xOC8,/product/39/";
		
		$request_url = explode(Mage::getBaseUrl(),$request_url);//echo $request_url[1];die();
		//var_dump($request_url );die();
		$url = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$request_url[1]);
		//$url = Mage::getResourceModel('core/url_rewrite_collection')->addFieldToFilter('request_path',$request_url[1]);
		$url_path = "";
		foreach($url as $u){
			$url_path = $u->getTargetPath();
		}
		// //$productId = (int) $this->getRequest()->getParam('product');
		// //echo $productId;
		if(!$url_path) {
			//echo" rong";die();
			$params=explode("/",$request_url[1]);
			$keyproduct=array_search("product", $params);
			//$params=$this->getRequest()->getParams();
			$idproduct=$params[$keyproduct+1];
			//echo $idproduct;die();	//find id product
			//var_dump($params);die();
		}
		else{
			echo $url_path;die();
		}
		//$this->loadLayout();
		$this->addAction();
		
		//$this->getLayout()->getBlock('root')->setTemplate('sm/cartpro/cartpro.phtml');catalog/product/view/options/wrapper.phtml
		//$this->getLayout()->getBlock('root')->setTemplate('catalog/product/view/options/wrapper.phtml');
		//$this->getLayout()->createBlock('cartpro/top');
		//$this->loadLayout();
		//$this->renderLayout();
	}
    protected function _initProduct()
    {
		// $params=array(
	    // "uenc"		=>"aHR0cDovLzEyNy4wLjAuMS9tYWdlMTQxMS9pbmRleC5waHAvY2F0YWxvZy9jYXRlZ29yeS92aWV3L3MvYXBwYXJlbC9pZC8xOC8,",
		// "product"	=>"39"
		// );
        $productId = (int) $this->getRequest()->getParam('product');
		//echo  $productId ;die();
		//$productId=(int)$params['product'];echo  $productId ;die();
        if ($productId) {//echo $productId;die();
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
				//var_dump($product);die();
            if ($product->getId()) {//echo $productId;die();
                return $product;
            }
        }
        return false;
    }
    public function addAction()
    {	//echo"abcdefge";die();
        $cart   = $this->_getCart();
		
        $params = $this->getRequest()->getParams();
		// $params=array(
	    // "uenc"		=>"aHR0cDovLzEyNy4wLjAuMS9tYWdlMTQxMS9pbmRleC5waHAvY2F0YWxvZy9jYXRlZ29yeS92aWV3L3MvYXBwYXJlbC9pZC8xOC8,",
		// "product"	=>"39"
		// );
		//echo"<pre>";var_dump($params);die();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
			
            $product = $this->_initProduct();
           // $related = $this->getRequest()->getParam('related_product');
			$related= $params['related_product'];
            /**
             * Check product availability
             */
            
            if (!$product) {//echo"break";die();
                $this->_goBack();
                return;
            }
			//echo"sorry";die();
            $cart->addProduct($product, $params);//echo"<pre>";var_dump($params);die();
           // echo"die";die();
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }
			//echo"die";die();
			//echo"sorry";die();
            $cart->save();//echo"die";die();

            $this->_getSession()->setCartWasUpdated(true);//echo"die";die();
			$this->loadLayout();//echo"die";die();
			
			//die();
			$this->getLayout()->getBlock('checkout/cart_sidebar')->setTemplate('checkout/cart/sidebar.phtml');$this->renderLayout();//echo"die";die();
			// $layout = $this->getLayout();
			// $update = $layout->getUpdate();
			// $update->load('cartpro_index_BBB');
			// $layout->generateXml();
			// $layout->generateBlocks();
			// $output = $layout->getOutput();
			// echo $output;return true;
			// $layout = $this->getLayout();
			// $update = $layout->getUpdate();
			// $update->load('cartpro_index_BBB');
			// $layout->generateXml();
			// $layout->generateBlocks();
			// $outputbbb = $layout->getOutput();
			
			
			
			// $outputs = array(
				// 'AAA'=>$output,
				// 'bbb'=>$outputbbb
			// );	
			//$this->renderLayout();
			//echo"die";die();
			//return $output;
            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
			
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();//echo"break2";die();
            }
        }
        catch (Mage_Core_Exception $e) {//trong khi san pham thuoc nhom group
           // echo"break1";die();
        	if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }
			
            $url = $this->_getSession()->getRedirectUrl(true);
            //echo $url;die();
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            $this->_goBack();
        }//echo"break";die();
        //echo"sorry";die();
		return true;
		//echo"brk";die();
    }	
}