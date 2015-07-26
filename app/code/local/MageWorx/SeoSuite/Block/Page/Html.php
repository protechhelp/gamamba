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
 * 
 */

class MageWorx_SeoSuite_Block_Page_Html extends Mage_Page_Block_Html
{
    protected function _toHtml() {
        $html = parent::_toHtml();
        if(Mage::registry('current_product') && Mage::getStoreConfig('mageworx_seo/seosuite/product_og_enabled')) {
            $product = Mage::registry('current_product');
	    $product = Mage::getModel('catalog/product')->load($product->getId());
            $doubleQuoteTitle = '"';
            $doubleQuoteDescr = '"';
            $descr = strip_tags($product->getShortDescription()?$product->getShortDescription():$product->getDescription());
            $title = strip_tags($product->getName());
            if(strpos($title,$doubleQuoteTitle)!==false) {
                $doubleQuoteTitle ="'";
            } elseif(strpos($descr,$doubleQuoteDescr)!==false) {
                $doubleQuoteDescr ="'";
            }
            $ogs =  "<meta property=\"og:title\" content=$doubleQuoteTitle".$title."$doubleQuoteTitle/>\n<meta property=\"og:description\" content=$doubleQuoteDescr".$descr."$doubleQuoteDescr/>\n<meta property=\"og:url\" content=\"".$product->getProductUrl()."\"/>\n";
            $ogs .= "<meta property=\"og:type\" content=\"product\"/>\n";
            $gallery = $product->getMediaGallery();
            if(isset($gallery['images'])) {
                foreach ($gallery['images'] as $_image) {
                    $ogs .="<meta property=\"og:image\" content=\"".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$_image['file']."\"/>\n";
                }
            }
            $pos = strpos($html, 'xmlns:fb="http://www.facebook.com/2008/fbml');
            if ($pos === false) {
                $html = str_replace("<html",'<html xmlns:fb="http://www.facebook.com/2008/fbml"',$html);
            }
            $pos = strpos($html, ' prefix="og: http://ogp.me/ns#"');
            if ($pos === false) {
                $html = str_replace("<html",'<html prefix="og: http://ogp.me/ns#"',$html);
            }
            $html = str_replace("<head>","<head>\n".$ogs,$html);
            
        }
        
        return $html;
    }
}
