<?php
require_once 'Mage/Checkout/controllers/CartController.php';   
class Sm_Cartpro_CartController extends Mage_Checkout_CartController 
//class Sm_Cartpro_IndexController extends Mage_Core_Controller_Front_Action
{
	// public function noRouteAction($coreRoute = null){
		// echo"norooute";die();
	// }
	
    public function indexAction()
    {
		echo"aaaa";die();
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

			
		//$this->loadLayout();     
		//$this->renderLayout();
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
		$productId=(int)$params['product'];
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
		echo"<pre>";var_dump($params);die();
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
			echo"sorry";die();
            $cart->save();//echo"die";die();

            $this->_getSession()->setCartWasUpdated(true);//echo"die";die();
			$this->loadLayout();//echo"die";die();
			$this->renderLayout();
			die();
			//$this->getLayout()->getBlock('checkout/cart_sidebar')->setTemplate('checkout/cart/sidebar.phtml');//echo"die";die();
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
			return $output;
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

	
	// ////////////////////product controller///////////////////
    // protected function _initProduct()
    // {
        // Mage::dispatchEvent('catalog_controller_product_init_before', array('controller_action'=>$this));
        // $categoryId = (int) $this->getRequest()->getParam('category', false);
        // $productId  = (int) $this->getRequest()->getParam('id');

        // if (!$productId) { //neu ton tai id cua san pham
            // return false;	//neu ko ton tai
        // }
		
        // $product = Mage::getModel('catalog/product')	//moc vao bang product
            // ->setStoreId(Mage::app()->getStore()->getId())	//lay san pham theo id 
            // ->load($productId);		//tim san pham co id nhu tren
		
        // if (!Mage::helper('catalog/product')->canShow($product)) {
            // return false;
        // }
        // if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            // return false;
        // }

        // $category = null;
        // if ($categoryId) {
            // $category = Mage::getModel('catalog/category')->load($categoryId);
			
            // $product->setCategory($category);
			
            // Mage::register('current_category', $category);
        // }
        // elseif ($categoryId = Mage::getSingleton('catalog/session')->getLastVisitedCategoryId()) {
            // if ($product->canBeShowInCategory($categoryId)) {
                // $category = Mage::getModel('catalog/category')->load($categoryId);
                // $product->setCategory($category);
                // Mage::register('current_category', $category);
            // }
        // }


        // Mage::register('current_product', $product);
        // Mage::register('product', $product);

        // try {
            // Mage::dispatchEvent('catalog_controller_product_init', array('product'=>$product));
            // Mage::dispatchEvent('catalog_controller_product_init_after', array('product'=>$product, 'controller_action' => $this));
        // } catch (Mage_Core_Exception $e) {
            // Mage::logException($e);
            // return false;
        // }

        // return $product;
    // }

    // /**
     // * Initialize product view layout
     // *
     // * @param   Mage_Catalog_Model_Product $product
     // * @return  Mage_Catalog_ProductController
     // */
    // protected function _initProductLayout($product)
    // {
        // $update = $this->getLayout()->getUpdate();
        // $update->addHandle('default');
        // $this->addActionLayoutHandles();

        // $update->addHandle('PRODUCT_TYPE_'.$product->getTypeId());
        // $update->addHandle('PRODUCT_'.$product->getId());

        // if ($product->getPageLayout()) {
            // $this->getLayout()->helper('page/layout')
                // ->applyHandle($product->getPageLayout());
        // }

        // $this->loadLayoutUpdates();


        // $update->addUpdate($product->getCustomLayoutUpdate());

        // $this->generateLayoutXml()->generateLayoutBlocks();

        // if ($product->getPageLayout()) {
            // $this->getLayout()->helper('page/layout')
                // ->applyTemplate($product->getPageLayout());
        // }

        // $currentCategory = Mage::registry('current_category');
        // if ($root = $this->getLayout()->getBlock('root')) {
            // $root->addBodyClass('product-'.$product->getUrlKey());
            // if ($currentCategory instanceof Mage_Catalog_Model_Category) {
                // $root->addBodyClass('categorypath-'.$currentCategory->getUrlPath())
                    // ->addBodyClass('category-'.$currentCategory->getUrlKey());
            // }
        // }
        // return $this;
    // }

    // /**
     // * View product action
     // */
    // public function viewAction()
    // {	
        // if ($product = $this->_initProduct()) { //$url = Mage::getSingleton("catalog/session")->getRedirectUrl(true);echo $url;die();
            // Mage::dispatchEvent('catalog_controller_product_view', array('product'=>$product));

            // if ($this->getRequest()->getParam('options')) {
			
                // $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
                // Mage::getSingleton('catalog/session')->addNotice($notice);
            // }
			
            // Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());
			// //var_dump($product);die();
            // Mage::getModel('catalog/design')->applyDesign($product, Mage_Catalog_Model_Design::APPLY_FOR_PRODUCT);
			
            // $this->_initProductLayout($product);
            // $this->_initLayoutMessages('catalog/session');
            // $this->_initLayoutMessages('tag/session');
            // $this->_initLayoutMessages('checkout/session');
            // $this->renderLayout();
        // }
        // else {
            // if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                // $this->_redirect('');
            // } elseif (!$this->getResponse()->isRedirect()) {
                // $this->_forward('noRoute');
            // }
        // }
    // }

    // /**
     // * View product gallery action
     // */
    // public function galleryAction()
    // {
        // if (!$this->_initProduct()) {
            // if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                // $this->_redirect('');
            // } elseif (!$this->getResponse()->isRedirect()) {
                // $this->_forward('noRoute');
            // }
            // return;
        // }
        // $this->loadLayout();
        // $this->renderLayout();
    // }

    // /**
     // * Display product image action
     // */
    // public function imageAction()
    // {
        // $size = (string) $this->getRequest()->getParam('size');
        // if ($size) {
            // $imageFile = preg_replace("#.*/catalog/product/image/size/[0-9]*x[0-9]*#", '', $this->getRequest()->getRequestUri());
        // } else {
            // $imageFile = preg_replace("#.*/catalog/product/image#", '', $this->getRequest()->getRequestUri());
        // }

        // if (!strstr($imageFile, '.')) {
            // $this->_forward('noRoute');
            // return;
        // }

        // try {
            // $imageModel = Mage::getModel('catalog/product_image');
            // $imageModel->setSize($size)
                // ->setBaseFile($imageFile)
                // ->resize()
                // ->setWatermark( Mage::getStoreConfig('catalog/watermark/image') )
                // ->saveFile()
                // ->push();
        // } catch( Exception $e ) {
            // $this->_forward('noRoute');
        // }
    // }
	
	// /////////////CartController////////////////
    // protected $_cookieCheckActions = array('add');

    // /**
     // * Retrieve shopping cart model object
     // *
     // * @return Mage_Checkout_Model_Cart
     // */
    // protected function _getCart()
    // {
        // return Mage::getSingleton('checkout/cart');
    // }

    // /**
     // * Get checkout session model instance
     // *
     // * @return Mage_Checkout_Model_Session
     // */
    // protected function _getSession()
    // {
        // return Mage::getSingleton('checkout/session');
    // }

    // /**
     // * Get current active quote instance
     // *
     // * @return Mage_Sales_Model_Quote
     // */
    // protected function _getQuote()
    // {
        // return $this->_getCart()->getQuote();
    // }

    // /**
     // * Set back redirect url to response
     // *
     // * @return Mage_Checkout_CartController
     // */
    // protected function _goBack()
    // {
        // if ($returnUrl = $this->getRequest()->getParam('return_url')) {
            // // clear layout messages in case of external url redirect
            // if ($this->_isUrlInternal($returnUrl)) {
                // $this->_getSession()->getMessages(true);
            // }
            // $this->getResponse()->setRedirect($returnUrl);
        // } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            // && !$this->getRequest()->getParam('in_cart')
            // && $backUrl = $this->_getRefererUrl()) {

            // $this->getResponse()->setRedirect($backUrl);
        // } else {
            // if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                // $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            // }
            // $this->_redirect('checkout/cart');//echo"break3";die();
        // }
        // return $this;
    // }

    // /**
     // * Initialize product instance from request data
     // *
     // * @return Mage_Catalog_Model_Product || false
     // */
    // protected function _initProductCart()
    // {
        // $productId = (int) $this->getRequest()->getParam('product');
        // if ($productId) {
            // $product = Mage::getModel('catalog/product')
                // ->setStoreId(Mage::app()->getStore()->getId())
                // ->load($productId);
            // if ($product->getId()) {
                // return $product;
            // }
        // }
        // return false;
    // }

    // /**
     // * Shopping cart display action
     // */
    // public function indexAction()
    // {//echo"sorry";die();
        // $cart = $this->_getCart();
        // if ($cart->getQuote()->getItemsCount()) {
            // $cart->init();
            // $cart->save();

            // if (!$this->_getQuote()->validateMinimumAmount()) {
                // $warning = Mage::getStoreConfig('sales/minimum_order/description');
                // $cart->getCheckoutSession()->addNotice($warning);
            // }
        // }

        // foreach ($cart->getQuote()->getMessages() as $message) {
            // if ($message) {
                // $cart->getCheckoutSession()->addMessage($message);
            // }
        // }

        // /**
         // * if customer enteres shopping cart we should mark quote
         // * as modified bc he can has checkout page in another window.
         // */
        // $this->_getSession()->setCartWasUpdated(true);

        // Varien_Profiler::start(__METHOD__ . 'cart_display');
        // $this
            // ->loadLayout()
            // ->_initLayoutMessages('checkout/session')
            // ->_initLayoutMessages('catalog/session')
            // ->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        // $this->renderLayout();
        // Varien_Profiler::stop(__METHOD__ . 'cart_display');
    // }

    // /**
     // * Add product to shopping cart action
     // */
    // public function addAction()
    // {
        // $cart   = $this->_getCart();
		
        // $params = $this->getRequest()->getParams();
		
        // try {
            // if (isset($params['qty'])) {
                // $filter = new Zend_Filter_LocalizedToNormalized(
                    // array('locale' => Mage::app()->getLocale()->getLocaleCode())
                // );
                // $params['qty'] = $filter->filter($params['qty']);
            // }
			
            // $product = $this->_initProductCart();
            // $related = $this->getRequest()->getParam('related_product');
			
            // /**
             // * Check product availability
             // */
            
            // if (!$product) {
                // $this->_goBack();
                // return;
            // }
			// //echo"sorry";die();
            // $cart->addProduct($product, $params);//echo"<pre>";var_dump($params);die();
            
            // if (!empty($related)) {
                // $cart->addProductsByIds(explode(',', $related));
            // }
			// //echo"sorry";die();
            // $cart->save();

            // $this->_getSession()->setCartWasUpdated(true);

            // /**
             // * @todo remove wishlist observer processAddToCart
             // */
            // Mage::dispatchEvent('checkout_cart_add_product_complete',
                // array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            // );

            // if (!$this->_getSession()->getNoCartRedirect(true)) {
                // if (!$cart->getQuote()->getHasError()){
                    // $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    // $this->_getSession()->addSuccess($message);
                // }
                // $this->_goBack();//echo"break2";die();
            // }
        // }
        // catch (Mage_Core_Exception $e) {//trong khi san pham thuoc nhom group
           // // echo"break1";die();
        	// if ($this->_getSession()->getUseNotice(true)) {
                // $this->_getSession()->addNotice($e->getMessage());
            // } else {
                // $messages = array_unique(explode("\n", $e->getMessage()));
                // foreach ($messages as $message) {
                    // $this->_getSession()->addError($message);
                // }
            // }
			
            // $url = $this->_getSession()->getRedirectUrl(true);
            // //echo $url;die();
            // if ($url) {
                // $this->getResponse()->setRedirect($url);
            // } else {
                // $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            // }
			
        // }
        // catch (Exception $e) {
            // $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            // $this->_goBack();
        // }//echo"break";die();
        // //echo"sorry";die();
		// return true;
		// //echo"brk";die();
    // }

    // public function addgroupAction()
    // {
        // $orderItemIds = $this->getRequest()->getParam('order_items', array());
        // if (is_array($orderItemIds)) {
            // $itemsCollection = Mage::getModel('sales/order_item')
                // ->getCollection()
                // ->addIdFilter($orderItemIds)
                // ->load();
            // /* @var $itemsCollection Mage_Sales_Model_Mysql4_Order_Item_Collection */
            // $cart = $this->_getCart();
            // foreach ($itemsCollection as $item) {
                // try {
                    // $cart->addOrderItem($item, 1);
                // }
                // catch (Mage_Core_Exception $e) {
                    // if ($this->_getSession()->getUseNotice(true)) {
                        // $this->_getSession()->addNotice($e->getMessage());
                    // } else {
                        // $this->_getSession()->addError($e->getMessage());
                    // }
                // }
                // catch (Exception $e) {
                    // $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
                    // $this->_goBack();
                // }
            // }
            // $cart->save();
            // $this->_getSession()->setCartWasUpdated(true);
        // }
        // $this->_goBack();
    // }

    // /**
     // * Update shoping cart data action
     // */
    // public function updatePostAction()
    // {
        // try {
            // $cartData = $this->getRequest()->getParam('cart');
            // if (is_array($cartData)) {
                // $filter = new Zend_Filter_LocalizedToNormalized(
                    // array('locale' => Mage::app()->getLocale()->getLocaleCode())
                // );
                // foreach ($cartData as $index => $data) {
                    // if (isset($data['qty'])) {
                        // $cartData[$index]['qty'] = $filter->filter($data['qty']);
                    // }
                // }
                // $cart = $this->_getCart();
                // if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    // $cart->getQuote()->setCustomerId(null);
                // }
                // $cart->updateItems($cartData)
                    // ->save();
            // }
            // $this->_getSession()->setCartWasUpdated(true);
        // }
        // catch (Mage_Core_Exception $e) {
            // $this->_getSession()->addError($e->getMessage());
        // }
        // catch (Exception $e) {
            // $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
        // }
        // $this->_goBack();
    // }

    // /**
     // * Delete shoping cart item action
     // */
    // public function deleteAction()
    // {
        // $id = (int) $this->getRequest()->getParam('id');
        // if ($id) {
            // try {
                // $this->_getCart()->removeItem($id)
                  // ->save();
            // } catch (Exception $e) {
                // $this->_getSession()->addError($this->__('Cannot remove the item.'));
            // }
        // }
        // $this->_redirectReferer(Mage::getUrl('*/*'));
    // }

    // /**
     // * Initialize shipping information
     // */
    // public function estimatePostAction()
    // {
        // $country    = (string) $this->getRequest()->getParam('country_id');
        // $postcode   = (string) $this->getRequest()->getParam('estimate_postcode');
        // $city       = (string) $this->getRequest()->getParam('estimate_city');
        // $regionId   = (string) $this->getRequest()->getParam('region_id');
        // $region     = (string) $this->getRequest()->getParam('region');

        // $this->_getQuote()->getShippingAddress()
            // ->setCountryId($country)
            // ->setCity($city)
            // ->setPostcode($postcode)
            // ->setRegionId($regionId)
            // ->setRegion($region)
            // ->setCollectShippingRates(true);
        // $this->_getQuote()->save();
        // $this->_goBack();
    // }

    // public function estimateUpdatePostAction()
    // {
        // $code = (string) $this->getRequest()->getParam('estimate_method');
        // if (!empty($code)) {
            // $this->_getQuote()->getShippingAddress()->setShippingMethod($code)/*->collectTotals()*/->save();
        // }
        // $this->_goBack();
    // }

    // /**
     // * Initialize coupon
     // */
    // public function couponPostAction()
    // {
        // /**
         // * No reason continue with empty shopping cart
         // */
        // if (!$this->_getCart()->getQuote()->getItemsCount()) {
            // $this->_goBack();
            // return;
        // }

        // $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        // if ($this->getRequest()->getParam('remove') == 1) {
            // $couponCode = '';
        // }
        // $oldCouponCode = $this->_getQuote()->getCouponCode();

        // if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            // $this->_goBack();
            // return;
        // }

        // try {
            // $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            // $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                // ->collectTotals()
                // ->save();

            // if ($couponCode) {
                // if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    // $this->_getSession()->addSuccess(
                        // $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    // );
                // }
                // else {
                    // $this->_getSession()->addError(
                        // $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    // );
                // }
            // } else {
                // $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
            // }

        // }
        // catch (Mage_Core_Exception $e) {
            // $this->_getSession()->addError($e->getMessage());
        // }
        // catch (Exception $e) {
            // $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
        // }

        // $this->_goBack();
    // }
	
}