<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Helper_Utils extends Mage_Core_Helper_Abstract {
	public function getTargetAttr($type=''){
		$attribs = '';
		switch($type){
			default:
			case '0':
			case '':
				break;
			case '1':
			case '_blank':
				$attribs = "target=\"_blank\"";
				break;
			case '2':
			case '_popup':
				$attribs = "onclick=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,false');return false;\"";
				break;
		}
		return $attribs;
	}
}
?>