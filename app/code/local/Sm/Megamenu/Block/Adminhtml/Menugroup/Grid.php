<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Menugroup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('menugroupGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('megamenu/menugroup')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'    => Mage::helper('megamenu')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'id',
		));

		$this->addColumn('title', array(
			'header'    => Mage::helper('megamenu')->__('Title'),
			'align'     =>'left',
			'index'     => 'title',
		));

		/*
		$this->addColumn('content', array(
			'header'    => Mage::helper('megamenu')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
		));
		*/

		$this->addColumn('status', array(
			'header'    => Mage::helper('megamenu')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   =>  Mage::getSingleton('megamenu/system_config_source_status')->getOptionArray(),
		));

		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('megamenu')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('megamenu')->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('megamenu')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('megamenu')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('menugroup_param');

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