<?php
/*------------------------------------------------------------------------
 # SM QuickView - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Quickview_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getInlucdeJQquery(){
		if(!(int)Mage::getStoreConfig('quickview/general/active')) return;
		if (!defined('MAGENTECH_JQUERY')  && (int)Mage::getStoreConfig('quickview/general/include_jquery')){
			define('MAGENTECH_JQUERY',1);
			$_jquery_libary = 'sm/quickview/js/jquery-1.8.2.min.js';
			return $_jquery_libary;
		}
	}
	
	public function getInlucdeNoconflict(){
		if(!(int)Mage::getStoreConfig('quickview/general/active')) return;
		if (!defined('MAGENTECH_JQUERY_NOCONFLICT')  && (int)Mage::getStoreConfig('quickview/general/include_jquery')){
			define('MAGENTECH_JQUERY_NOCONFLICT',1);
			$_jquery_noconflict = 'sm/quickview/js/jquery-noconflict.js';
			return $_jquery_noconflict;
		}
	}
	
	public function getInlucdeElevateZoom(){
		if(!(int)Mage::getStoreConfig('quickview/general/active')) return;
		$_package_name = Mage::getSingleton('core/design_package')->getPackageName();
		$_template_name = Mage::getSingleton('core/design_package')->getTheme('template');
		if($_package_name == 'rwd' && $_template_name == 'default'){
			return  'js/lib/elevatezoom/jquery.elevateZoom-3.0.8.min.js';
		}
	}
	
}