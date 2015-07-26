<?php
/*------------------------------------------------------------------------
 # SM Creative - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Creative_Model_System_Config_Source_ListBgImage{
	public function toOptionArray(){	
	$_urlmedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);	
		return array(
			array('value'=>'pattern1', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern1.png" />')),
			array('value'=>'pattern2', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern2.png" />')),			
			array('value'=>'pattern3', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern3.png" />')),
			array('value'=>'pattern4', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern4.png" />')),
			array('value'=>'pattern5', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern5.png" />')),
			array('value'=>'pattern6', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern6.png" />')),
			array('value'=>'pattern7', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern7.png" />')),
			array('value'=>'pattern8', 'label'=>Mage::helper('creative')->__('<img src="'.$_urlmedia.'/pattern/pattern8.png" />'))			
		);
	}
}
?>

