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

class MageWorx_Adminhtml_Block_Seosuite_Report_Category_Duplicate_View_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('entity_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASK');
        //$this->setSaveParametersInSession(true);
    }

    
    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', Mage::app()->getDefaultStoreView()->getStoreId());
        return Mage::app()->getStore($storeId);
    }    

    protected function _prepareCollection() {
        $store = $this->_getStore();
        $collection = Mage::getResourceModel('seosuite/report_category_collection')->addFieldToFilter('store_id', $store->getId());
        
        $preparedName = $this->getRequest()->getParam('prepared_name', '');
        if ($preparedName) $collection->addFieldToFilter('prepared_name', $preparedName);
        
        $preparedMetaTitle = $this->getRequest()->getParam('prepared_meta_title', '');
        if ($preparedMetaTitle) $collection->addFieldToFilter('prepared_meta_title', $preparedMetaTitle);        
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {                

        $this->addColumn('entity_id', array(
            'header'=> Mage::helper('seosuite')->__('ID'),
            'width' => '50px',
            'type'  => 'number',
            'index' => 'entity_id',
            'align' => 'center',
        ));
        
        
        $this->addColumn('name', array(
            'header'=> Mage::helper('seosuite')->__('Category Name'),
            'type'  => 'text',
            'index' => 'name',
            'align' => 'left',
        ));
        
        
        $this->addColumn('meta_title', array(
            'header'=> Mage::helper('seosuite')->__('Meta Title'),
            'type'  => 'text',
            'index' => 'meta_title',
            'align' => 'left',
        ));                
               
        
        $this->addColumn('url', array(
            'header'=> Mage::helper('seosuite')->__('Url'),
            'renderer'  => 'mageworx/seosuite_report_grid_renderer_url',
            'type'  => 'text',
            'index' => 'url_path',
            'align' => 'left',
        ));
        
        
        $this->addColumn('level', array(
            'header'=> Mage::helper('seosuite')->__('Level'),
            'width' => '50px',
            'type'  => 'number',
            'index' => 'level',
            'align' => 'center',
        ));
        
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('seosuite')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getCategoryId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('seosuite')->__('Edit'),
                        'url'     => array('base'=>'adminhtml/catalog_category/edit/', 'params'=>array('store'=>$this->getRequest()->getParam('store'))),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'align' => 'center',
                'is_system' => true,
        ));
        

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        return $this;
    }

    public function getRowUrl($row) {        
        return $this->getUrl('adminhtml/catalog_category/edit', array('id' => $row->getCategoryId(),'store'=>$this->getRequest()->getParam('store')));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/duplicateViewGrid', array('_current'=>true));
    }

}
