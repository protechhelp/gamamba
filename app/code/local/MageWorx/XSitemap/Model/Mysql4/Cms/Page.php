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
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @author     MageWorx Dev Team
 */
class MageWorx_XSitemap_Model_Mysql4_Cms_Page extends Mage_Core_Model_Mysql4_Abstract
{
    const XML_PATH_FILTER_PAGES = 'mageworx_seo/xsitemap/filter_pages';
    const XML_PATH_HOME_PAGE = 'web/default/cms_home_page';

    protected function _construct() {
        $this->_init('cms/page', 'page_id');
        $this->_homePage = Mage::getStoreConfig(self::XML_PATH_HOME_PAGE);
    }

    public function getCollection($storeId) {
        $pages = array();

        $filterPages = Mage::getStoreConfig(self::XML_PATH_FILTER_PAGES, $storeId);
        $filterPages = explode(',', $filterPages);
        
        $read  = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName(), 'identifier AS url'))
            ->join(
                array('store_table' => $this->getTable('cms/page_store')),
                'main_table.page_id=store_table.page_id',
                array()
            )
            ->where('main_table.identifier NOT IN(?)', $filterPages)
            ->where('main_table.exclude_from_sitemap=0')
            ->where('main_table.is_active=1')
            ->where('store_table.store_id IN(?)', array(0, $storeId));

        $query = $read->query($select);
        while ($row = $query->fetch()) {
            if ($row['url'] == $this->_homePage) {
                $row['url'] = '';
            }
            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    protected function _prepareObject(array $data) {
        $object = new Varien_Object();
        $object->setId($data[$this->getIdFieldName()]);
        $object->setUrl($data['url']);

        return $object;
    }

}