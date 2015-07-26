<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */
class MageWorx_SeoSuite_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard {

    protected $_request = null;
    protected $_urlData = null;

    protected function _beforeModuleMatch()
    {
        return true;
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }
    
    public function initControllerRouters($observer) {
        $front = $observer->getEvent()->getFront();
      //  Varien_Autoload::registerScope('catalog');
        $router = new MageWorx_SeoSuite_Controller_Router();
        $front->addRouter('seosuite', $router);
    }

    public function match(Zend_Controller_Request_Http $request) {
        $this->_beforeModuleMatch();
        
        $this->_setRequest($request);                
        
        if ($this->_matchCategoryPager($request)) {
            return true;
    	}
        
        if ($this->_matchCategoryLayer() && !Mage::getStoreConfig('mageworx_seo/seosuite/disable_layered_rewrites')) {
            return true;
        }

        $identifier = trim($request->getPathInfo(), '/');
        
        $d = explode('/', $identifier);

        if (count($d) < 2) {
            return false;
        }

        if ('reviews' == $d[1]) {
            $product = Mage::getModel('catalog/product')->loadByAttribute('url_key', $d[0]);
            if (!$product || !$product->getId()) {
                $keyArr = explode('-', $d[0]);
                $productId = end($keyArr);
                if (!is_numeric($productId)){
                	$urlRewrite = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getId())->loadByRequestPath($d[0]);
					$productId = $urlRewrite->getProductId();
					if (!is_numeric($productId)){
						return false;
					}
                }
                $product = Mage::getModel('catalog/product')->load($productId);
                if (!$product || !$product->getId()) return false;               
                $urlArr = explode('/', $product->getProductUrl());
                $urlKey = substr(end($urlArr), 0, -(strlen(Mage::getStoreConfig('catalog/seo/product_url_suffix'))));
                $request->setParam('category', $product->getCategoryId());
            }

            if (isset($d[2]) && $d[2] != 'category') {
                if (isset($d[3]) && is_numeric($d[3])) {
                    $reviewId = $d[3];
                    $request->setActionName('view')
                        ->setParam('id', $reviewId);
                }
                
            } else {
                if (isset($d[3])) {
                    $category = Mage::getModel('seosuite/catalog_category')->loadByAttribute('url_key', $d[3]);
                    if ($category && $categoryId = $category->getId()) {
                        $request->setParam('category', $categoryId);
                    }
                }
                $request->setActionName('list')
                        ->setParam('id', $product->getId());
            }

            $request->setModuleName('review')
                    ->setControllerName('product')
                    ->setAlias(
                            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'review'
            );

            return true;
        }
        
        switch ($d[0]) {
            case 'tag':
                if (!isset($d[1])) {
                    return false;
                }
                if (count($d) > 2 || in_array($d[1], array('index', 'customer', 'list'))) {
                    return false;
                }
                $tag = Mage::getModel('tag/tag')->load(urldecode($d[1]), 'name');

                if (!$tag->getId() || !$tag->isAvailableInStore()) {
                    return false;
				}

                $request->setModuleName('tag')->setControllerName('product')->setActionName('list')
						->setParam('tagId', $tag->getId());

                break;
            case 'rss':
                if (!isset($d[1]) || !isset($d[2])) {
                    return false;
                }
                if (count($d) > 4 || in_array($d[1], array('order'))) {
                    return false;
                }
                $storeId = Mage::app()->getStore($d[1])->getId();
                $t = null;
                if ($d[2]{0} == '@') {
                    $t = substr($d[2], 1);
                }
                switch ($t) {
                    case 'new':
                        $request->setActionName('new')
                                ->setParam('store_id', $storeId);
                        break;
                    case 'specials':
                        $request->setActionName('special')
                                ->setParam('cid', $d[3])
                                ->setParam('store_id', $storeId);
                        break;
                    case 'discounts':
                        $request->setActionName('salesrule')
                                ->setParam('cid', $d[3])
                                ->setParam('store_id', $storeId);
                        break;
                    default:
                        $category = Mage::getModel('seosuite/catalog_category')->setStoreId($storeId)->loadByAttribute('url_key', $d[2]);
                        if (!$category || !$category->getId()) {
                            return false;
                        }
                        $request->setActionName('category')
                                ->setParam('cid', $category->getId())
                                ->setParam('store_id', $storeId);
                }
                $request->setModuleName('rss')
                        ->setControllerName('catalog');
                break;
            default:
                
                $seoCode = 301;
                if(!Mage::getStoreConfig('catalog/seo/save_rewrites_history')) {
                    $seoCode = 302;
                }
                // if changed product category -> check product by url_key and redirect to canonicalUrl
                $catRewriteModel = $this->_getCategoryUrlRewrite($identifier); //$identifier
                if (!$catRewriteModel) {
                    $catRewriteModel = $this->_getCategoryUrlRewrite($d[count($d)-1]);
                    if ($catRewriteModel && $catRewriteModel->getProductId()) {                        
                        $product = Mage::getModel('catalog/product')->load($catRewriteModel->getProductId());
                        if ($product && $product->getId()>0) {
                            $canonicalUrl = Mage::helper('seosuite')->getCanonicalUrl($product);
                            if ($canonicalUrl) {
                                //var_dump($canonicalUrl); exit;
                                Mage::app()->getFrontController()->getResponse()->setRedirect($canonicalUrl)->setHttpResponseCode($seoCode)->sendResponse(); exit;
                            }
                        }
                        
                        
                    }
                }
                
                return false;
        }
        
