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
class MageWorx_SeoSuite_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_SEOSUITE_PRODUCT_REPORT_STATUS = 'mageworx_seo/seosuite/product_report_status';
    const XML_PATH_SEOSUITE_CATEGORY_REPORT_STATUS = 'mageworx_seo/seosuite/category_report_status';
    const XML_PATH_SEOSUITE_CMS_REPORT_STATUS = 'mageworx_seo/seosuite/cms_report_status';
    
    public function setProductReportStatus($flag) {
        Mage::getConfig()->saveConfig(self::XML_PATH_SEOSUITE_PRODUCT_REPORT_STATUS, $flag);
    }
    
    public function getProductReportStatus() {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEOSUITE_PRODUCT_REPORT_STATUS);
    }
    
    public function setCategoryReportStatus($flag) {
        Mage::getConfig()->saveConfig(self::XML_PATH_SEOSUITE_CATEGORY_REPORT_STATUS, $flag);
    }
    
    public function getCategoryReportStatus() {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEOSUITE_CATEGORY_REPORT_STATUS);
    }
    
    public function setCmsReportStatus($flag) {
        // if doesn't work, check save new row in DB!!!!!!!!
        Mage::getConfig()->saveConfig(self::XML_PATH_SEOSUITE_CMS_REPORT_STATUS, $flag);
    }
    
    public function getCmsReportStatus() {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEOSUITE_CMS_REPORT_STATUS);
    }
    
    public function isTrailingSlashEnabled() {
        return Mage::getStoreConfigFlag('mageworx_seo/seosuite/trailing_slash');
    }
    
    public function _trailingSlash($url) {
        if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/trailing_slash') && substr($url, -1)!='/' && !in_array(substr(strrchr($url, '.'), 1), array('rss', 'html', 'htm', 'xml', 'php'))) {
            $url.= '/';
        }   
        return $url;
    }
    
    public function getRssGenerator() {
        return base64_decode('TWFnZVdvcnggU0VPIFN1aXRlIChodHRwOi8vd3d3Lm1hZ2V3b3J4LmNvbS8p');
    }
    
    
    
    public function getAttributeValueDelimiter() {
        return Mage::getStoreConfig('mageworx_seo/seosuite/layered_separatort');        
        //return ':';
    }
    
    public function getAttributeParamDelimiter() {
        return Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_hide_attributes') ? '/' : $this->getAttributeValueDelimiter();
    }
    
    public function getPagerUrlFormat() {
        $pagerUrlFormat = Mage::getStoreConfig('mageworx_seo/seosuite/pager_url_format');
        if (strpos($pagerUrlFormat, '[page_number]')!==false) return $pagerUrlFormat;
        return false;
    }
    
    public function isLinkRelEnabled() {
        return Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_link_rel');        
    }
    

    public function _getFilterableAttributes($catId = false) {
        if (!is_null(Mage::registry('_layer_filterable_attributes'))) return Mage::registry('_layer_filterable_attributes');
        
        $hlp = Mage::helper('seosuite');
        $attr = array();
        
        $layerModel = Mage::getModel('catalog/layer');
        if ($catId) $layerModel->setCurrentCategory($catId);        
        $attributes = $layerModel->getFilterableAttributes();
        
        foreach ($attributes as $attribute) {
            $attr[$attribute->getAttributeCode()]['type'] = $attribute->getBackendType();
            $options = $attribute->getSource()->getAllOptions();
            foreach ($options as $option) {
                $attr[$attribute->getAttributeCode()]['options'][$hlp->formatUrlKey($option['label'])] = $option['label'];
                $attr[$attribute->getAttributeCode()]['frontend_label'] = $attribute->getFrontendLabel();
            }
        }
        Mage::register('_layer_filterable_attributes', $attr);
        return $attr;
    }

    public function getLayerFilterUrl($params) {
        if (!Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_friendly_urls')) {
            return Mage::getUrl('*/*/*', $params);
        }
        $hideAttributes = Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_hide_attributes');

        $urlModel = Mage::getModel('core/url');

        $queryParams = $urlModel->getRequest()->getQuery();
        if (isset($queryParams['price']) && is_array($queryParams['price'])) {
            $queryParams['price'] = join(' ',$queryParams['price']);
        }
        if (isset($queryParams['price']) && strpos($queryParams['price'], '-')!==false) {
         $multipliers = explode('-', $queryParams['price']);
         $priceFrom = floatval($multipliers[0]);
         $priceTo = (!$multipliers[1]?'':floatval($multipliers[1])-0.01);
         $queryParams['price'] = $priceFrom . '-' . $priceTo;
        }
        
        foreach ($params['_query'] as $param => $value) {
            $queryParams[$param] = $value;
        }
        $queryParams = array_filter($queryParams);
        //$attr = Mage::registry('_layer_filterable_attributes');
        $attr = $this->_getFilterableAttributes();

        $layerParams = array();
        foreach ($queryParams as $param => $value) {
            if ($param == 'cat' || isset($attr[$param])) {
                switch ($hideAttributes) {
                    case true:
                        $layerParams[$param == 'cat' ? 0 : $param] = ($param == 'cat' ? $this->formatUrlKey($value) : ($attr[$param]['type'] == 'decimal' ? $this->formatUrlKey($param) . $this->getAttributeValueDelimiter() . $value : $this->formatUrlKey($value)));
                        break;
                    default:
                        $layerParams[$param == 'cat' ? 0 : $param] = ($param == 'cat' ? $this->formatUrlKey($value) : $this->formatUrlKey($param) . $this->getAttributeValueDelimiter() . ($attr[$param]['type'] == 'decimal' ? $value : $this->formatUrlKey($value)));
                        break;
                }
                $params['_query'][$param] = null;
            }
        }
        $layer = null;
        if (!empty($layerParams)) {
            uksort($layerParams, 'strcmp');
            $layer = implode('/', $layerParams);
        }
        $url = Mage::getUrl('*/*/*', $params);
        if (!$layer) return $url;
        
        $urlParts = explode('?', $url, 2);
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');        
        $url = ($suffix && substr($urlParts[0], -(strlen($suffix)))==$suffix?substr($urlParts[0], 0, -(strlen($suffix))):$urlParts[0]);        
        return $url . '/l/' . $layer . $suffix . (isset($urlParts[1]) ? '?' . $urlParts[1] : '');
    }

    private function _sortUrlRewriteColletion($collection) {
        $list = array();
        foreach ($collection as $item) {
            $count = count(array_filter(explode('/',$item->getRequestPath())));
            if(!isset($list[$count])) $list[$count]=array();
            $list[$count][strlen($item->getRequestPath())] = $item->getRequestPath();
            ksort($list[$count]);
        }
        if(isset($list[1])) unset($list[1]);
        ksort($list);
        return $list;
    }
    public function getUrlRewriteCanonical($product) {
        $canonicalUrl = '';
        $productCanonicalUrl = Mage::getStoreConfig('mageworx_seo/seosuite/product_canonical_url');
            $collection = Mage::getResourceModel('seosuite/core_url_rewrite_collection')
                    ->filterAllByProductId($product->getId(), $productCanonicalUrl)
                    ->addStoreFilter(Mage::app()->getStore()->getId(), false);
            
            $urlRewrite = $collection->getFirstItem();
            if ($urlRewrite && $urlRewrite->getRequestPath()) {
                    switch($productCanonicalUrl) {
                        case "1": //Use Longest by url
                        case "2": //Use Shortest  by url
                            $canonicalUrl = $urlRewrite->getRequestPath();
                            break;
                        case "3": // use root
                            $canonicalUrlArr = explode('/', $urlRewrite->getRequestPath());
                            $canonicalUrl = end($canonicalUrlArr);
                            $canonicalUrlArr = explode('/', $canonicalUrl);
                            $canonicalUrl = end($canonicalUrlArr);
                            $canonicalUrl = trim($canonicalUrl,'/');
                            break;
                        case "4": //Use Longest by category
                            $list = $this->_sortUrlRewriteColletion($collection);
                            $maxItem = array_pop($list);
                            if(is_array($maxItem)) {
                                $canonicalUrl = array_pop($maxItem);
                            } else {
                                $canonicalUrl = $maxItem;
                            }
                            break;
                        case "5": //Use Shortest by category
                            $list = $this->_sortUrlRewriteColletion($collection);
                            $minItem = array_shift($list);
                            if(is_array($minItem)) {
                                $canonicalUrl = array_shift($minItem);
                            } else {
                                $canonicalUrl = $minItem;
                            }
                            break;
                         
                    }
                    $secure = '';
                    if(Mage::app()->getStore()->isFrontUrlSecure()) {
                        $secure = 's';
                    }
                    if(strpos($urlRewrite->getRequestPath(), "http".$secure)!==true) {
                    }
                    elseif(strpos($urlRewrite->getRequestPath(), "http")!==false) {
                    }
                    else {
                        $canonicalUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $canonicalUrl;
                    }
                    $canonicalUrl = trim($canonicalUrl,'/');	
            }
            return $canonicalUrl;
    }

    public function getCanonicalUrl($product) {
        if (!Mage::getStoreConfig('mageworx_seo/seosuite/enabled')) return;

        $canonicalUrl = null;

        $productActions = array(
            'catalog_product_view',
            'review_product_list',
            'review_product_view',
            'productquestions_show_index',
        );
        
        $useCategories = Mage::getStoreConfigFlag('catalog/seo/product_use_categories');
       
        $canonicalUrl = $product->getCanonicalUrl();
        $canonicalUrl = trim($canonicalUrl,'/');
        if ($canonicalUrl) {
            $urlRewrite = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getId())->loadByIdPath($canonicalUrl);
            $canonicalUrl = Mage::getUrl('') . $urlRewrite->getRequestPath();
        } else {
            
            $canonicalUrl = $this->getUrlRewriteCanonical($product);

            if (!$canonicalUrl) {
                $canonicalUrl = $product->getProductUrl('mw_false');        # fix recursion
                if (!$canonicalUrl || $productCanonicalUrl == 0) {
                    $product->setDoNotUseCategoryId(!$useCategories);
                    $canonicalUrl = $product->getProductUrl('mw_false');	# fix recursion
                }
            }
        }
        $canonicalUrl = trim($canonicalUrl,'/');
        if ($canonicalUrl) {            
            $canonicalUrl = $this->_trailingSlash($canonicalUrl);
        }
        
        // apply crossDomainUrl
        $crossDomainStore = false;
        if ($product->getCanonicalCrossDomain()) {
            $crossDomainStore = $product->getCanonicalCrossDomain();
        } elseif (Mage::getStoreConfig('mageworx_seo/seosuite/cross_domain')) {
            $crossDomainStore = Mage::getStoreConfig('mageworx_seo/seosuite/cross_domain');
        }                
        if ($crossDomainStore) {                
            $url = Mage::app()->getStore($crossDomainStore)->getBaseUrl();
            $canonicalUrl = str_replace(Mage::getUrl(), $url, $canonicalUrl);
        }        
        return $canonicalUrl;
    }

    public function formatUrlKey($str) {
        $str = str_ireplace('ã', 'a', $str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }
    
    public function getErrorTypes($arr=array()) {
        $errorTypes = array();
        if (empty($arr) || in_array('missing', $arr)) $errorTypes['missing'] = $this->__('Missing');
        if (empty($arr) || in_array('long', $arr)) $errorTypes['long'] = $this->__('Long');
        if (empty($arr) || in_array('duplicate', $arr)) $errorTypes['duplicate'] = $this->__('Duplicate');
        return $errorTypes;
    }
    
    public function _trimText($str) {
        if (!$str) return '';
        return trim(preg_replace("/\s+/is", ' ', $str));
    }
    
    public function _prepareText($str) {
        if (!$str) return '';
        $str = strtolower(preg_replace("/[^\w\d]+/is", ' ', $str));
        return $this->_trimText($str);
    }
    

}
