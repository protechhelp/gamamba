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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2010 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_SeoSuite_Block_Review_Product_View_List extends Mage_Review_Block_Product_View_List
{
    public function getReviewUrl($id)
    {
        if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/reviews_friendly_urls')){
            $review = Mage::getModel('review/review')->load($id);
            $formattedTitle = $this->getProduct()->formatUrlKey($review->getTitle());
            return Mage::getUrl() . implode('/', array($this->getProduct()->getUrlKey(), 'reviews', $formattedTitle, $id));
        } else {
            return parent::getReviewUrl($id);
        }
    }
}