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

class MageWorx_XSitemap_Block_Pages extends Mage_Core_Block_Template
{
    const XML_PATH_FILTER_PAGES = 'mageworx_seo/xsitemap/filter_pages';
    const XML_PATH_HOME_PAGE = 'web/default/cms_home_page';

    protected $_homePage;

    protected function _construct()
    {
        $this->_homePage = Mage::getStoreConfig(self::XML_PATH_HOME_PAGE);
    }

    protected function _prepareLayout()
    {
        $filterPages = Mage::getStoreConfig(self::XML_PATH_FILTER_PAGES);
        $filterPages = explode(',', $filterPages);
        $collection = Mage::getModel('cms/page')->getCollection();
        $collection->addStoreFilter(Mage::app()->getStore()->getId());
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        $collection->addFieldToFilter('identifier', array('nin' => $filterPages));
        $this->setCollection($collection);
        return $this;
    }

    public function getItemUrl($page)
    {
        if ($this->_homePage == $page->getIdentifier()){
            return Mage::getUrl(null);
        }
        $version = Mage::getVersion();
        if (version_compare($version, '1.2', '<')){
            return Mage::getUrl($page->getIdentifier());
        }
        return Mage::getUrl(null, array('_direct' => $page->getIdentifier()));
    }

}
