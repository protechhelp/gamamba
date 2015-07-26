<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Menugroup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('menugroup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('megamenu')->__('Group Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('megamenu')->__('Group Information'),
          'title'     => Mage::helper('megamenu')->__('Group Information'),
          'content'   => $this->getLayout()->createBlock('megamenu/adminhtml_menugroup_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}