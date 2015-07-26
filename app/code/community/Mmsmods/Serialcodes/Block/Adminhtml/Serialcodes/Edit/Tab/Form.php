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
 
class Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
		if ( $this->getRequest()->getParam('id') <> 0  )
        {
			$fieldset = $form->addFieldset('serialcodes_form', array('legend'=>Mage::helper('serialcodes')->__('Edit Code')));
        } else {
			$fieldset = $form->addFieldset('serialcodes_form', array('legend'=>Mage::helper('serialcodes')->__('Add Codes')));
		}
        $fieldset->addField('sku', 'text', array(
            'label'     => Mage::helper('serialcodes')->__('SKU (or Code Pool)'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'sku'
        ));
        $fieldset->addField('type', 'text', array(
            'label'     => Mage::helper('serialcodes')->__('Serial Code Type'),
            'class'     => 'optional',
            'required'  => false,
            'name'      => 'type',
			'note'		=> Mage::helper('serialcodes')->__('Reference only. Set Serial Code Type at product level.')
        ));
		if ( $this->getRequest()->getParam('id') <> 0  )
        {
			$fieldset->addField('code', 'text', array(
				'label'     => Mage::helper('serialcodes')->__('Serial Code'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'code'
			));
			$fieldset->addField('status', 'select', array(
				'label'     => Mage::helper('serialcodes')->__('Status'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'status',
				'values'    => array(
					array(
						'value'     => 0,
						'label'     => Mage::helper('serialcodes')->__('Available')
					),
					array(
						'value'     => 1,
						'label'     => Mage::helper('serialcodes')->__('Used')
					),
					array(
						'value'     => 2,
						'label'     => Mage::helper('serialcodes')->__('Pending')
					)
				)
			));
			$fieldset->addField('note', 'text', array(
				'label'     => Mage::helper('serialcodes')->__('Note (Order)'),
				'class'     => 'optional',
				'required'  => false,
				'name'      => 'note'
			));
			} else {
			$fieldset->addField('code', 'editor', array(
				'label'     => Mage::helper('serialcodes')->__('Serial Codes (one per line)'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'code',
				'style'     => 'width:98%; height:300px;',
				'wysiwyg'   => false
			));
			$fieldset->addField('status', 'hidden', array(
				'name'      => 'status',
				'value'     => 0
		    ));
			$fieldset->addField('note', 'hidden', array(
				'name'      => 'note',
				'value'     => ''
		    ));
		}
        if ( Mage::getSingleton('adminhtml/session')->getSerialcodesData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSerialcodesData());
            Mage::getSingleton('adminhtml/session')->setSerialcodesData(null);
        } elseif ( Mage::registry('serialcodes_data') ) {
            $form->setValues(Mage::registry('serialcodes_data')->getData());
        }
        return parent::_prepareForm();
    }
}