<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_ListGroup extends Varien_Object
{
	static public function getOptionArray(){
		foreach (Mage::getModel('megamenu/menugroup')->getCollection() as $group) 
		{   
			$arr[$group ->getTitle()] = $group ->getTitle();
		}
		return $arr;
	}	
	static public function toOptionArray(){
    	$arr[] = array(
			'value'			=>	'',
			'label'     	=>	Mage::helper('megamenu')->__('--Please Select--'),
		);
		foreach (Mage::getModel('megamenu/menugroup')->getCollection() as $group) 
		{   
			$arr[] = array(
				'value'		=>	$group ->getId(),
				'label'     => 	$group ->getTitle(),
			);
		}
		return $arr;
	}
}