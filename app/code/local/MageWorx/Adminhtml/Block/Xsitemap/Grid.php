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
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Adminhtml_Block_Xsitemap_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sitemapGrid');
        $this->setDefaultSort('sitemap_id');

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xsitemap/sitemap')->getCollection();
        /* @var $collection MageWorx_XSitemap_Model_Mysql4_Sitemap_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('sitemap_id', array(
            'header'    => Mage::helper('sitemap')->__('ID'),
            'width'     => '50px',
            'index'     => 'sitemap_id'
        ));

        $this->addColumn('sitemap_filename', array(
            'header'    => Mage::helper('sitemap')->__('Filename'),
            'index'     => 'sitemap_filename'
        ));

        $this->addColumn('sitemap_path', array(
            'header'    => Mage::helper('sitemap')->__('Path'),
            'index'     => 'sitemap_path'
        ));

        $this->addColumn('link', array(
            'header'    => Mage::helper('sitemap')->__('Link for Google'),
            'index'     => 'concat(sitemap_path, sitemap_filename)',
            'renderer'  => 'mageworx/xsitemap_grid_renderer_link',
        ));

        $this->addColumn('sitemap_time', array(
            'header'    => Mage::helper('sitemap')->__('Last Time Generated'),
            'width'     => '150px',
            'index'     => 'sitemap_time',
            'type'      => 'datetime',
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sitemap')->__('Store View'),
                'index'     => 'store_id',
                'type'      => 'store',
            ));
        }

        $this->addColumn('action', array(
            'header'   => Mage::helper('sitemap')->__('Action'),
            'filter'   => false,
            'sortable' => false,
            'width'    => '100',
            'renderer' => 'mageworx/xsitemap_grid_renderer_action'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('sitemap_id' => $row->getId()));
    }
}
