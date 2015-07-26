<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_Observer
{
	public function createRoot($observer){
		$modelMenugroup = $observer->getMenugroup();
		$groupId = $modelMenugroup->getId();
		$groupTitle = $modelMenugroup->getTitle();
		$modelMenuitems = Mage::getModel('megamenu/menuitems');
		$modelMenuitems	->setGroupId($groupId)
						->setDepth(0)
						->setLft(1)
						->setRgt(2)
						->setColsNb(6)
						->setShowAsGroup(1)
						->setStatus(1)
						->setTitle(Mage::helper('megamenu')->__('Root[%s]',$groupTitle));
		try {
			if ($modelMenuitems->getCreatedTime == NULL || $modelMenuitems->getUpdateTime() == NULL) {
				$modelMenuitems->setCreatedTime(now())
					->setUpdateTime(now());
			} else {
				$modelMenuitems->setUpdateTime(now());
			}	
			
			$modelMenuitems->save();		
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			return;
		}		
	}
	public function setItemLRWhileEdit($observer){	//set item LR while edit item
		$model = $observer->getMenuitems();
		$selfId = $model->getData('id');
		$orderItemId = $model->getData('order_item'); 
		if($orderItemId == $selfId){
			$modelMenuitems = Mage::getModel('megamenu/menuitems')->load($selfId);
			$data = $modelMenuitems->getData();
			$nametable =  Mage::getSingleton('core/resource')->getTableName('megamenu/menuitems');
			$parentSelf = Mage::helper('megamenu')->getParentIdNode($selfId, $data['group_id'], $nametable, $data['lft'], $data['rgt']);
			// Zend_Debug::dump($parentSelf);die;
			$parentPostByForm = $model->getData('parent_id');
			if($parentSelf->getId() == $parentPostByForm){		//not conflict parent in real time
				return true;
			}
			else{	//exists action update this item for other parent by manager website 2 st before
				Mage::getSingleton('adminhtml/session')->addError('exists action update this item for other parent by manager website 2 st before');
				$this->_redirectReferer();
				return false;				
			}
		}
		elseif($orderItemId ==0){	// first child of parent 
			// $itemId = $model->getData('parent_id');
			// $modelMenuitems = Mage::getModel('megamenu/menuitems')->load($itemId);
			// $data = $modelMenuitems->getData();
			// $depth =  intval($data['depth'])+1;	
			// $model->setData('depth', $depth);			
			return true;
		}
		else{						// need pos_item to determine before or after of $orderItemId
			// $itemId = $model->getData('parent_id');
			// $modelMenuitems = Mage::getModel('megamenu/menuitems')->load($itemId);
			// $data = $modelMenuitems->getData();
			// $depth =  intval($data['depth'])+1;	
			// $model->setData('depth', $depth);			
			return true;
		}
		return false;
	}
	public function updateItemsLRWhileEdit($observer){	//set item LR while edit item
		$model = $observer->getMenuitems();
		$selfId = $model->getData('id');
		$modelMenuitems = Mage::getModel('megamenu/menuitems')->load($selfId);
		$orderItemId = $model->getData('order_item');
		$order = $model->getData('position_item');
		$parentId = $model->getData('parent_id');
		$modelParent = Mage::getModel('megamenu/menuitems')->load($parentId);
		$parentDepth = $modelParent->getData('depth');
		$myLeft = $modelMenuitems->getData('lft');
		$myRight = $modelMenuitems->getData('rgt');
		$myDepth = $modelMenuitems->getData('depth');
		$wDepth = $parentDepth - $myDepth +1;
		if($orderItemId == $selfId){
			$modelMenuitems = Mage::getModel('megamenu/menuitems')->load($selfId);
			$data = $modelMenuitems->getData();
			$nametable =  Mage::getSingleton('core/resource')->getTableName('megamenu/menuitems');
			$parentSelf = Mage::helper('megamenu')->getParentIdNode($selfId, $data['group_id'], $nametable, $data['lft'], $data['rgt']);
			$parentPostByForm = $model->getData('parent_id');
			if($parentSelf->getId() == $parentPostByForm){		//not conflict parent in real time
				return true;
			}
			else{	//exists action update this item for other parent by manager website 2 st before
				Mage::getSingleton('adminhtml/session')->addError('exists action update this item for other parent by manager website 2 st before');
				$this->_redirectReferer();
				return false;				
			}
		}
		elseif($orderItemId ==0){	// first child of parent 
			$modelMenuParent = Mage::getModel('megamenu/menuitems')->load($parentId);
			$groupId = $modelMenuParent->getGroupId();
			$order = Sm_Megamenu_Model_System_Config_Source_Position::FIRST;
			Mage::helper('megamenu')->insertNode($order, $groupId, $nametable, $myLeft, $myRight, $modelMenuParent, $wDepth);
			return true;
		}
		else{						// need pos_item to determine before or after of $orderItemId
			$modelMenuOrderItem = Mage::getModel('megamenu/menuitems')->load($orderItemId);	
			$nametable =  Mage::getSingleton('core/resource')->getTableName('megamenu/menuitems');
			$parentSelf = Mage::helper('megamenu')->getParentIdNode($orderItemId, $data['group_id'], $nametable, $modelMenuOrderItem->getData('lft'), $modelMenuOrderItem->getData('rgt'));
			$groupId = $modelMenuOrderItem->getGroupId();	
			Mage::helper('megamenu')->insertNode($order, $groupId, $nametable, $myLeft, $myRight, $modelMenuOrderItem, $wDepth);
			return true;
		}
		return false;
	}	
	public function setItemLR($observer){ 	//set item LR while add new item
		$model = $observer->getMenuitems();
		try {
			if($model->getData('order_item')){
				$itemId = $model->getData('order_item');
				$modelMenuitems = Mage::getModel('megamenu/menuitems')->load($itemId);
				$data = $modelMenuitems->getData();
				if($model->getData('position_item') == Sm_Megamenu_Model_System_Config_Source_Position::AFTER){		//after item:
					$lft = intval($data['rgt'])+1;
					$rgt = intval($data['rgt'])+2;
					$depth =  intval($data['depth']);
				}
				elseif($model->getData('position_item') == Sm_Megamenu_Model_System_Config_Source_Position::BEFORE){		//before item:
					$lft = intval($data['lft']);
					$rgt = intval($data['lft'])+1;
					$depth =  intval($data['depth']);				
				}
			}
			else{
				$itemId = $model->getData('parent_id');
				$modelMenuitems = Mage::getModel('megamenu/menuitems')->load($itemId);
				$data = $modelMenuitems->getData();
				$lft = intval($data['rgt']);
				$rgt = intval($data['rgt'])+1;
				$depth =  intval($data['depth'])+1;			
			}
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			return false;
		}		
		$model->setData('lft', $lft);
		$model->setData('rgt', $rgt);
		$model->setData('depth', $depth);
		return true;	
	}
	public function updateItemsLR($observer){
		$model = $observer->getMenuitems();
		// $parentId = $model->getData('parent_id');
		$myLeft = $model->getData('lft');
		$groupId = $model->getData('group_id');
		$id = $model->getId();		// $id of new node
		// $currentDate = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
		$modelMenuitems = Mage::getModel('megamenu/menuitems');
		$nametable = $modelMenuitems->getCollection()->getTable('menuitems');
		Mage::helper('megamenu')->updateRSide($id, $groupId, $nametable, $myLeft);	//update left, right of nodes by right side of new node have $id
	}
	public function deleteItemsLR($observer){
		$model = $observer->getMenuitems();
		// $parentId = $model->getData('parent_id');
		$myLeft = $model->getData('lft');
		$myRigth = $model->getData('rgt');
		$groupId = $model->getData('group_id');
		$id = $model->getId();
		// $currentDate = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
		$modelMenuitems = Mage::getModel('megamenu/menuitems');
		$nametable = $modelMenuitems->getCollection()->getTable('menuitems');
		Mage::helper('megamenu')->deleteNode($id, $groupId, $nametable, $myLeft, $myRigth);
		Mage::helper('megamenu')->updateRSideOfDeletedNode($id, $groupId, $nametable, $myLeft, $myRigth);
	}	
	public function getItemsByGroupId($observer){
		$params = $observer->getParams();	
		$helper = Mage::helper('megamenu');
		$items_data = $helper->getNodesByGroupId($params['group'], $params['addprefix']);
		$arr_items = array();
		$success ="true";
		if(COUNT($items_data)){
			foreach ($items_data as $item){
				$arr_items[] = '{"id":"'.$item["id"].'", "title":'.json_encode($item["name"]).'}';
			}
			header('Content-Type: application/x-json');
			echo '{"success":"'.$havedata.'", "items":' . json_encode($arr_items) . '}';
		}
		else{
			echo '{"success":"'.$havedata.'", "items":' . json_encode($arr_items) . '}';
		}
		die;
	}
	public function getChildItemsByParentId($observer){
		$params = $observer->getParams();	
		$helper = Mage::helper('megamenu');
		$paramsItem = Mage::getModel('megamenu/menuitems')->load($params['group']);
		
		$items_data = $helper->getChildsDirectlyByItem($paramsItem, 2);
		$cols_num = 6;
		if($paramsItem->getColsNb()){
			$cols_num = $paramsItem->getColsNb();
		}
		$arr_items = array();
		$success ="true";
		if(COUNT($items_data->getItems())){
			foreach ($items_data->getItems() as $item){
				$arr_items[] = '{"id":"'.$item->getId().'", "title":'.json_encode('('.$item->getId().') '.$item->getTitle()).'}';
			}
			header('Content-Type: application/x-json');
			echo '{"success":"'.$havedata.'", "items":' . json_encode($arr_items) . ', "col_max":"'.$cols_num.'"}';
		}
		else{
			echo '{"success":"'.$havedata.'", "items":' . json_encode($arr_items) . ', "col_max":"'.$cols_num.'"}';
		}
		die;
	}	
}
