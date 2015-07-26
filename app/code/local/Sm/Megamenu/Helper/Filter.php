<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
 
class Sm_Megamenu_Helper_Filter extends Mage_Core_Helper_Abstract {
	public function  getFilterData($data,$typeFilter){
		$new = '';
		if($typeFilter =='text'){
			$new = $this->_getDataText($data);
		}
		return $new;
	}
	protected function _getDataText($data){
		return strip_tags(trim($data)); 
	}
}
