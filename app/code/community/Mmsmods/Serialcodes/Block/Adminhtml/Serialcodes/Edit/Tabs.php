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
 
class Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('serialcodes_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('serialcodes')->__('Serial Codes'));
    }
 
    protected function _beforeToHtml()
    {
		if ( $this->getRequest()->getParam('id') <> 0  )
        {
			$this->addTab('form_section', array(
				'label'     => Mage::helper('serialcodes')->__('Edit Code'),
				'title'     => Mage::helper('serialcodes')->__('Edit Code'),
				'content'   => $this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_edit_tab_form')->toHtml(),
			));
        } else {
			$this->addTab('form_section', array(
				'label'     => Mage::helper('serialcodes')->__('Add Codes'),
				'title'     => Mage::helper('serialcodes')->__('Add Codes'),
				'content'   => $this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_edit_tab_form')->toHtml(),
			));
		}

        return parent::_beforeToHtml();
    }
}