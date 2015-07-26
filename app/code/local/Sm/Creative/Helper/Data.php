<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Helper_Data extends Mage_Core_Helper_Abstract{

	public function __construct(){
		$this->defaults = array(
			/* general options */
			'layout_styles'				 => '1',
			'color'						 => 'tomato',
			'body_font_family'			 => 'Arial',
			'body_font_size'			 => '13px',
			'google_font'				 => 'Open Sans',
			'google_font_targets'		 => '',
			'body_link_color'			 => '#666666',
			'body_link_hover_color'		 => '#666666',
			'body_text_color'			 => '#666666',
			'body_title_color'			 => '#444444',
			'title_color_targets'		 => '',
			'body_background_color'		 => '#ffffff',			
			'body_background_image'		 => '',
			'use_customize_image'		 => '',
			'background_customize_image' => '',
			'background_repeat'		     => '',			
			'background_position'		 => '',
			'device_responsive'			 => '1',
			'menu_ontop'		         => '1',			
			'responsive_menu'		     => '3',			
			/* detail creative */
			'show_imagezoom'		     => '',
			'zoom_mode'		 			 => '',
			'use_addthis' 				 => '',
			'show_related' 				 => '',
			'related_number'		     => '',			
			'show_upsell'		 		 => '',
			'upsell_number'              => '',
			'show_customtab'		     => '',			
			'customtab_name'		     => '',
			'customtab_content'		     => '',			
			/* advanced */
			'show_popup'		     	 => '1',
			'show_cpanel'		     	 => '1',
			'use_ajaxcart'		 		 => '1',
			'show_addtocart' 			 => '1',
			'show_wishlist'		     	 => '1',			
			'show_compare'		 		 => '1',
			'show_quickview'             => '1',
			'custom_copyright'		     => '',			
			'copyright'		     		 => '',
			'custom_css'		     	 => '',	
			'custom_js'		     		 => '',	
			'less_compile'				 => '1',
		);
	}

	function get($attributes=array()){
		$data           = $this->defaults;
		$general        = Mage::getStoreConfig("creative_cfg/general");
		$detail_creative = Mage::getStoreConfig("creative_cfg/detail_creative");
		$rich_snippets_setting = Mage::getStoreConfig("creative_cfg/rich_snippets_setting");
		$social_creative = Mage::getStoreConfig("creative_cfg/social_creative");
		$advanced 	    = Mage::getStoreConfig("creative_cfg/advanced");
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		if (is_array($general))	
		$data = array_merge($data, $general);
		if (is_array($detail_creative)) 				
		$data = array_merge($data, $detail_creative);
		if (is_array($rich_snippets_setting)) 				
		$data = array_merge($data, $rich_snippets_setting);
		if (is_array($social_creative)) 				
		$data = array_merge($data, $social_creative);
		if (is_array($advanced)) 				
		$data = array_merge($data, $advanced);
		
		return array_merge($data, $attributes);
	}
	
}
	 