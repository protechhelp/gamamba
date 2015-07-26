<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

/**PHP_COMMENT**/
class Sm_Megamenu_Helper_Default extends Mage_Core_Helper_Abstract {
	CONST EFFECT_DURATION = 800;
	public function __construct(){
		$this->defaults = array(
			/* General options */
			'isenabled'		=> '1',
			'title' 		=> 'MegaMenu',
			/* Module options */
			'module_width' 		=> '',
			'theme' 			=> '1',			//default = Horizontal	
			'group_id'			=> '1',
			'group_id_h'		=> '2',
			/* megamenu options */
			'effect'				=> '1',		//default = css
			'effect_duration'		=> '800',
			'start_level'			=> '1',
			'end_level'				=> '5',
			
			/* advanced options*/
			'include_jquery'	=> '1',
			'pretext'			=> '',
			'posttext'			=> ''
		);
	}

	function get($attributes=array())
	{
		$data 						= $this->defaults;
		$general 					= Mage::getStoreConfig("megamenu_cfg/general");
		$module_setting				= Mage::getStoreConfig("megamenu_cfg/module_setting");
		$advanced 					= Mage::getStoreConfig("megamenu_cfg/advanced");
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		
		if (is_array($general))					$data = array_merge($data, $general);
		if (is_array($module_setting)) 			$data = array_merge($data, $module_setting);
		if (is_array($advanced)) 				$data = array_merge($data, $advanced);
		
		return array_merge($data, $attributes);;
	}
}
?>