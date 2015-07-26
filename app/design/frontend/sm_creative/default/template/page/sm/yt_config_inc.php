<?php
/*------------------------------------------------------------------------
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

/*--- BEGIN: Theme param config ---*/
$_params = new ThemeParameter();
$_config = array(	
	'layout_styles'					=>'1',
	'color'							=>'green',
	'device_responsive'             =>'1',
	'body_font_family'				=>'',
	'body_font_size'				=>'13px',
	'google_font'	            	=>'Raleway',
	'google_font_targets'			=>'',
	'body_link_color'				=>'#666666',
	'body_link_hover_color'			=>'',
	'body_text_color'				=>'#666666',
	'body_title_color'				=>'#444444',
	'body_background_color'			=>'#ffffff',	
	'use_background_image'			=>'0',
	'body_background_image'			=>'',
	'use_customize_image'			=>'0',
	'background_customize_image'	=>'',
	'background_repeat'				=>'',
	'background_position'			=>'',
	'menu_ontop'					=>'0',	
	'responsive_menu'           	=>'1',
	'show_imagezoom'				=>'1',
	'zoom_mode'						=>'1',
	'use_addthis'					=>'1',
	'show_related'					=>'1',
	'related_number'				=>'',
	'show_upsell'					=>'1',
	'upsell_number'					=>'',
	'show_customtab'				=>'1',
	'customtab_name'				=>'',
	'customtab_content'   			=>'',
	'show_facebook'					=>'1',
	'facebook_name'					=>'',
	'facebook_url'					=>'',
	'show_twitter'					=>'1',
	'twitter_name'					=>'',
	'twitter_url'					=>'',
	'show_google'					=>'1',
	'google_name'					=>'',
	'google_url'					=>'',	
	'show_linkedin'					=>'1',
	'linkedin_name'					=>'',
	'linkedin_url'					=>'',
	'show_flickr'					=>'1',
	'flickr_name'					=>'',
	'flickr_url'					=>'',
	'show_pinterest'				=>'1',
	'pinterest_name'				=>'',
	'pinterest_url'					=>'',	
	'show_popup'					=>'1',
	'show_cpanel'					=>'1',
	'show_ontop'					=>'1',
	'show_addtocart'				=>'1',
	'show_addtocart'				=>'1',
	'show_compare' 					=>'1',
	'custom_copyright'				=>'0',
	'copyright'						=>'',
	'custom_css'					=>'',
	'custom_js'						=>'',	
);
$attributes = array();
if( Mage::getConfig()->getNode('modules/Sm_Creative') ){
	$_config = Mage::helper('creative/data')->get($attributes);
}
// Layout
if($_config['layout_styles'] == 1) { $layout_style='1';}	
if($_config['layout_styles'] == 2) { $layout_style='2';}	
$_params->set('layoutstyle',$layout_style);

