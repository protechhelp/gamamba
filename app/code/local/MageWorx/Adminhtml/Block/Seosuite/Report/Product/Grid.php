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

class MageWorx_Adminhtml_Block_Seosuite_Report_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('entity_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        
        if (!Mage::registry('error_types')) Mage::register('error_types', Mage::helper('seosuite')->getErrorTypes());
    }

    
    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 1);
		if(!$storeId) {
			$stores = Mage::app()->getStores();
			$store = array_shift($stores);
			$storeId = $store->getStoreId();
	//		echo $storeId; exit;
		}
	return $storeId;
    }    

    protected function _prepareCollection() {
        $store = $this->_getStore();        
	
        $collection = Mage::getResourceModel('seosuite/report_product_collection');
		if ($store) {
				$collection->addFieldToFilter('store_id', $store);
		}
        $this->setCollection($collection);
        
	    return parent::_prepareCollection();
    }
	
	protected function _filterStoreCondition($collection, $column)
	    {
	        if (!$value = $column->getFilter()->getValue()) {
	            return;
	        }
	        $this->getCollection()->addFieldToFilter('store_id',$value);
	    }
	
    protected function _prepareColumns() {                

        $this->addColumn('entity_id', array(
            'header'=> Mage::helper('seosuite')->__('ID'),
            'width' => '50px',
            'type'  => 'number',
            'index' => 'entity_id',
            'align' => 'center',
        ));
//         $this->addColumn('entity_id', array(
//               'header'=> Mage::helper('seosuite')->__('Entity ID'),
//               'width' => '50px',
//               'type'  => 'hidden',
//               'index' => 'entity_id',
//               'align' => 'center',
//           ));
           
           $this->addColumn('name', array(
               'header'=> Mage::helper('seosuite')->__('Product Name'),
               //'width' => '300px',
               'type'  => 'text',
               'index' => 'name',
               'align' => 'left',
           ));
           
           $this->addColumn('sku', array(
               'header'=> Mage::helper('seosuite')->__('SKU'),
               //'width' => '100px',
               'type'  => 'text',
               'index' => 'sku'
           ));
                  
           
           $this->addColumn('url', array(
                                 'header'=> Mage::helper('seosuite')->__('Url'),
                                 'renderer'  => 'mageworx/seosuite_report_grid_renderer_url',
                                 //'width' => '300px',
                                 'type'  => 'text',
                                 'index' => 'url_path',
                                 'align' => 'left',
                             ));
           
           $this->addColumn('type',
               array(
                   'header'=> Mage::helper('seosuite')->__('Type'),
                   'width' => '125px',
                   'index' => 'type_id',
                   'type'  => 'options',
                   'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
                   'align' => 'center'
           ));
           
           $this->addColumn('name_error', array(
               'renderer'  => 'mageworx/seosuite_report_grid_renderer_error',
               'filter'    => 'mageworx/seosuite_report_grid_filter_error',
               'type'  => 'options',
               'options' => Mage::helper('seosuite')->getErrorTypes(array('duplicate')),
               'header' => Mage::helper('seosuite')->__('Name'),
               'index' => 'name_error',
               'width' => '100px',
               'align' => 'center',
               'sortable'  => false,
           ));
           
           $this->addColumn('meta_title_error', array(
               'renderer'  => 'mageworx/seosuite_report_grid_renderer_error',
               'filter'    => 'mageworx/seosuite_report_grid_filter_error',
               'type'  => 'options',
               'options' => Mage::helper('seosuite')->getErrorTypes(),
               'header' => Mage::helper('seosuite')->__('Meta Title'),
               'index' => 'meta_title_error',
               'width' => '100px',
               'align' => 'center',
               'sortable'  => false,
           ));
           
           $this->addColumn('meta_descr_error', array(
               'renderer'  => 'mageworx/seosuite_report_grid_renderer_error',
               'filter'    => 'mageworx/seosuite_report_grid_filter_error',
               'type'  => 'options',
               'options' => Mage::helper('seosuite')->getErrorTypes(array('missing', 'long')),
               'header' => Mage::helper('seosuite')->__('Meta Description'),
               'index' => 'meta_descr_error',
               'width' => '100px',
               'align' => 'center',
               'sortable'  => false,
           ));        
           // $this->addColumn('store', array(
           //        			            'header'        => Mage::helper('seosuite')->__('Store View'),
           //        			            'index'         => 'store_id',
           //        			            'type'          => 'store',
           //        			            'store_all'     => false,
           //        			            'store_view'    => true,
           //        			            'sortable'      => false,
           // 								'default'		=> 1,
           // 								'filter_condition_callback'
           //        			                            => array($this, '_filterStoreCondition'),
           //        			        ));
         		
           $this->addColumn('action',
               array(
                   'header'    => Mage::helper('seosuite')->__('Action'),
                   'width'     => '50px',
                   'type'      => 'action',
                   'getter'     => 'getProductId',
                   'actions'   => array(
                       array(
                           'caption' => Mage::helper('seosuite')->__('Edit'),
                           'url'     => array('base'=>'adminhtml/catalog_product/edit/', 'params'=>array('store'=>$this->getRequest()->getParam('store'))),
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


    public function getRowUrl($row)
    {        
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId(),'store'=>$this->getRequest()->getParam('store')));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
