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

class MageWorx_SeoSuite_Block_Page_Html_Head extends MageWorx_SeoSuite_Block_Page_Html_Head_Abstract {
    
    public function getContentType()
    {
        $this->_data['content_type'] = $this->getMediaType().'; charset='.$this->getCharset();
        return $this->_data['content_type'];
    }
    
    public function getCssJsHtml() {
        $this->setLinkRel();        
        if ($this->setCanonicalUrl() || empty($this->_data['canonical_url'])) {
            return parent::getCssJsHtml();
        }
        $w = new Varien_Data_Collection();
        
        return '<link rel="canonical" href="' . $this->_data['canonical_url'] . '" />' . "\n" . parent::getCssJsHtml();        
    }
    
    public function setLinkRel() {
        if (!Mage::helper('seosuite')->isLinkRelEnabled()) return false;        
        
        $actionName = $this->getAction()->getFullActionName();
        if (    $actionName=='catalog_category_view' 
             || $actionName=='tag_product_list' 
             || $actionName=='catalogsearch_result_index' 
             || $actionName=='review_product_list' 
             || $actionName == 'vendors_index_index'
             || $actionName == 'umicrosite_index_index'
           ) {
            if ($actionName=='catalog_category_view' || $actionName=='catalogsearch_result_index') {
                // Category Page + Layer + Search
                $collection = $this->getLayout()->createBlock('catalog/product_list')->getLoadedProductCollection();
                $toolbar = $this->getLayout()->createBlock('page/html_pager')->setLimit($this->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())->setCollection($collection);
            } else if ($actionName=='review_product_list') {
                // Reviews
                $collection = $this->getLayout()->createBlock('review/product_view_list')->getReviewsCollection();
                $toolbar = $this->getLayout()->createBlock('page/html_pager')->setLimit($this->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())->setCollection($collection);
            } else if ($actionName=='tag_product_list') {
                // Tags
                $tag = Mage::registry('current_tag');
                if (!$tag) return false;
                $collection = $tag->getEntityCollection()
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addTagFilter($tag->getId())
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addMinimalPrice()
                    ->addUrlRewrite()
                    ->setActiveFilter();
                Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);                

                // tags
                $toolbar = $this->getLayout()                    
                    ->createBlock('page/html_pager')
                    ->setLimit($this->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())
                    ->setCollection($collection);
            } else if ($actionName == 'vendors_index_index') {
                $collection = Mage::getModel('udropship/vendor')->getCollection();
                $collection->getSelect()->joinLeft(array('vs' => Mage::getSingleton('core/resource')->getTableName('vendors_shop')), 'main_table.vendor_id = vs.vendor_id  ', array('vs.*'));
                $collection->addFieldToFilter('main_table.status', array("eq" => "A"));
                $collection->addFieldToFilter('vs.shop_status ', array("eq" => "1"));
                $toolbar = $this->getLayout()->createBlock('page/html_pager')->setLimit(Mage::getStoreConfig('udropship/vendor/vendor_page_size'))->setCollection($collection);
            } else if($actionName == 'umicrosite_index_index') {
                $collection = $this->getLayout()->createBlock('umicrosite/frontend_vendorProducts')->_getProductCollectionLeftCategory();
                $toolbar = $this->getLayout()->createBlock('page/html_pager')->setLimit($this->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())->setCollection($collection);
            }

            
            $linkPrev = false;
            $linkNext = false;
            if ($toolbar->getCollection()->getSelectCountSql()) {
                if ($toolbar->getLastPageNum() > 1) {
                    if (!$toolbar->isFirstPage()) {
                        $linkPrev = true;
                        if ($toolbar->getCurrentPage() == 2) { 
                            // remove p=1
                            $prevUrl = str_replace(array('?p=1&amp;', '?p=1&', '&amp;p=1&amp;', '&p=1&'), array('?', '?', '&amp;', '&'), $toolbar->getPreviousPageUrl());                            
                            if (substr($prevUrl, -4)=='?p=1') {
                                $prevUrl = substr($prevUrl, 0, -4);
                                $prevUrl = Mage::helper('seosuite')->_trailingSlash($prevUrl);
                            } elseif (substr($prevUrl, -8)=='&amp;p=1') {
                                $prevUrl = substr($prevUrl, 0, -8);
                            } elseif (substr($prevUrl, -4)=='&p=1') {
                                $prevUrl = substr($prevUrl, 0, -4);
                            }
                        }
                        else {
                            $prevUrl = $toolbar->getPreviousPageUrl();
                        }
                    }
                    if (!$toolbar->isLastPage()) {
                        $linkNext = true;
                        $nextUrl = $toolbar->getNextPageUrl();
                    }
                }
            }
//            if ($linkPrev) echo '<link rel="prev" href="' . $prevUrl . '" />';
//            if ($linkNext) echo '<link rel="next" href="' . $nextUrl . '" />';
            if ($linkPrev) $this->addLinkRel('prev', $prevUrl);
            if ($linkNext) $this->addLinkRel('next', $nextUrl);
            
        }
        
    }
    
    
    public function setCanonicalUrl() {
        
        if(Mage::registry('amshopby_current_params')) return ;
        if (!Mage::getStoreConfig('mageworx_seo/seosuite/enabled')) return;
        if (Mage::app()->getRequest()->getRequestedActionName()=='noRoute') return ;
        if (strpos($this->getAction()->getRequest()->getRequestString(), '/l/') !== false && !Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_canonical_tag_for_layered_navigation')) {
            return;
        }

        $canonicalUrl = null;
        $productActions = array(
            'catalog_product_view',
            'review_product_list',
            'review_product_view',
            'productquestions_show_index',
        );

        if(count(Mage::registry('layer_canonical_filter')))
        {
            $category = Mage::registry('current_category');
            if($category->getId()) {
                $this->_data['canonical_url'] = $category->getUrl();
            }
        }
        if (empty($this->_data['canonical_url'])) {
            if (in_array($this->getAction()->getFullActionName(), array_filter(preg_split('/\r?\n/', Mage::getStoreConfig('mageworx_seo/seosuite/ignore_pages'))))) {
                return;
            } elseif (in_array($this->getAction()->getFullActionName(), $productActions)) {
                $useCategories = Mage::getStoreConfigFlag('catalog/seo/product_use_categories');
                $product = Mage::registry('current_product');
                if ($product) {
                    $canonicalUrl = $product->getCanonicalUrl();
                    
                    if ($canonicalUrl) {    
                        $secure = '';
                        if(Mage::app()->getStore()->isFrontUrlSecure()) {
                            $secure = 's';
                        }
                     
                        $urlRewrite = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getId())->loadByIdPath($canonicalUrl);
                        if(strpos($urlRewrite->getRequestPath(), "http".$secure)!==true) {
                            $canonicalUrl = $urlRewrite->getRequestPath();
                        }
                        elseif(strpos($urlRewrite->getRequestPath(), "http")!==false) {
                            $canonicalUrl = $urlRewrite->getRequestPath();
                        }
                        else {
                            $canonicalUrl = $urlRewrite->getRequestPath();
                        }
                        
                    //    echo "<pre>"; print_r(); exit;
                    } else {   
                       
                        $canonicalUrl = Mage::helper('seosuite')->getUrlRewriteCanonical($product);
                        if (!$canonicalUrl) {
                            $canonicalUrl = $product->getProductUrl(false);
                            if (!$canonicalUrl) {  //|| $productCanonicalUrl == 0) {
                                $product->setDoNotUseCategoryId(!$useCategories);
                                $canonicalUrl = $product->getProductUrl(false);
                            }
                        }
                    }
                }

                if ($canonicalUrl) $canonicalUrl = Mage::helper('seosuite')->_trailingSlash(Mage::getUrl('') . $canonicalUrl);
            } elseif ($this->getAction()->getFullActionName()=='catalog_category_view' && strpos(Mage::helper('core/url')->getCurrentUrl(), 'catalog/category/view/id') && Mage::registry('current_category')) {
                $category = Mage::registry('current_category');
                if ($category->getId()) $canonicalUrl = Mage::helper('seosuite')->_trailingSlash($category->getUrl());
            } else {                
                $url = Mage::helper('core/url')->getCurrentUrl();
                $parsedUrl = parse_url($url);
                extract($parsedUrl);
                $canonicalUrl = $scheme . '://' . $host . (isset($port) && '80' != $port && Mage::getStoreConfigFlag('mageworx_seo/seosuite/add_canonical_url_port')? ':' . $port : '') . $path;                
                $canonicalUrl = Mage::helper('seosuite')->_trailingSlash($canonicalUrl);
                if (isset($parsedUrl['query']) && preg_match("/(?:^|\&)p=([0-9]+)/s", $parsedUrl['query'], $match)) {
                    $page = $match[1];
                    $canonicalUrl .= '?p='.$page;
                }
            }
            
            // apply crossDomainUrl
            $crossDomainStore = false;
            if (isset($product) && $product && $product->getCanonicalCrossDomain()) {
                $crossDomainStore = $product->getCanonicalCrossDomain();
            } elseif (Mage::getStoreConfig('mageworx_seo/seosuite/cross_domain')) {
                $crossDomainStore = Mage::getStoreConfig('mageworx_seo/seosuite/cross_domain');
            }                
            if ($crossDomainStore) {                
                $url = Mage::app()->getStore($crossDomainStore)->getBaseUrl();
                $canonicalUrl = str_replace(Mage::getUrl(), $url, $canonicalUrl);
            }             
            $this->_data['canonical_url'] =filter_var(filter_var($canonicalUrl, FILTER_SANITIZE_STRING), FILTER_SANITIZE_URL);
        }
        
        if (method_exists($this, 'addLinkRel') && !empty($this->_data['canonical_url'])) {
            $this->addLinkRel('canonical', $this->_data['canonical_url']);
            return true;
        }
    }

    public function getRobots() {        
        // standart magento
    	//$this->_data['robots'] = Mage::getStoreConfig('design/head/default_robots');
        
        //https_robots
    	if (substr(Mage::helper('core/url')->getCurrentUrl(), 0, 8)=='https://') $this->_data['robots'] = Mage::getStoreConfig('mageworx_seo/seosuite/https_robots');
        
        $noindexPatterns = explode(',', Mage::getStoreConfig('mageworx_seo/seosuite/noindex_pages'));
        foreach ($noindexPatterns as $pattern) {
         //  $pattern = str_replace(array('\\','^','$','.','[',']','|','(',')','?','*','+','{','}'),array('\\\\','\^','\$','\.','\[','\]','\|','\(','\)','\?','\*','\+','\{','\}'),$pattern);
            if (preg_match('/' . $pattern . '/', $this->getAction()->getFullActionName())) {
                $this->_data['robots'] = 'NOINDEX, FOLLOW';
                break;
            }
        }
        $noindexPatterns = array_filter(preg_split('/\r?\n/', Mage::getStoreConfig('mageworx_seo/seosuite/noindex_pages_user')));
        foreach ($noindexPatterns as $pattern) {
            $pattern = str_replace('?', '\?', $pattern);
            $pattern = str_replace('*', '.*?', $pattern);
          //  $pattern = str_replace(array('\\','^','$','.','[',']','|','(',')','?','*','+','{','}'),array('\\\\','\^','\$','\.','\[','\]','\|','\(','\)','\?','\*','\+','\{','\}'),$pattern);
            
            if (preg_match('%' . $pattern . '%', $this->getAction()->getFullActionName()) ||
                    preg_match('%' . $pattern . '%', $this->getAction()->getRequest()->getRequestString()) ||
                    preg_match('%' . $pattern . '%', $this->getAction()->getRequest()->getRequestUri()) ||
                    preg_match('%' . $pattern . '%', $this->helper('core/url')->getCurrentUrl())
            ) {
                $this->_data['robots'] = 'NOINDEX, FOLLOW';
                break;
            }
        }
        if (empty($this->_data['robots'])) {
            $this->_data['robots'] = Mage::getStoreConfig('design/head/default_robots');
        }

        return $this->_data['robots'];
    }
    
    
    public function getKeywords()
    {
        if (Mage::app()->getRequest()->getModuleName()=='splash') return parent::getKeywords();
        if(!isset($this->_data['keywords'])) $this->_data['keywords'] = '';
        if($product = Mage::registry('current_product')) {
            $origKeywords = $product->getMetaKeyword()?$product->getMetaKeyword():'';  
        } else {
            $origKeywords = $this->_data['keywords'];
        }
        $this->_data['keywords'] = '';
        
        $this->_convertLayerMeta();
        
        if (empty($this->_data['keywords'])) {
            $this->_data['keywords'] = $origKeywords;
        }
        
        if ($this->getAction()->getFullActionName() == 'xsitemap_index_index') {
            $this->_data['keywords'] = $origKeywords;        
            if (Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_keywords')!=="") {
                $this->_data['keywords'] = Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_keywords');
            }
        } 
        
        if (Mage::app()->getRequest()->getModuleName()=='cms') {
            $keywords = Mage::getSingleton('cms/page')->getData('meta_keywords');
            if ($keywords) $this->_data['keywords'] = $keywords;
        }
        return trim(htmlspecialchars(html_entity_decode($this->_data['keywords'], ENT_QUOTES, 'UTF-8')));
    }

    public function getTitle() {
        if (Mage::app()->getRequest()->getModuleName()=='splash') return parent::getTitle();
        
        if(!isset($this->_data['title'])) {
            $this->_data['title'] = '';    
        }
        
        $origTitle = (isset($this->_data['title'])?$this->_data['title']:''); 
        
        if ($this->_product || Mage::registry('current_product')) {

            $this->_product = Mage::registry('current_product');
            if (!isset($this->_data['title']) || empty($this->_data['title'])) {
                $this->_data['title'] = $this->getDefaultTitle();
            } else if ($origTitle!=$this->_data['title']) {
                $this->setTitle($this->_data['title']);
            }
        }
        
        $this->_convertLayerMeta();
        
        if (!$this->_product && Mage::app()->getRequest()->getModuleName()=='cms') {
            $title = Mage::getSingleton('cms/page')->getTitle();
            if(Mage::getSingleton('cms/page')->getMetaTitle()) {
                $title = Mage::getSingleton('cms/page')->getMetaTitle();
            }
            if(Mage::getSingleton('cms/page')->getData('published_revision_id')) {
                $collection = Mage::getResourceModel('enterprise_cms/page_revision_collection');
                $collection->getSelect()->where('revision_id=?',Mage::getSingleton('cms/page')->getData('published_revision_id'));
                $pageData = $collection->getFirstItem();
                $title = $pageData->getMetaTitle();
            }
            if ($title) $this->_data['title'] = $title;
        }

        if ($this->getAction()->getFullActionName() == 'xsitemap_index_index') {
            $this->_data['title'] = $origTitle; 
            if (Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_title')!=="") {
                $this->_data['title'] = Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_title');
            }
        }

        if(!$this->_data['title']) {
            $this->_data['title'] = $origTitle;
        }
        $this->_data['title'] = trim(htmlspecialchars(html_entity_decode($this->_data['title'], ENT_QUOTES, 'UTF-8')));
        return $this->_data['title'];
    }

    public function getDescription() {
        if (Mage::app()->getRequest()->getModuleName()=='splash') return parent::getDescription();
        if($product = Mage::registry('current_product')) {
            $oldDescription = $product->getMetaDescription()?$product->getMetaDescription():$this->_data['description'];
        } else {
            $oldDescription = isset($this->_data['description'])?$this->_data['description']:"";
        }
        $this->_data['description'] = '';
        
        $this->_convertLayerMeta();
       
        if (empty($this->_data['description'])) {
            $category = Mage::registry('current_category');
            if ($category && Mage::registry('current_product')==null) {                
                $this->_data['description'] = $category->getMetaDescription() ? $category->getMetaDescription() : $oldDescription;
            } else {
                $this->_data['description'] = $oldDescription;
            }
        }
        
        if (Mage::app()->getRequest()->getModuleName()=='cms') {
            $description = Mage::getSingleton('cms/page')->getMetaDescription();
            if ($description) $this->_data['description'] = $description;
        }
        
        if ($this->getAction()->getFullActionName() == 'xsitemap_index_index') {
            $this->_data['description'] = $oldDescription; 
            if (Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_desc')!=="") {
                $this->_data['description'] = Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_desc');
            }
        } 
          
        $stripTags = new Zend_Filter_StripTags();

        return $this->_data['description']=htmlspecialchars(html_entity_decode(preg_replace(array('/\r?\n/', '/[ ]{2,}/'), array(' ', ' '), $stripTags->filter($this->_data['description'])), ENT_QUOTES, 'UTF-8'));
    }

    public function checkEmptyData($emptyData) {
        $object = false;
        if(Mage::registry('current_category')) {
            $object = Mage::registry('current_category');
        }
        if (Mage::registry('current_product')) {
           $object = Mage::registry('current_product');
        }
        if(!$object) return $emptyData;
        $emptyData['meta_title'] = $object->getMetaTitle()?false:true;
        $emptyData['meta_description']=$object->getMetaTitle()?false:true;
        $emptyData['meta_keywords']=$object->getMetaTitle()?false:true; 
        return $emptyData;   
    }
    
    private function _convertLayerMeta() {
       
        $helper = Mage::helper('seosuite');
        $request = Mage::app()->getRequest();
        $emptyData = array('meta_title'=>false,'meta_description'=>false,'meta_keywords'=>false);
        $emptyData = $this->checkEmptyData($emptyData);
        
        $hideAttributes = Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_hide_attributes');
        $layeredFriendlyUrls = Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_friendly_urls');
        
        $metaTemplate = '';
        $params = Mage::app()->getRequest()->getParams();
        
// get meta title
        
        if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_title') || $emptyData['meta_title']) {
            $type = '';
            $object = '';
            if(Mage::registry('current_category')) {
                $type = 'category';
                $object = Mage::registry('current_category');
                $metaTemplate = Mage::getModel('seosuite/template')->loadCategoryTitle();
            }
            if (Mage::registry('current_product')) {
                $type = 'product';
                $object = Mage::registry('current_product');
                $metaTemplate = Mage::getModel('seosuite/template')->loadTitle();            
            }
            if($metaTemplate) {
                $metaTitle = $this->__compile($object,$metaTemplate,$type);
                $this->setTitle($metaTitle);
            }
            
        }    
        
