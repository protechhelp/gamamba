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

class Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
		$fieldset = $form->addFieldset('serialcodes_items_form', array('legend'=>Mage::helper('serialcodes')->__('Edit Serial Codes')));
       
        $fieldset->addField('order', 'text', array(
            'label'     => Mage::helper('serialcodes')->__('Order Number'),
            'class'     => 'disabled',
            'required'  => false,
			'disabled'	=> true,
			'readonly'	=> true,
            'name'      => 'order',
        ));

        $fieldset->addField('customer', 'text', array(
            'label'     => Mage::helper('serialcodes')->__('Customer Name'),
            'class'     => 'disabled',
            'required'  => false,
			'disabled'	=> true,
			'readonly'	=> true,
            'name'      => 'customer'
        ));

		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('serialcodes')->__('Product'),
			'class'     => 'disabled',
			'required'  => false,
			'disabled'	=> true,
			'readonly'	=> true,
			'name'      => 'name'
		));

		$fieldset->addField('qty', 'text', array(
			'label'     => Mage::helper('serialcodes')->__('Quantity Ordered'),
			'class'     => 'disabled',
			'required'  => false,
			'disabled'	=> true,
			'readonly'	=> true,
			'name'      => 'qty'
		));

		$fieldset->addField('serial_code_type', 'text', array(
			'label'     => Mage::helper('serialcodes')->__('Serial Code Type'),
			'class'     => 'optional',
			'required'  => false,
			'name'      => 'serial_code_type'
		));
		$fieldset->addField('serial_codes', 'editor', array(
			'label'     => Mage::helper('serialcodes')->__('Serial Codes (one per line)'),
			'class'     => 'optional',
			'required'  => false,
			'name'      => 'serial_codes'
		));
		$fieldset->addField('serial_codes_issued', 'text', array(
			'label'     => Mage::helper('serialcodes')->__('Number Serial Codes Issued'),
			'class'     => 'optional',
			'required'  => false,
			'name'      => 'serial_codes_issued'
		));

        if ( Mage::getSingleton('adminhtml/session')->getSerialcodesItemsData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSerialcodesItemsData());
            Mage::getSingleton('adminhtml/session')->setSerialcodesItemsData(null);
        } elseif ( Mage::registry('serialcodes_items_data') ) {
            $form->setValues(Mage::registry('serialcodes_items_data')->getData());
			$form->getElement('order')->setValue($this->getRequest()->getParam('order'));
			$form->getElement('customer')->setValue(str_replace('%20',' ',$this->getRequest()->getParam('customer')));
			$form->getElement('qty')->setValue($this->getRequest()->getParam('qty'));
        }
        return parent::_prepareForm();
    }
}