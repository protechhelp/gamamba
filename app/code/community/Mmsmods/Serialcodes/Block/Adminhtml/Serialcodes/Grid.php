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
 
class Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('serialcodesGrid');
        $this->setDefaultSort('serialcodes_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
		if (Mage::getSingleton('admin/session')->getScGridPool()) {
			$this->setDefaultFilter(array('sku' => Mage::getSingleton('admin/session')->getScGridPool()));
		}
	}

    protected function _addColumnFilterToCollection($column)
    {
		if ($column->getId() == 'sku' && $column->getFilter()->getValue()) {
			$this->getCollection()->addFieldToFilter($column->getId(), $column->getFilter()->getValue());
		} else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('serialcodes/serialcodes')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header'    => Mage::helper('serialcodes')->__('SKU (Code Pool)'),
            'align'     => 'left',
            'width'     => '120px',
            'index'     => 'sku'
        ));
        $this->addColumn('type', array(
            'header'    => Mage::helper('serialcodes')->__('Serial Code Type'),
            'align'     => 'left',
            'width'     => '150px',
            'index'     => 'type',
		));
        $this->addColumn('code', array(
            'header'    => Mage::helper('serialcodes')->__('Serial Code'),
            'align'     => 'left',
            'width'     => '200px',
            'index'     => 'code'
        ));
         $this->addColumn('status', array(
            'header'    => Mage::helper('serialcodes')->__('Status'),
            'align'     => 'left',
            'width'     => '85px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('serialcodes')->__('Available'),
                1 => Mage::helper('serialcodes')->__('Used'),
                2 => Mage::helper('serialcodes')->__('Pending')
            )
        ));
        $this->addColumn('note', array(
            'header'    => Mage::helper('serialcodes')->__('Note (Order)'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'note'
        ));
		$this->addColumn('created_time', array(
            'header'    => Mage::helper('serialcodes')->__('Created'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'created_time'
        ));
        $this->addColumn('update_time', array(
            'header'    => Mage::helper('serialcodes')->__('Updated'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'update_time'
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('serialcodes')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('serialcodes')->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
	{
		$this->setMassactionIdField('serialcodes_id');
		$this->getMassactionBlock()->setFormFieldName('serialcodes_id');
		$this->getMassactionBlock()->addItem('sku', array(
			'label'=> Mage::helper('serialcodes')->__('Change SKU (Code Pool)'),
			'url'  => $this->getUrl('*/*/massSku', array('_current'=>true)),
			'confirm' => Mage::helper('serialcodes')->__('Are you sure you want to change these records?'),
			'additional' => array(
				'product' => array(
					'name' => 'sku',
					'type' => 'text',
					'class' => 'required-entry',
					'label' => Mage::helper('serialcodes')->__('New SKU:')
				)
			)
		));
		$this->getMassactionBlock()->addItem('type', array(
			'label'=> Mage::helper('serialcodes')->__('Change Serial Code Type'),
			'url'  => $this->getUrl('*/*/massType', array('_current'=>true)),
			'confirm' => Mage::helper('serialcodes')->__('Are you sure you want to change these records?'),
			'additional' => array(
				'codetype' => array(
					'name' => 'type',
					'type' => 'text',
					'class' => 'optional',
					'label' => Mage::helper('serialcodes')->__('New Type:')
				)
			)
		));
		$statuses = array(
			0 => Mage::helper('serialcodes')->__('Available'),
			1 => Mage::helper('serialcodes')->__('Used'),
			2 => Mage::helper('serialcodes')->__('Pending')
			);
			$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('serialcodes')->__('Change Status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'confirm' => Mage::helper('serialcodes')->__('Are you sure you want to change these records?'),
			'additional' => array(
				'availability' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('serialcodes')->__('New Status:'),
					'values' => $statuses
				)
			)
		));
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('serialcodes')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
			'confirm' => Mage::helper('serialcodes')->__('Are you sure you want to delete these records?')
		));
		return $this;
	}

	public function getRowUrl($row)
    {
//		if ($default = $this->_defaultFilter) {
//			return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'codepool' => $default['sku']));
//		} else {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
//		}
    }
}