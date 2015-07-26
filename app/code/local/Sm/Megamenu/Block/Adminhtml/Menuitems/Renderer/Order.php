<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Menuitems_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		
		$value =  $row->getData('order');
		// Mage::helper('megamenu')->
		return $value;
		// echo $this->getColumn()->getIndex();die;
		// Zend_Debug::dump($row->getData());
		// $input ='<input type="text" value="" name="track['.$row->getData('redeemvoucher_id').'][]" class="input-text " style="width:80px !important;">'; 
		// $button = '<button style="" onclick="trackGridJsObject.doExport()" class="scalable task" type="button" id="id_c5acf9c45ff3cb6bf71ac838433294ab"><span>Update</span></button>';
		// return '<span style="color:red;">'.$value.'</span>'.$input;
	}

}