        $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        
        return true;
    }

    protected function _matchCategoryPager($request) {
        
        $pagerUrlFormat = Mage::helper('seosuite')->getPagerUrlFormat();
        if (!$pagerUrlFormat) return false;
        
        $url = $request->getRequestUri();
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        
        $pagerUrlFormatRegEx = explode('[page_number]', $pagerUrlFormat);
        foreach ($pagerUrlFormatRegEx as $key=>$part) {
            $pagerUrlFormatRegEx[$key] = preg_quote($part, '/');
        }
        $pagerUrlFormatRegEx = implode('([0-9]+)', $pagerUrlFormatRegEx);
        if (preg_match('/' . $pagerUrlFormatRegEx . preg_quote($suffix, '/') . '/', $url, $match)) {
            
            $url = str_replace($match[0], $suffix, $url);
            $request->setRequestUri($url);

            $path = $request->getPathInfo();
            $path = str_replace($match[0], $suffix, $path);
            $request->setPathInfo($path);
            $request->setParam('p', $match[1]);
        } else {
            return false;
        }
        
        $identifier = trim(($suffix && substr($request->getPathInfo(), -(strlen($suffix)))==$suffix?substr($request->getPathInfo(), 0, -(strlen($suffix))):$request->getPathInfo()), '/');
                
        $urlSplit = explode('/l/', $identifier, 2);
        if (isset($urlSplit[1])) {
            return false;
        }
        
        $productUrl = Mage::getModel('catalog/product_url');
        $cat = $identifier;
        $_params = array();

        $catPath = $cat . $suffix;
        $isVersionEE13 = ('true' == (string)Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
        if ($isVersionEE13){
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');
            /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */

            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catPath);
        }
        else {
            $urlRewrite = Mage::getModel('core/url_rewrite');
            /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */

            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catPath);
        }

        // todo check in ee13
        if (!$urlRewrite->getId()){
            $store = $request->getParam('___from_store'); 
            $store = Mage::app()->getStore($store)->getId();  
            if (!$store){
                return false;  
            }

            $urlRewrite->setData(array())
                ->setStoreId($store)
                ->loadByRequestPath($catPath);

            if (!$urlRewrite->getId()){
                return false;
            }
        }
        if ($urlRewrite && $urlRewrite->getId()) {
            $request->setPathInfo($catPath);
            $request->setModuleName('catalog')
                    ->setControllerName('category')
                    ->setActionName('view')
                    ->setParam('id', $urlRewrite->getCategoryId())
                    ->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'catalog');            
            $urlRewrite->rewrite($request);
            return true;
        }
        return false;
    }
    
    protected function _matchProductAndCategory() {
        list($catPath, $layerParams) = $this->_getUrlData();
        if (!isset($layerParams) || !isset($catPath)) return false;        

        $urlRewrite = $this->_getCategoryUrlRewrite($catPath);
        if ($urlRewrite && $urlRewrite->getId()) {
            $this->_prepareRequestForUrlRewrite($urlRewrite, $catPath);

            if (count($layerParams)) {
                $this->_passLayerParamsToRequest($layerParams);
                $urlRewrite->rewrite($this->_getRequest());
                return true;
            }
        }
        return false;
    }
    
    
    protected function _matchCategoryLayer() {
        list($catPath, $layerParams) = $this->_getUrlData();
        if (!isset($layerParams) || !isset($catPath)) return false;        

        $urlRewrite = $this->_getCategoryUrlRewrite($catPath);
        if ($urlRewrite && $urlRewrite->getId()) {
            $this->_prepareRequestForUrlRewrite($urlRewrite, $catPath);

            if (count($layerParams)) {
                $this->_passLayerParamsToRequest($layerParams);
                $isVersionEE13 = ('true' == (string)Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
                if ($isVersionEE13) {
                    $this->_getRequest()->setParams($layerParams)
                            ->setParam('id',$urlRewrite->getValueId())
                            ->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $catPath);
                }
                else {
                    $this->_getRequest()->setParams($layerParams);
                    $urlRewrite->rewrite($this->_getRequest());
                }
                return true;                
            }
        }
        return false;
    }

    protected function _prepareRequestForUrlRewrite($urlRewrite, $catPath) {
        $this->_getRequest()->setPathInfo($catPath);
        $this->_getRequest()->setModuleName('catalog')
                ->setControllerName('category')
                ->setActionName('view')
                ->setParam('id', $urlRewrite->getCategoryId())
                ->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'catalog'
                );
    }

    protected function _getFilterableCategoryAttributesArray($category) {
        $modelLayer = Mage::getModel('catalog/layer')->setData('current_category', $category);
        $attributes = $modelLayer->getFilterableAttributes();
        $attr = array();

        foreach ($attributes as $attribute) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $attr[] = $option['label'];
            }
        }

        return $attr;
    }

    protected function _isCategoryNameDuplicatesAttribute($category) {
        $attributes = $this->_getFilterableCategoryAttributesArray($category);
        return in_array($category->getName(), $attributes);
    }

    protected function _getCategoryUrlRewrite($catPath) {
        $isVersionEE13 = ('true' == (string)Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
        if ($isVersionEE13){
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');
            /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */

            if(!is_array($catPath)) {
                $catPath = array($catPath);
            }
            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catPath);
        }
        else {
            $urlRewrite = Mage::getModel('core/url_rewrite');
            /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */
            if(is_array($catPath)) {
                $catPath = array_shift($catPath);
            }
            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catPath);
        }

        // todo check in ee13
        if (!$urlRewrite->getId()){
            $store = $this->_getRequest()->getParam('___from_store'); 
            $store = Mage::app()->getStore($store)->getId();  
            if (!$store){
                return false;  
            }

            $urlRewrite->setData(array())
                ->setStoreId($store)
                ->loadByRequestPath($catPath);

            if (!$urlRewrite->getId()){
                return false;
            }
        }
        if ($urlRewrite->getId()) return $urlRewrite; else return null;
    }

    protected function _getUrlData() {
        if (!$this->_urlData) {
            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            $request = $this->_getRequest();
            if($request->getPathInfo()!==$request->getOriginalPathInfo()) {
                $request->setPathInfo($request->getOriginalPathInfo());
            }
            $identifier = trim(($suffix && substr($request->getPathInfo(), -(strlen($suffix)))==$suffix?substr($request->getPathInfo(), 0, -(strlen($suffix))):$request->getPathInfo()), '/');

            $urlSplit = explode('/l/', $identifier, 2);
            if (isset($urlSplit[1])) {
                $urlSplit[1] = explode('/', $urlSplit[1]);
            } else {
                $urlSplit[1] = array();
            }
            $urlSplit[0] .= $suffix;
            $this->_urlData = $urlSplit;
        }
        return $this->_urlData;
    }

    protected function _getCategoryPathInArray() {
        $urlData = $this->_getUrlData();
        return $urlData[1];
    }

    protected function _getCategoryByParam($param) {
        return false;
        if (count($param) == 1 && !$this->_getRequest()->getQuery('cat')) {
            $productUrl = Mage::getModel('catalog/product_url');

            $cat = Mage::getModel('seosuite/catalog_category')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->loadByAttribute('url_key', $productUrl->formatUrlKey($param[0]));
            if (!$cat) {
                $name = str_replace('-', ' ', $productUrl->formatUrlKey($param[0]));
                $cat = Mage::getModel('seosuite/catalog_category')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->loadByAttribute('name', $name);
            }
            if ($cat && $cat->getId() && !in_array($cat->getUrlKey(), $this->_getCategoryPathInArray())) {
                if (!$this->_isCategoryNameDuplicatesAttribute($cat)) {
                    return $cat;
                }
            }
        }

        return false;
    }

    protected function _isHiddenAttribute($param) {
        return count($param) == 1;
    }

    protected function _isHiddenPriceAttribute($param) {
        $localParam = explode(Mage::helper('seosuite')->getAttributeValueDelimiter(), $param[0]);
        return count($localParam) == 2 && $localParam[0] === 'price';
    }

    protected function _setHiddenAttributeToRequest($param) {
        $attr = Mage::helper('seosuite')->_getFilterableAttributes($this->_getCategoryId());
        foreach ($attr as $attrCode => $attrData) {
            if (isset($attrData['options'][$param[0]])) {
                $this->_getRequest()->setQuery($attrCode, $attrData['options'][$param[0]]);
                break;
            }
        }
    }
    
    protected function _setPriceAttributeToRequest($param) {
        $priceParam = explode(Mage::helper('seosuite')->getAttributeValueDelimiter(), $param[0]);
        $this->_getRequest()->setQuery($priceParam[0], $priceParam[1]);
    }

    protected function _setCategoryToRequest($cat) {
        $this->_getRequest()->setQuery('cat', $cat->getName());
    }
    
    protected function _getCategoryId() {        
        $catId = false;
        $catPath = $this->_getUrlData();
        if ($catPath) {
            $catRewriteModel = $this->_getCategoryUrlRewrite($catPath);
            if ($catRewriteModel) {
                $catId = $catRewriteModel->getCategoryId();
                $isVersionEE13 = ('true' == (string)Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
                if ($isVersionEE13){
                    $catId = $catRewriteModel->getValueId();
                }
            }
        }
        return $catId;
    }
    
    protected function _setNotHiddenAttributeToRequest($param) {
        $code = $param[0];
        $value = $param[1];
        if ($code == 'price') {
         // custom!!
         if (strpos($value, '-')!==false) {
          $multipliers = explode('-', $value);
          $priceFrom = floatval($multipliers[0]);
          $priceTo = ($multipliers[1]?floatval($multipliers[1])+0.01:$multipliers[1]);
           $value = $priceFrom . '-' . $priceTo;
         }
            $this->_getRequest()->setQuery($code, $value);
        }
                
        $attr = Mage::helper('seosuite')->_getFilterableAttributes($this->_getCategoryId());
        if (isset($attr) && !empty($attr)) {            
            $code = str_replace('-', '_', $code); // attrCode is only = [a-z0-9_]            
            if (isset($attr[$code]) && isset($attr[$code]['options'][$value])) {
                $this->_getRequest()->setQuery($code, $attr[$code]['options'][$value]);
            }
        }
    }

    protected function _passLayerParamsToRequest($layerParams) {
        if (empty($layerParams)) return;
         $this->_getRequest()->setParam('seo_layered_params',$layerParams);
         return $this;
    }
    
    public function parseLayeredParams() {
        $this->_setRequest(Mage::app()->getRequest());
        $layerParams = $this->_getRequest()->getParam('seo_layered_params',array());
        foreach ($layerParams as $params) {
            $param = explode(Mage::helper('seosuite')->getAttributeParamDelimiter(), $params, 2);
            if (empty($param)) continue;
            if ($cat = $this->_getCategoryByParam($param)) {
                $this->_setCategoryToRequest($cat);
            } else if ($this->_isHiddenAttribute($param)) {
                if ($this->_isHiddenPriceAttribute($param)) {
                    $this->_setPriceAttributeToRequest($param);
                } else {
                    $this->_setHiddenAttributeToRequest($param);
                }
            } else {
                $this->_setNotHiddenAttributeToRequest($param);
            }
        }
    }

    protected function _setRequest($request) {
        $this->_request = $request;
    }

    protected function _getRequest() {
        return $this->_request;
    }

}