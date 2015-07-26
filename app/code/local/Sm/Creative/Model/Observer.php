<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Model_Observer {

	public function less_compile( $observer ){	
	
		$setting = Mage::helper('creative/data');
		$less_theme_compile     = Mage::getStoreConfig('creative_cfg/advanced/less_compile');
		$preset_name 			= Mage::getStoreConfig('creative_cfg/general/color');
		$device_responsive      = Mage::getStoreConfig('creative_cfg/general/device_responsive'); 
		
		if ( !Mage::app()->getStore()->isAdmin() && $less_theme_compile ){
			if (!class_exists('Less_Parser')) {			
				include_once(Mage::getBaseDir('lib').'Less/Version.php');
				include_once(Mage::getBaseDir('lib').'Less/Parser.php');
			}
			
			if ( class_exists('Less_Parser') && $less_theme_compile ){
				$skin_base_dir = Mage::getDesign()->getSkinBaseDir();
				$skin_base_url = Mage::getDesign()->getSkinUrl();

				define('LESS_PATH', $skin_base_dir.'/less');
				define('CSS__PATH', $skin_base_dir.'/css');						
				
				$import_dirs = array(
						LESS_PATH.'/path/' => $skin_base_url.'/less/path/',
						LESS_PATH.'/bootstrap/' => $skin_base_url.'/less/bootstrap/'
				);
				$options = array( 'compress'=>true );
				
				if ( file_exists(LESS_PATH.'/theme.less') && $less_theme_compile  ){
				
					if ( $preset_name ){
						$output_cssf = CSS__PATH.'/theme-'.$preset_name.'.css';
					} else {
						$output_cssf = CSS__PATH.'/theme-default.css';
					}
					
					$less = new Less_Parser($options);
					$less->SetImportDirs( $import_dirs );
					$less->parseFile(LESS_PATH.'/theme.less', $skin_base_url.'css/');
					
					if ( file_exists(LESS_PATH.'/theme-'.$preset_name.'.less') ){
						$less->parseFile(LESS_PATH.'/theme-'.$preset_name.'.less', $skin_base_url.'css/');
					}
				
					if( $device_responsive == 1 ){
						$less->parseFile(LESS_PATH.'/path/yt-responsive.less', $skin_base_url.'css/');
					} else {
						$less->parseFile(LESS_PATH.'/path/yt-non-responsive.less', $skin_base_url.'css/');
					}
					
					$cache = $less->getCss();
					file_put_contents($output_cssf, $cache);
					
				}
			
			}
		}
		
	}			
	
}