<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/megamenu?id=15 
    	 *  or
    	 * http://site.com/megamenu/id/15 	
    	 */
    	/* 
		$megamenu_id = $this->getRequest()->getParam('id');

  		if($megamenu_id != null && $megamenu_id != '')	{
			$megamenu = Mage::getModel('megamenu/megamenu')->load($megamenu_id)->getData();
		} else {
			$megamenu = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($megamenu == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$megamenuTable = $resource->getTableName('megamenu');
			
			$select = $read->select()
			   ->from($megamenuTable,array('megamenu_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$megamenu = $read->fetchRow($select);
		}
		Mage::register('megamenu', $megamenu);
		*/

		// echo"";die;	
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function getitemsAction(){
		if ($params = Mage::app()->getRequest()->getParams()) {
			if($params['group']){				
				Mage::dispatchEvent('megamenu_menuitems_getItemsByGroupId',array('params'=>$params));
			}
		}
	}
	public function getchilditemsAction(){
		if ($params = Mage::app()->getRequest()->getParams()) {
			if($params['group']){					
				Mage::dispatchEvent('megamenu_menuitems_getChildItemsByParentId',array('params'=>$params));
			}
		}	
	}
	// public function testAction(){
		
		// $item = new Varien_Object(array(
			// 'lft'      => '13',
			// 'rgt'      => '18',
			// 'depth'	   => '2',
			// 'group_id' => '1',
		// ));
		// $collect = Mage::helper('megamenu')->getChildsDirectlyByItem($item,2);
		// Zend_Debug::dump($collect);die;
	// }
	// public function testAction(){
		// try{
			// $query[] = "UPDATE `sm_menu_items` SET lft = lft - 14, rgt = rgt - 14 WHERE lft >= 9 AND rgt <= 14 AND group_id='1';";
			// $query[] = "UPDATE `sm_menu_items` SET lft = lft + 6 WHERE lft > 38 AND group_id='1';";
			// $query[] = "UPDATE `sm_menu_items` SET rgt = rgt + 6 WHERE rgt > 38 AND group_id='1';";
			// $query[] = "UPDATE `sm_menu_items` SET rgt = rgt - 6 WHERE rgt > 14 AND group_id='1';";
			// $query[] = "UPDATE `sm_menu_items` SET lft = lft - 6 WHERE lft > 14 AND group_id='1';";
			// $query[] = "UPDATE `sm_menu_items` SET lft = lft + 38 + 0, rgt = rgt + 38 + 0 WHERE rgt <= 0 AND group_id='1';";
			// foreach($query as $q){
				// $write= Mage::getSingleton('core/resource') ->getConnection('core_write');			
				// $write->query($q);
			// }
			// die;
			// return true;
		// }
		// catch(Exception $e){
			// Mage::getSingleton('adminhtml/session')->addError($e->getMessage().__LINE__);
			// return;
		// }	
	// }
}