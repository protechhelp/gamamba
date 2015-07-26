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
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_SeoSuite_Block_Page_Html_Pager extends MageWorx_SeoSuite_Block_Page_Html_Pager_Abstract
{
    public function getPagerUrl($params=array()) {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;

        $request = Mage::app()->getRequest();
        if (strpos($request->getOriginalPathInfo(), 'reviews') !== false) {
            $url = $this->getUrl('*/*/*', $urlParams);
            return str_replace('/reviews', $request->getOriginalPathInfo(), $url);
        }
        
        
        //$url = $this->getUrl('*/*/*', $urlParams);
        $url = Mage::helper('seosuite')->getLayerFilterUrl($urlParams);
        
        // modify pager url
        $pagerUrlFormat = Mage::helper('seosuite')->getPagerUrlFormat();
        if (isset($params['p']) && Mage::app()->getRequest()->getControllerName()=='category' && $pagerUrlFormat) {
            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            $pageNum = $params['p'];
            $url = str_replace(array('&amp;p='.$pageNum, '&p='.$pageNum, '?p='.$pageNum), '', $url);            
            if ($pageNum>1) {
                $urlArr = explode('?', $url);
                $urlArr[0] = ($suffix && substr($urlArr[0], -(strlen($suffix)))==$suffix?substr($urlArr[0], 0, -(strlen($suffix))):$urlArr[0]);
                $urlArr[0] .= str_replace('[page_number]', $pageNum, $pagerUrlFormat);
                $urlArr[0] .= $suffix;
                $url = implode('?', $urlArr);
            }
        }
        
        if ($this->getAction()->getFullActionName() === 'umicrosite_index_index') {
            $mUrl = '';
            $catid = '';
            if (isset($_GET['catid'])) {
                $catid = 'catid=' . $_GET['catid'] . '&';
            }
            $_vendor = Mage::helper('umicrosite')->getCurrentVendor();
            if (Mage::registry('vndMarketUrl')) {
                $mUrl = 'VND-' . $_vendor->getId() . '/?' . $catid;
            } else {
                $mUrl = '?' . $catid;
            }
            return Mage::helper('umicrosite')->getSchemaUrl() . $_SERVER['HTTP_HOST'] . '/' . $mUrl . 'p=' . $params['p'];
        }
        
        return $url;
    }       
}