<?php
/*
* ModifyMage Solutions (http://ModifyMage.com)
* Serial Codes - Serial Numbers, Product Codes, PINs, and More
*
* NOTICE OF LICENSE
* This source code is owned by ModifyMage Solutions and distributed for use under the
* provisions, terms, and conditions of our Commercial Software License Agreement which
* is bundled with this package in the file LICENSE.txt. This license is also available
* through the world-wide-web at this URL: http://www.modifymage.com/software-license
* If you do not have a copy of this license and are unable to obtain it through the
* world-wide-web, please email us at license@modifymage.com so we may send you a copy.
*
* @category		Mmsmods
* @package		Mmsmods_Serialcodes
* @author		David Upson
* @copyright	Copyright 2013 by ModifyMage Solutions
* @license		http://www.modifymage.com/software-license
*/
 
class Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('serialcode_itemsGrid');
		$this->setDefaultSort('created_at');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(false);
    }
 
    protected function _addColumnFilterToCollection($column)
    {
		if ($column->getId() == 'serial_codes' && $column->getFilter()->getValue() == '*') {
			$this->getCollection()
				->addFieldToFilter($column->getId(), array('notnull' => true))
				->addFieldToFilter($column->getId(), array('neq' => ''));
		} else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareCollection()
	{
		$ordertable = Mage::getSingleton('core/resource')->getTableName('sales/order');
		$collection = Mage::getModel('sales/order_item')->getCollection();
		$bundles = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('product_type', array('eq' => 'bundle'));
		$ids = implode(',', $bundles->getColumnValues('item_id'));
		if(!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && version_compare(Mage::getVersion(),'1.4.1.1') < 0)
		{
			if ($ids) {
				$collection->getSelect()
					->join($ordertable,"main_table.order_id=$ordertable.entity_id", array('increment_id','store_id','status'))
					->where("main_table.parent_item_id IS NULL OR main_table.parent_item_id IN ($ids)");
			} else {
				$collection->getSelect()
					->join($ordertable,"main_table.order_id=$ordertable.entity_id", array('increment_id','store_id','status'))
					->where("main_table.parent_item_id IS NULL");
			}
		} else {
			if ($ids) {
				$collection->getSelect()
					->join($ordertable,"main_table.order_id=$ordertable.entity_id", array('increment_id','store_id','status','lastname' => 'customer_lastname'))
					->where("main_table.parent_item_id IS NULL OR main_table.parent_item_id IN ($ids)");
			} else {
				$collection->getSelect()
					->join($ordertable,"main_table.order_id=$ordertable.entity_id", array('increment_id','store_id','status','lastname' => 'customer_lastname'))
					->where("main_table.parent_item_id IS NULL");
			}
		}
		$this->addCustomerName($collection, $ordertable);
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
 
    protected function _prepareColumns()
    {		
		$ordertable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        if (!Mage::app()->isSingleStoreMode()) {
			if ($this->_isExport) {
					$this->addColumn('store_id', array(
					'header'    		=> Mage::helper('serialcodes')->__('Store Id'),
					'align'     		=>'left',
					'width'     		=> '160px',
					'index'     		=> 'store_id',
					'store_view'		=> false,
					'display_deleted'	=> true
				));
			} else {
				$this->addColumn('store_id', array(
					'header'    		=> Mage::helper('serialcodes')->__('Store'),
					'align'     		=>'left',
					'width'     		=> '160px',
					'index'     		=> 'store_id',
					'filter_index'		=> $ordertable.'.store_id',
					'type'      		=> 'store',
					'store_view'		=> true,
					'display_deleted'	=> true
				));
			}
        }

		if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			$this->addColumn('increment_id', array(
				'header'    => Mage::helper('serialcodes')->__('Order'),
				'align'     =>'left',
				'width'     => '80px',
				'index'     => 'increment_id',
				'renderer'	=> 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Order'
			));
		} else {
			$this->addColumn('increment_id', array(
				'header'    => Mage::helper('serialcodes')->__('Order'),
				'align'     =>'left',
				'width'     => '80px',
				'index'     => 'increment_id'
			));
		}

        $this->addColumn('status', array(
            'header'    => Mage::helper('serialcodes')->__('Order Status'),
            'align'     =>'left',
            'width'     => '30px',
            'type'		=> 'options',
            'index'     => 'status',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses()
        ));

        $this->addColumn('qty_ordered', array(
            'header'    => Mage::helper('serialcodes')->__('Qty'),
            'align'     =>'left',
            'width'     => '30px',
            'index'     => 'qty_ordered'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('catalog/products')) {
			$this->addColumn('name', array(
				'header'    => Mage::helper('serialcodes')->__('Product'),
				'align'     =>'left',
				'width'     => '180px',
				'index'     => 'name',
				'filter_index' => 'main_table.name',
				'renderer'	=> 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Product'
			));
		} else {
			$this->addColumn('name', array(
				'header'    => Mage::helper('serialcodes')->__('Product'),
				'align'     =>'left',
				'width'     => '180px',
				'index'     => 'name',
				'filter_index' => 'main_table.name'
			));
		}
		
        if (Mage::getSingleton('admin/session')->isAllowed('customer/manage')) {
			$this->addColumn('customer_name', array(
				'header'       => Mage::helper('serialcodes')->__('Customer'),
				'align'        =>'left',
				'width'        => '120px',
				'index'        => 'fullname',
				'filter_index' => 'lastname',
				'format'       => '$fullname',
				'filter_condition_callback' => array($this, '_customerNameCondition'),
				'renderer'	   => 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Customer'
			));
		} else {
			$this->addColumn('customer_name', array(
				'header'       => Mage::helper('serialcodes')->__('Customer'),
				'align'        =>'left',
				'width'        => '120px',
				'index'        => 'fullname',
				'filter_index' => 'lastname',
				'format'       => '$fullname',
				'filter_condition_callback' => array($this, '_customerNameCondition')
			));
		}
		
        if (Mage::getSingleton('admin/session')->isAllowed('catalog/serialcodes')) {
			$this->addColumn('serial_code_pool', array(
				'header'    => Mage::helper('serialcodes')->__('Serial Code Pool'),
				'align'     => 'left',
				'width'     => '160px',
				'index'     => 'serial_code_pool',
				'renderer'	   => 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Codepool'
			));
		} else {
			$this->addColumn('serial_code_pool', array(
				'header'    => Mage::helper('serialcodes')->__('Serial Code Pool'),
				'align'     => 'left',
				'width'     => '160px',
				'index'     => 'serial_code_pool'
			));
		}

		$this->addColumn('serial_code_type', array(
            'header'    => Mage::helper('serialcodes')->__('Serial Code Type'),
            'align'     => 'left',
            'width'     => '160px',
            'index'     => 'serial_code_type'
        ));

        $this->addColumn('serial_codes', array(
            'header'    => Mage::helper('serialcodes')->__('Serial Codes'),
            'align'     =>'left',
            'width'     => '160px',
            'index'     => 'serial_codes',
			'renderer'	=> 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Codes'
        ));

		$this->addColumn('created_at', array(
            'header'       => Mage::helper('serialcodes')->__('Created'),
            'align'        => 'left',
            'width'        => '120px',
            'type'         => 'datetime',
            'index'        => 'created_at',
			'filter_index' => 'main_table.created_at'
        ));

        $this->addColumn('updated_at', array(
            'header'       => Mage::helper('serialcodes')->__('Updated'),
            'align'        => 'left',
            'width'        => '120px',
            'type'         => 'datetime',
            'index'        => 'updated_at',
			'filter_index' => 'main_table.updated_at'
        ));   
        $this->addExportType('*/*/exportCsv', Mage::helper('serialcodes')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('serialcodes')->__('XML'));
        return parent::_prepareColumns();
    }

	public function getRowUrl($row)
    {
		if(Mage::getSingleton('admin/session')->isAllowed('sales/serialcodes_items/serialcodes_items_edit'))
		{
			return $this->getUrl('*/*/edit', array(
				'id' 		=> $row->getId(),
				'order'		=> $row->getIncrementId(),
				'qty'		=> $row->getQtyOrdered(),
				'customer'	=> $row->getFullname()
			));
		}
    }
	
	private function addCustomerName($collection, $ordertable)
	{
		if(!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && version_compare(Mage::getVersion(),'1.4.1.1') < 0)
		{
			$itemtable = Mage::getSingleton('core/resource')->getTableName('sales/quote_item');
			$addresstable = Mage::getSingleton('core/resource')->getTableName('sales/quote_address');
			$collection	->getSelect()
						->join($itemtable,"main_table.quote_item_id=$itemtable.item_id",array());
			$collection	->getSelect()
						->join($addresstable,"$itemtable.quote_id = $addresstable.quote_id",array('firstname', 'lastname'))
						->where($addresstable.'.address_type = ?', 'billing');
			$collection	->getSelect()
						->columns(new Zend_Db_Expr("CONCAT(`$addresstable`.`firstname`, ' ',`$addresstable`.`lastname`) AS fullname"));
		} else {
			$collection	->getSelect()
						->columns(new Zend_Db_Expr("CONCAT(`$ordertable`.`customer_firstname`, ' ',`$ordertable`.`customer_lastname`) AS fullname"));
		}
	}

    protected function _customerNameCondition($collection, $column)
	{
        if (!$value = trim($column->getFilter()->getValue())) {
            return;
        }
		$condition = $column->getFilter()->getCondition();
		$filter = $condition['like'];
		$filters = explode(' ',$filter);
		$filters = array_map('trim', $filters);
		$filters = array_filter($filters);
		$filters = array_values($filters);
		if(!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && version_compare(Mage::getVersion(),'1.4.1.1') < 0) {
			$table = Mage::getSingleton('core/resource')->getTableName('sales/quote_address');
			if(count($filters) == 2) {
				$collection->getSelect()->where("$table.firstname LIKE '$filters[0]%' AND $table.lastname LIKE '%$filters[1]'");
			} else {
				$collection->getSelect()->where("$table.firstname LIKE '$filter' OR $table.lastname LIKE '$filter'");
			}
		} else {
			$table = Mage::getSingleton('core/resource')->getTableName('sales/order');
			if(count($filters) == 2) {
				$collection->getSelect()->where("$table.customer_firstname LIKE $filters[0]%' AND $table.customer_lastname LIKE '%$filters[1]");
			} else {
				$collection->getSelect()->where("$table.customer_firstname LIKE $filter OR $table.customer_lastname LIKE $filter");
			}
		}
		return $this;
    }
}