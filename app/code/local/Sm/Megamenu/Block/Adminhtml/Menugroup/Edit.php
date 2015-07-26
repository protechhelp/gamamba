<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Menugroup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
				 
		$this->_objectId = 'id';
		$this->_blockGroup = 'megamenu';
		$this->_controller = 'adminhtml_menugroup';
		
		$this->_updateButton('save', 'label', Mage::helper('megamenu')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('megamenu')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'   => 'saveAndContinueEdit()',
			'class'     => 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('megamenu_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'megamenu_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'megamenu_content');
				}
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText()
	{
		if( Mage::registry('menugroup_data') && Mage::registry('menugroup_data')->getId() ) {
			return Mage::helper('megamenu')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('menugroup_data')->getTitle()));
		} else {
			return Mage::helper('megamenu')->__('Add Item');
		}
	}
	protected function _prepareLayout()
    {
        // Load Wysiwyg on demand and Prepare layout
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
            $block->setCanLoadTinyMce(true);
        }
        parent::_prepareLayout();
    } 	
}