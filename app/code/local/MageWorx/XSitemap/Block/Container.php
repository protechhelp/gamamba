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
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_XSitemap_Block_Container extends Mage_Core_Block_Template
{
    const XML_PATH_SHOW_STORES = 'mageworx_seo/xsitemap/show_stores';
    const XML_PATH_SHOW_CATEGORIES = 'mageworx_seo/xsitemap/show_categories';
    const XML_PATH_SHOW_PAGES = 'mageworx_seo/xsitemap/show_pages';
    const XML_PATH_SHOW_LINKS = 'mageworx_seo/xsitemap/show_links';

    protected function _construct()
    {
        $this->setTitle($this->__('Site Map'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->getTitle());
    }

    public function showStores()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_STORES);
    }

    public function showCategories()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_CATEGORIES);
    }

    public function showPages()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PAGES);
    }

    public function showLinks()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_LINKS);
    }
}
