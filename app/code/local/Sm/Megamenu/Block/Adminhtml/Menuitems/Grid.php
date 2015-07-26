<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Menuitems_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('menuitemsGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{	
		$collection = Mage::getModel('megamenu/menuitems')->getCollection();
		$tbl_group = 	Mage::getModel('megamenu/menugroup')->getCollection()->getTable('menugroup');
		$collection ->getSelect()
					->join(array('mgroup' => $tbl_group),'mgroup.id = main_table.group_id', array('group_name'=>'mgroup.title'));
		// $group_id_default = Sm_Megamenu_Model_System_Config_Source_ListGroup::getOptionArray();
		// Zend_Debug::dump($group_id_default);die;
		// $i=0;
		// foreach($group_id_default as $item){
			// if($i==1){
				// $group_default = $item;
			// }
			// $i++;
		// }
		$collection ->getSelect()
				->join(array('parent' => $collection->getTable('menuitems') ),'',array())
				->columns('CONCAT( REPEAT( "'.Sm_Megamenu_Model_System_Config_Source_Prefix::PREFIX.'   ", (COUNT(parent.depth) - 1) ) , main_table.title) AS name')
				->where('main_table.lft BETWEEN parent.lft AND parent.rgt')
				// ->where('main_table.group_id ="'.$params['group'].'"')
				->where('parent.group_id = main_table.group_id')
				// ->where('mgroup.title = \''.$group_default.'\'')
				->group('main_table.id')
				->order('main_table.group_id')
				->order('main_table.lft');		
		// echo $collection->getSelect();die;
		// Zend_Debug::dump($collection->getData());die;
		// $pos = array();
		// $curr = -1;
		// $order = array();
		// // $prefix = "---  ";
		// $prefix='';
		// foreach($collection->getItems() as $item){
			// $lv = $item->getDepth();
			
			// if ($lv==0){
				// $item->setOrder('---');
				// $order[$item->getId()] = $prefix;
				// $curr = $lv;
				// $pos = array();
				
			// } else {
				// if ($lv < $curr){
					// $pos[($lv+1)] = 0;
				// }
				// if(!isset($pos[$lv])){
					// $pos[$lv] = 0;
				// }
				// $add_prefix = str_repeat($prefix, $lv);
				// $item->setOrder($add_prefix.++$pos[$lv]);
				// // $order[$item->getId()] = ++$pos[$lv];
				// $curr = $lv;
			// }
		// }
		// Zend_Debug::dump($order);die;
		// Zend_Debug::dump($this->getParam('filter'));die;
        // $filter = $this->getParam('filter');
    	// $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);		
		// Zend_Debug::dump($filter_data);die;
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	public function addGroupFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		 // echo $column->getFilter()->getValue();die;
		$collection->addFieldToFilter('mgroup.title', Array('eq'=>$column->getFilter()->getValue()));
		// echo $collection->getSelect();die;
	}
	public function addIdFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.id', Array('eq'=>$column->getFilter()->getValue()));
	}	
	public function addNameFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		// echo $column->getFilter()->getValue();die;
		$collection->addFieldToFilter('main_table.title', Array('like'=>$column->getFilter()->getValue().'%'));
		// echo $collection->getSelect();die;
	}	
	public function addDescriptionFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		// echo $column->getFilter()->getValue();die;
		$collection->addFieldToFilter('main_table.description', Array('like'=>$column->getFilter()->getValue().'%'));
		// echo $collection->getSelect();die;
	}	
	public function addDepthFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		// echo $column->getFilter()->getValue();die;
		$collection->addFieldToFilter('main_table.depth', Array('eq'=>$column->getFilter()->getValue()));
		// echo $collection->getSelect();die;
	}	
	public function addStatusFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		// echo $column->getFilter()->getValue();die;
		$collection->addFieldToFilter('main_table.status', Array('eq'=>$column->getFilter()->getValue()));
		// echo $collection->getSelect();die;
	}	
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'    => Mage::helper('megamenu')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'id',
			'filter_condition_callback' => array($this, 'addIdFilter'),
			'sortable'  => false,
		));

		$this->addColumn('name', array(
			'header'    => Mage::helper('megamenu')->__('MenuTitle'),
			'align'     =>'left',
			'index'     => 'name',
			'filter_condition_callback' => array($this, 'addNameFilter'),
			'sortable'  => false,
			'renderer'  => 'megamenu/adminhtml_menuitems_renderer_edit',
		));
		// $this->addColumn('order',
			// array(
				// 'header'    =>  Mage::helper('megamenu')->__('Order'),
				// 'width'     => '100',
				// 'type'      => 'action',
				// 'getter'    => 'getId',
				// // 'actions'   => array(
					// // array(
						// // 'caption'   => Mage::helper('megamenu')->__('Edit'),
						// // 'url'       => array('base'=> '*/*/edit'),
						// // 'field'     => 'id'
					// // )
				// // ),
				// 'filter'    => false,
				// 'sortable'  => false,
				// 'index'     => 'id',
				// 'is_system' => true,
				// 'renderer'  => 'megamenu/adminhtml_menuitems_renderer_order',
		// ));
		
		$this->addColumn('description', array(
			'header'    => Mage::helper('megamenu')->__('Description'),
			'width'     => '150px',
			'index'     => 'description',
			'filter_condition_callback' => array($this, 'addDescriptionFilter'),
			'sortable'  => false,
		));
		$this->addColumn('group_name', array(
			'header'    => Mage::helper('megamenu')->__('Menu Group'),
			'width'     => '150px',
			'index'     => 'group_name',
			'type'      => 'options',
			'options'   => Sm_Megamenu_Model_System_Config_Source_ListGroup::getOptionArray(),	
			'filter_condition_callback' => array($this, 'addGroupFilter'),
			'sortable'  => false,
		));		
		$this->addColumn('depth', array(
			'header'    => Mage::helper('megamenu')->__('Menu Level'),
			'width'     => '150px',
			'index'     => 'depth',
			'filter_condition_callback' => array($this, 'addDepthFilter'),
			'sortable'  => false,
		));	
		$this->addColumn('status', array(
			'header'    => Mage::helper('megamenu')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
			  1 => 'Enabled',
			  2 => 'Disabled',
			),
			'filter_condition_callback' => array($this, 'addStatusFilter'),
			'sortable'  => false,
		));

		// $this->addColumn('action',
			// array(
				// 'header'    =>  Mage::helper('megamenu')->__('Action'),
				// 'width'     => '100',
				// 'type'      => 'action',
				// 'getter'    => 'getId',
				// 'actions'   => array(
					// array(
						// 'caption'   => Mage::helper('megamenu')->__('Edit'),
						// 'url'       => array('base'=> '*/*/edit'),
						// 'field'     => 'id'
					// )
				// ),
				// 'filter'    => false,
				// 'sortable'  => false,
				// 'index'     => 'stores',
				// 'is_system' => true,
		// ));

		$this->addExportType('*/*/exportCsv', Mage::helper('megamenu')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('megamenu')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('menuitems_param');

		$this->getMassactionBlock()->addItem('delete', array(
			 'label'    => Mage::helper('megamenu')->__('Delete'),
			 'url'      => $this->getUrl('*/*/massDelete'),
			 'confirm'  => Mage::helper('megamenu')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('megamenu/system_config_source_status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			 'label'=> Mage::helper('megamenu')->__('Change status'),
			 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			 'additional' => array(
					'visibility' => array(
						 'name' => 'status',
						 'type' => 'select',
						 'class' => 'required-entry',
						 'label' => Mage::helper('megamenu')->__('Status'),
						 'values' => $statuses
					 )
			 )
		));
		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}