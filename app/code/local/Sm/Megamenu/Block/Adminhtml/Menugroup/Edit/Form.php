<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Menugroup_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
									  'id' => 'edit_form',
									  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
									  'method' => 'post',
									  'enctype' => 'multipart/form-data'
								   )
		);

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}