// get meta description enable_dynamic_meta_desc
        if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_desc') || $emptyData['meta_description']) {
            $type = '';
            $object = '';
            if(Mage::registry('current_category')) {
                $type = 'category';
                $object = Mage::registry('current_category');
                $metaTemplate = Mage::getModel('seosuite/template')->loadCategoryMetaDescription(); 
            }
            if (Mage::registry('current_product')) {
                $type = 'product';
                $object = Mage::registry('current_product');
                $metaTemplate = Mage::getModel('seosuite/template')->loadMetaDescription();
            }
            if($metaTemplate) {
                $metaDescr = $this->__compile($object,$metaTemplate,$type);
                $this->_data['description'] = $metaDescr;
            }
        }  
        
// get meta keywords enable_dynamic_meta_keywords
        if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_keywords') || $emptyData['meta_keywords']) {
            $type = '';
            $object = '';
            if(Mage::registry('current_category')) {
                $type = 'category';
                $object = Mage::registry('current_category');
                $metaTemplate = Mage::getModel('seosuite/template')->loadCategoryMetaKeywords(); 
            }
            if (Mage::registry('current_product')) {
                $type = 'product';
                $object = Mage::registry('current_product');
                 $metaTemplate = Mage::getModel('seosuite/template')->loadKeywords();
            }
            if($metaTemplate) {
                $metaKeywords = $this->__compile($object,$metaTemplate,$type);
                $this->_data['keywords'] = $metaKeywords;
            }
        }  
        
        if ($layeredFriendlyUrls) {
            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');            
            $identifier = trim(($suffix && substr($request->getOriginalPathInfo(), -(strlen($suffix)))==$suffix?substr($request->getOriginalPathInfo(), 0, -(strlen($suffix))):$request->getOriginalPathInfo()), '/');
            $urlSplit = explode('/l/', $identifier, 2);
            if (!isset($urlSplit[1])) return false;            
            $productUrl = Mage::getModel('catalog/product_url');
            list($cat, $params) = $urlSplit;
            $layerParams = explode('/', $params);
            $_params = array();

            $descParts = array();

            // from registry
            $attr = $helper->_getFilterableAttributes();
            $titleParts = array(trim($this->_data['title']));
            if (isset($this->_data['description']) && trim($this->_data['description'])) {
                $descParts = array(trim($this->_data['description']));
            }
            if (count($layerParams)) {
                foreach ($layerParams as $params) {   
                  
                    $param = explode($helper->getAttributeParamDelimiter(), $params, 2);
                    if (count($param) == 1) {
                        if(strpos(current($param),Mage::getStoreConfig('mageworx_seo/seosuite/layered_separatort'))!==false) {
                            list($_param,$value) = explode(Mage::getStoreConfig('mageworx_seo/seosuite/layered_separatort'),current($param));
                            $param[0] = $_param;
                            if(current($param) == 'price') {
                                $_value = explode("-",$value);
                                $value = Mage::helper('core')->currency($_value[0],true,false).'-'.Mage::helper('core')->currency($_value[1],true,false);
                                $titleParts[] = $value;
                                $descParts[] = $value;
                                continue;
                            }
                        }
                        
                        $cat = Mage::getModel('seosuite/catalog_category')
                                ->setStoreId(Mage::app()->getStore()->getId())
                                ->loadByAttribute('url_key', $productUrl->formatUrlKey($param[0]));
                        if ($cat && $cat->getId()) {
                            $titleParts[0] .= ' - ' . $cat->getName();
                            continue;
                        }
                      
                        foreach ($attr as $attribute) {
                            if (isset($attribute['options'][current($param)])) {
                                $titleParts[] = $attribute['options'][current($param)];
                                $descParts[] = $attribute['options'][current($param)];
                                break;
                            }
                        }
                    } else {
                        $code = str_replace('-', '_', $param[0]); // attrCode is only = [a-z0-9_]
                        if (isset($attr[$code])) {
                            if ($code == 'price') {
                                //$multipliers = explode(',', $param[1].',0');
                                $multipliers = explode(',', $param[1]);
                                $frontendLabel = $hideAttributes ? '' : (isset($attr[$code]['frontend_label'])?$attr[$code]['frontend_label']:'');
                                if (isset($multipliers[1])) {
                                    $titleParts[] = $descParts[] = $frontendLabel . ' ' . Mage::app()->getStore()->formatPrice($multipliers[0] * $multipliers[1] - $multipliers[1], false) . ' - ' . Mage::app()->getStore()->formatPrice($multipliers[0] * $multipliers[1], false);
                                } else {
                                    if (strpos($multipliers[0], '-')!==false) {
                                        $multipliers = explode('-', $multipliers[0]);
                                        $priceFrom = Mage::app()->getStore()->formatPrice(floatval($multipliers[0]), false);
                                        $priceTo = (!$multipliers[1]?$this->__('And Above'):Mage::app()->getStore()->formatPrice(floatval($multipliers[1]), false));
                                        $titleParts[] = $descParts[] = $frontendLabel . ' ' . $priceFrom . ' - ' . $priceTo; 
                                    }
                                }
                                continue; 
                            }
                            if (isset($attr[$code]['frontend_label']) && isset($attr[$code]['options'][$param[1]])) $titleParts[] = $descParts[] = $attr[$code]['frontend_label'] . ' - ' . $attr[$code]['options'][$param[1]];
                        }
                    }
                }
            }
            if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_title')) {
                $this->_data['title'] = implode(', ', $titleParts);
            }

            if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_desc')) {
                $this->_data['description'] = implode(', ', $descParts);
            }
        }
    }

    protected function __compile($object,$template,$type='product') {
        if(!$object) return '';
        $template = Mage::getModel('seosuite/catalog_'.$type.'_template_title')->getCompile($object,$template);
        return $template;
    }

}