// Device Responsive
$_params->set('device_responsive', $_config['device_responsive']);
// Theme color
$_params->set('theme_color',$_config['color']);
// font family
$_params->set('font_name',$_config['body_font_family']);
// Fontsize
$_params->set('fontsize',$_config['body_font_size']);
// Google web font
$_params->set('googleWebFont',$_config['google_font']);
// Google WebFont Targets
$_params->set('googleWebFontTargets',$_config['google_font_targets']);
// Body link color
$_params->set('linkcolor', $_config['body_link_color']);
// Body link color hover
$_params->set('linkcolorhover', $_config['body_link_hover_color']);
// Body text color
$_params->set('textcolor', $_config['body_text_color']);
// Body title color
$_params->set('titlecolor', $_config['body_title_color']);
// Title color Targets
$_params->set('titlecolorTargets',$_config['title_color_targets']);
// Body background-color
$_params->set('bgcolor', $_config['body_background_color']);
// Body background-image
$_params->set('usebgimage', $_config['use_background_image']);
// Body background-image
$_params->set('bgimage', $_config['body_background_image']);
// Body use_customize_image
$_params->set('usecustomizeimage', $_config['use_customize_image']);
// Body background_customize_image
$_params->set('bgcustomizeimage', $_config['background_customize_image']);
// Body background-repeat
$_params->set('bgimagerepeat', $_config['background_repeat']);
// Body background-position
$_params->set('bgimageposition', $_config['background_position']);
// Menu on Top
$_params->set('menuontop',$_config['menu_ontop']);
// Respionsive menu
$_params->set('responsivemenu',$_config['responsive_menu']);
// Detail show_imagezoom
$_params->set('show_imagezoom',$_config['show_imagezoom']);
// Detail zoom_mode
$_params->set('zoom_mode',$_config['zoom_mode']);
// Detail use_addthis
$_params->set('use_addthis',$_config['use_addthis']);
// Detail show_related
$_params->set('show_related',$_config['show_related']);
// Detail related_number
$_params->set('related_number',$_config['related_number']);
// Detail show_upsell
$_params->set('show_upsell',$_config['show_upsell']);
// Detail upsell_number
$_params->set('upsell_number',$_config['upsell_number']);
// Detail show_customtab
$_params->set('show_customtab',$_config['show_customtab']);
// Detail customtab_name
$_params->set('customtab_name',$_config['customtab_name']);
// Detail customtab_content
$_params->set('customtab_content',$_config['customtab_content']);
// Show Facebook
$_params->set('show_facebook',$_config['show_facebook']);
// Facebook Name
$_params->set('facebook_name',$_config['facebook_name']);
// Facebook Url
$_params->set('facebook_url',$_config['facebook_url']);
// Show Twitter
$_params->set('show_twitter',$_config['show_twitter']);
// Twitter Name
$_params->set('twitter_name',$_config['twitter_name']);
// Twitter Url
$_params->set('twitter_url',$_config['twitter_url']);
// Show Google+
$_params->set('show_google',$_config['show_google']);
// Google+ Name
$_params->set('google_name',$_config['google_name']);
// Google+ Url
$_params->set('google_url',$_config['google_url']);
// Show Linkedin
$_params->set('show_linkedin',$_config['show_linkedin']);
// Linkedin Name
$_params->set('linkedin_name',$_config['linkedin_name']);
// Linkedin Url
$_params->set('linkedin_url',$_config['linkedin_url']);
// Show Flickr
$_params->set('show_flickr',$_config['show_flickr']);
// Flickr Name
$_params->set('flickr_name',$_config['flickr_name']);
// Flickr Url
$_params->set('flickr_url',$_config['flickr_url']);
// Show Pinterest
$_params->set('show_pinterest',$_config['show_pinterest']);
// Pinterest Name
$_params->set('pinterest_name',$_config['pinterest_name']);
// Pinterest Url
$_params->set('pinterest_url',$_config['pinterest_url']);
// Show pop up location
$_params->set('showPopup',$_config['show_popup']);
// Show cpanel
$_params->set('showCpanel',$_config['show_cpanel']);
// Show on Top
$_params->set('showontop',$_config['show_ontop']);
// Show addtocart
$_params->set('show_addtocart',$_config['show_addtocart']);
// Show wishlist
$_params->set('show_wishlist',$_config['show_wishlist']);
// Show compare
$_params->set('show_compare',$_config['show_compare']);
// Use custom_copyright
$_params->set('custom_copyright',$_config['custom_copyright']);
// Show copyright
$_params->set('copyright',$_config['copyright']);
// Use custom_css
$_params->set('custom_css',$_config['custom_css']);
// Show custom_js
$_params->set('custom_js',$_config['custom_js']);

// Array param for cookie
$paramscookie = array(
				  'theme_color',
				  'layoutstyle'
);
global $var_yttheme;
$var_yttheme = new YtTheme('sm_creative', $_params, $paramscookie);
