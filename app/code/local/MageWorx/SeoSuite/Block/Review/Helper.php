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
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */
/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_SeoSuite_Block_Review_Helper extends MageWorx_SeoSuite_Block_Review_Helper_Abstract
{
    public function getReviewsUrl()
    {
        if (Mage::getStoreConfig('mageworx_seo/seosuite/reviews_friendly_urls')) {
            $path = array(
                $this->getProduct()->getUrlKey(),
                'reviews'
            );
            if ($this->getProduct()->getCategoryId()) {
                $path[] = 'category';
                $path[] = $this->getProduct()->getCategory()->getUrlKey();
            }
            return Mage::getUrl() . implode('/', $path);
        }
        else {
            return parent::getReviewsUrl();
        }
    }

    /*
     * @todo Product page may be contain a several review blocks. All be modify now...
     */
    public function _toHtml()
    {
        $html = parent::_toHtml();
        if (Mage::helper('seosuite/richsnippet')->isRichsnippetEnabled()) {
            if(Mage::helper('seosuite/richsnippet')->isProductPage()){
//                $model = Mage::getModel('seosuite/catalog_product_richsnippet_review');
                $model = Mage::getModel('seosuite/richsnippet_catalog_product_review');
                $updateHtml = $model->render($html, $this, true);
                if ($updateHtml) {
                    return $updateHtml;
                }
            }
        }
        return $html;
    }
}