<?php
/*------------------------------------------------------------------------
 # SM Basic Products - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_BasicProducts_Block_Adminhtml_System_Config_Form_Field_Additem extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	public function __construct()
	{
		$this->addColumn('title', array(
			'label' => Mage::helper('adminhtml')->__('Title '),
			'style' => 'width:120px',
		));
		$this->addColumn('link', array(
			'label' => Mage::helper('adminhtml')->__('Link'),
			'style' => 'width:120px',
		));
		$this->addColumn('image', array(
			'label' => Mage::helper('adminhtml')->__('Media'),
			'style' => 'width:120px'
		));
		$this->addColumn('content', array(
			'label' => Mage::helper('adminhtml')->__('Content'),
			'style' => 'width:220px'

		));

		$this->_addAfter = false;
		$this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Item');
		parent::__construct();
	}

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		$html = $this->_toHtml();
		$this->_arrayRowsCache = null; // doh, the object is used as singleton!
		$html = '<div id="basicproducts_cfg_source_options_product_additem">' . $html . '</div>';
		return $html;
	}
}