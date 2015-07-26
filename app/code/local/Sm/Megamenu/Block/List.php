<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_List extends Mage_Core_Block_Template
{
	private static $parent_ignored = array();
	protected $_config = null;
	protected $_storeId = null;
	protected $_productCollection = null;
	protected $_allLeafId = null;
	protected $_allItemsFirstColumnId = null;
	protected $_allActivedItems = null; 
	protected $_allActivedId = null;
	protected $_typeCurrentUrl = null; 
	protected $_itemCurrentUrl = null; 
	
    public function getMegamenu()     
    { 
        if (!$this->hasData('megamenu')) {
            $this->setData('megamenu', Mage::registry('megamenu'));
        }
        return $this->getData('megamenu');
        
    }	
	public function __construct($attributes = array()){
		parent::__construct();
		$this->_config = Mage::helper('megamenu/default')->get($attributes);
		if(!$this->_config['isenabled']) return;
		
		
		$itemsLeaf = Mage::helper('megamenu')->getAllLeafByGroupId($this->_config['group_id']);
		$this->_allLeafId = ($itemsLeaf)?$itemsLeaf->getALLIds():'';
		if(!$this->_allItemsFirstColumnId){
			$itemsFirstColumn = Mage::helper('megamenu')->getAllItemsFirstByGroupId($this->_config['group_id']);
			$this->_allItemsFirstColumnId = ($itemsFirstColumn)?$itemsFirstColumn->getALLIds():'';			
		}
	}

	public function getConfig($name=null, $value=null){
		if (is_null($this->_config)){
			$this->_config = Mage::helper('megamenu/default')->get(null);
		}
		if (!is_null($name) && !empty($name)){
			$valueRet = isset($this->_config[$name]) ? $this->_config[$name] : $value;
			return $valueRet;
		}
		return $this->_config;
	}
	
	public function setConfig($name, $value=null){
		if (is_null($this->_config)) $this->getConfig();
		if (is_array($name)){
			$this->_config = array_merge($this->_config, $name);
			return;
		}
		if (!empty($name)){
			$this->_config[$name] = $value;
		}
		return true;
	}
	
	protected function _toHtml(){
		if(!$this->_config['isenabled']) return;
		//$template_file = 'sm/megamenu/megamenu.phtml';		
		//$this->setTemplate($template_file);
		if($this->filterRouter()){
			if($this->_typeCurrentUrl == Sm_Megamenu_Model_System_Config_Source_Type::CMSPAGE ){
				$item_id = $this->_itemCurrentUrl;
			}
			if($this->_typeCurrentUrl == Sm_Megamenu_Model_System_Config_Source_Type::PRODUCT ){
				$item_id = 'product/'.$this->_itemCurrentUrl->getId();
			}
			if($this->_typeCurrentUrl == Sm_Megamenu_Model_System_Config_Source_Type::CATEGORY ){
				$item_id = 'category/'.$this->_itemCurrentUrl->getId();
			}
			$this->_allActivedItems = Mage::helper('megamenu')->getAllActivedItems($this->_typeCurrentUrl, $item_id, $this->_config['group_id']);
			$this->_allActivedId = $this->_allActivedItems->getALLIds();
		};
		return parent::_toHtml();
	}
	
	public function getStoreId(){
		if (is_null($this->_storeId)){
			$this->_storeId = Mage::app()->getStore()->getId();
		}
		return $this->_storeId;
	}
	public function setStoreId($storeId=null){
		$this->_storeId = $storeId;
	}
	
	public function getConfigObject(){
		return $this->_config;
	}

	public function getItems(){
		//get Menu items
		$helper = Mage::helper('megamenu');
		$group_item = Mage::getModel('megamenu/menugroup')->load($this->_config['group_id']);
		if($group_item->getStatus() == Sm_Megamenu_Model_System_Config_Source_Status::STATUS_ENABLED){
			$collection_items = $helper ->getItemsByLv($this->_config['start_level'],$this->_config['group_id']);
			return $collection_items;
		}
		else{
			return array();
		}
	}

	public function getItemHtml2($item, $isFirstColumn =''){			// is testing
		$this->setCurrentItem ( $item );
		$this->setTemplate ('sm/megamenu/item.phtml');
		return parent::_toHtml();		
	}
	public function getItemHtml($item, $isFirstColumn =''){	
		$prefix = Sm_Megamenu_Model_System_Config_Source_Html::PREFIX;
		$align_right = "";
		// if($item->getDepth() == $this->_config['start_level']){
			// // $divClassName = ($this->isDrop($item))?'dropdown_'.$item->getColsNb().'columns':'';
			// $divClassName = $prefix.'dropdown_'.$item->getColsNb().'columns ';
			// if($this->isAlignRight($item)){
				// $divClassName .= $prefix.'align_right';
			// }
		// }
		// else{
			$divClassName = $prefix.'col_'.$item->getColsNb();
			
		// }
		// $divClassName =($this->isDrop($item))?'dropdown_'.$item->getColsNb().'columns':'col_'.$item->getColsNb();	//dropdown_4column or col_4
		$firstClassName =($this->isFirstCol($item) OR $isFirstColumn)?$prefix.'firstcolumn ':'';
		$aClassName = ($this->isDrop($item))?$prefix.'drop':$prefix.'nodrop';
// 		$activedClassName = ($this->isActived($item) AND $this->hasActivedType($item))?$prefix.'actived':'';
		$activedClassName = ($this->isActived($item))?$prefix.'actived':'';
		if($item->getAlign() == Sm_Megamenu_Model_System_Config_Source_Align::RIGHT){
			$align_right = $prefix."right";
		}	
		/*if($item->getFly() == Sm_Megamenu_Model_System_Config_Source_ListFly::TWO){
			$fly_left = $prefix."fly-left";
		}*/		
		$html = '<div class="'.$divClassName.' '.$firstClassName.' '.$prefix.'id'.$item->getId().$activedClassName.' '.$item->getCustomClass().'">';
		$link = ($this->hasLinkType($item))?$this->getLinkOfType($item):'#';
		$title = ($item->getShowTitle()==Sm_Megamenu_Model_System_Config_Source_Status::STATUS_ENABLED)?'<span class="'.$prefix.'title_lv'.$item->getDepth().'">'.$item->getTitle().'</span>':'&nbsp';
		$icon_title = ($this->hasIcon($item))?'<span style="background: url('.$this->filterImage($item).') no-repeat scroll 0% 50%  transparent;" class="'.$prefix.'icon">'.$title.'</span>':$title;		
				
		if($this->isDrop($item) OR $this->hasLinkType($item)){
			$headTitle = '<a class="'.$aClassName.' '.$activedClassName.'" href="'.$link.'" '.Mage::helper('megamenu/utils')->getTargetAttr($item->getTarget()).' >'.$icon_title.'</a>';
		}
		else{
			$headTitle = $icon_title;
		}
		if($item->getDepth() != $this->_config['start_level']){
			$html.= '<div class="'.$prefix.'head_item'.' '.$activedClassName.'">	';
			
			if($item->getShowTitle()==Sm_Megamenu_Model_System_Config_Source_Status::STATUS_ENABLED OR $this->hasIcon($item)){
				$addClass['title'] = $prefix.'title';
				$html.= '<div class="'.implode(' ',$addClass).'">'.$headTitle.'</div>';
				$addClass=array();	//reset array
			}
			if($item->getDescription()){
				$addClass['description'] = $prefix.'description';
				$html.= '<div class="'.implode(' ',$addClass).'"><p>'.$item->getDescription().'</p></div>';
			}
			
			$html.= '</div>';
		}
		if(!$this->isLeaf($item)){
			if($item->getDepth()+1 <= $this->_config['end_level']){
				
				$childItems = Mage::helper('megamenu')->getChildsDirectlyByItem($item);
				// if($item->getId() =='8'){
					// Zend_Debug::dump($childItems->getItems());
				// }
				if(!count($childItems->getItems())){	//fix issue: if item have child but child only and status child is disable
					if(!$this->hasLinkType($item)){
						$html.= '<div class="'.$prefix.'content">'.$this->getContentType($item).'</div>';
					}
					$html.= '</div>';
					return $html;
				}
				$cols_total = $item->getColsNb();
				$cols_sub = intval($cols_total);
				foreach($childItems as $childItem){
					$cols_sub = $cols_sub - intval($childItem->getColsNb());
					$isFirst = '';
					if($cols_sub < 0){			// if cols_sub 
						$isFirst = 'isFirstColumn';
						// $cols_sub = $cols_total;	// this is bug, fix as below
						$cols_sub = $cols_total - intval($childItem->getColsNb());	//reset cols_sub for new row
					}
					$html .= $this->getItemHtml($childItem, $isFirst);
				}
				$html .= '</div>';
			}	
			else{
				if(!$this->hasLinkType($item)){
					$html.= '<div class="'.$prefix.'content">'.$this->getContentType($item).'</div>';
				}			
				$html .= '</div>';
			}
		}
		else{
			if(!$this->hasLinkType($item)){
				$html.= '<div class="'.$prefix.'content">'.$this->getContentType($item).'</div>';
			}
			$html.= '</div>';
		}
		return $html;
	}

	public function isLeaf($item){
		return (in_array($item->getId(),$this->_allLeafId))?true:false;
	}
	public function isFirstCol($item){
		return (in_array($item->getId(),$this->_allItemsFirstColumnId))?true:false;
	}
	public function isDrop($item){
		return ($item->getShowAsGroup()==Sm_Megamenu_Model_System_Config_Source_Status::STATUS_DISABLED)?true:false;
	}
	public function isAlignRight($item){
		return ($item->getAlign()==Sm_Megamenu_Model_System_Config_Source_Align::RIGHT)?true:false;
	}
	
	/*public function isFlyLeft($item){
		return ($item->getFly()==Sm_Megamenu_Model_System_Config_Source_ListFly::TWO)?true:false;
	}*/
	
	public function hasIcon($item){
		return ($item->getIconUrl())?true:false;
	}
	public function isActived($item){
		if(!is_array($this->_allActivedId))
			return false;
		return (in_array($item->getId(),$this->_allActivedId))?true:false;
	}
	public function hasActivedType($item){
		$activedType = array(
			Sm_Megamenu_Model_System_Config_Source_Type::PRODUCT ,
			Sm_Megamenu_Model_System_Config_Source_Type::CATEGORY ,
			Sm_Megamenu_Model_System_Config_Source_Type::CMSPAGE ,
		);
		return (in_array($item->getType(),$activedType))?true:false;
	}
	public function hasLinkType($item){
		$linkType = array(
			Sm_Megamenu_Model_System_Config_Source_Type::EXTERNALLINK ,
			Sm_Megamenu_Model_System_Config_Source_Type::PRODUCT ,
			Sm_Megamenu_Model_System_Config_Source_Type::CATEGORY ,
			Sm_Megamenu_Model_System_Config_Source_Type::CMSPAGE ,
		);
		return (in_array($item->getType(),$linkType))?true:false;
	}
	public function hasConntentType($item){
		$contentType = array(
			Sm_Megamenu_Model_System_Config_Source_Type::CMSBLOCK ,
			Sm_Megamenu_Model_System_Config_Source_Type::CONTENT ,
		);
		return (in_array($item->getType(),$contentType))?true:false;
	}
	public function getLinkOfType($item){
		if($item->getType() == Sm_Megamenu_Model_System_Config_Source_Type::EXTERNALLINK){
			return $this->filterUrl($item);
		}
		elseif($item->getType() == Sm_Megamenu_Model_System_Config_Source_Type::PRODUCT){
			return $this->getProductLink($item);
		}
		elseif($item->getType() == Sm_Megamenu_Model_System_Config_Source_Type::CATEGORY){
			return $this->getCategoryLink($item);
		}
		elseif($item->getType() == Sm_Megamenu_Model_System_Config_Source_Type::CMSPAGE){
			return $this->getCMSPageLink($item);
		}
		else
			return '#';
	}
	public function getContentType($item){
		if($item->getType() == Sm_Megamenu_Model_System_Config_Source_Type::CMSBLOCK){
			return $this->getBlockPageHtml($item);
		}
		elseif($item->getType() == Sm_Megamenu_Model_System_Config_Source_Type::CONTENT){
			return $this->getContentHtml($item);
		}
		else{
			return false;
		}
	}

	public function filterUrl($item){
		// $link = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format(trim($item->getDataType())));
		$link = Mage::helper('catalog/product_url')->format(trim($item->getDataType()));
		$link = strtolower($link);		
		$haveHttp =  strpos($link, "http://"); 
		if(!$haveHttp && ($haveHttp!==0)){
			return "http://" . $link;  
		}else {
			return $link;
		}
	}
	public function filterImage($item){
		$params = explode('/',$item->getIconUrl());
		$key = array_search('___directive', $params);
        $directive = $params[$key+1];
        $directive = Mage::helper('core')->urlDecode($directive);
        $url = Mage::getModel('core/email_template_filter')->filter($directive);
		return $url;
	}
	public function getProductLink($item){
		$filter = explode('/',$item->getDataType());	// product/3
		$productId = $filter[1];			//3
		$product = Mage::getModel('catalog/product')->load($productId);
		return $product->getProductUrl();
	}
	public function getCategoryLink($item){
		$filter = explode('/',$item->getDataType());	// category/3
		$categoryId = $filter[1];			//3
		$category = Mage::getModel('catalog/category')->load($categoryId);
		return $category->getUrl();		
	}	
	public function getCMSPageLink($item){
		$cmspageId = $item->getDataType();
		return Mage::Helper('cms/page')->getPageUrl($cmspageId);
	}	
	public function getBlockPageHtml($item){
		$blockId = $item->getDataType();
		$block = Mage::getSingleton('core/layout')->createBlock('cms/block')->setBlockId($blockId);
		return $block->toHtml();
	}	
	public function getContentHtml($item){
		return $this->filterContent($item->getContent());
	}	
	public function filterContent($content){
		$helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
		//Zend_Debug::dump($this->getPage()->getContent());die;
		//echo $this->getPage()->getContent();die;
		//echo get_class($processor);die;
        $html = $processor->filter($content);
		return $html;
	}
	/*  
	 * 	filter router current page 
	 * 	return true mean url current have _typeCurrentUrl and _itemCurrentUrl
	 *  return false mean url current not 
	 *  */
	public function filterRouter(){
		$current_page = '';
		/*
		* Check to see if its a CMS page
		* if it is then get the page identifier
		*/
		if(Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms'){
			$this->_typeCurrentUrl = Sm_Megamenu_Model_System_Config_Source_Type::CMSPAGE ;
// 			$this->_itemCurrentUrl = Mage::getSingleton('cms/page')->getIdentifier() ;
			$this->_itemCurrentUrl = Mage::getSingleton('cms/page')->getId() ;
			
			return true;
		}
		/*
		* If its not CMS page, then just get the route name
		*/
		if(empty($current_page)){
			$current_page = Mage::app()->getFrontController()->getRequest()->getRouteName();
		}
		/*
		* What if its a catalog page?
		* Then we can get the catalog category or catalog product :)
		*/
		if($current_page == 'catalog'){
			// $current_page = preg_replace('#[^a-z0-9]+#', '-', strtolower(Mage::registry('current_category')->getUrlPath()));
			if($this->getRequest()->getControllerName()=='product') {
				$this->_typeCurrentUrl = Sm_Megamenu_Model_System_Config_Source_Type::PRODUCT ;
				$this->_itemCurrentUrl = Mage::registry('current_product') ;	
				return true;			
			}//do something
			if($this->getRequest()->getControllerName()=='category'){
				$this->_typeCurrentUrl = Sm_Megamenu_Model_System_Config_Source_Type::CATEGORY ;
				$this->_itemCurrentUrl = Mage::registry('current_category') ;
				return true;				
			} //do others
		}
		return false;
		// 		else do not anything	
	}
	public function getScriptTags(){
		$import_str = "";
		$jsHelper = Mage::helper('core/js');
		if (null == Mage::registry('jsmart.jquery')){
			// jquery has not added yet
			if (Mage::getStoreConfigFlag('megamenu_cfg/advanced/include_jquery')){
				// if module allowed jquery.
				$import_str .= $jsHelper->includeSkinScript('sm/megamenu/js/jquery.min.js');
				Mage::register('jsmart.jquery', 1);
			}
		}
		if (null == Mage::registry('jsmart.jquerynoconfict')){
			// add once noConflict
			$import_str .= $jsHelper->includeSkinScript('sm/megamenu/js/jsmart.noconflict.js');
			Mage::register('jsmart.jquerynoconfict', 1);
		}
		
		if (null == Mage::registry('jsmart.megamenu.js')){
			// add script for this module.
			// $import_str .= $jsHelper->includeSkinScript('sm/megamenu/js/jquery.hoveraccordion.js');
			//$import_str .= $jsHelper->includeSkinScript('sm/megamenu/js/js.js');
			//if( $this->getConfig('effect') != 3 ){
				$import_str .= $jsHelper->includeSkinScript('sm/megamenu/js/sm-megamenu.js');
			//}
			Mage::register('jsmart.megamenu.js', 1);
		}
		return $import_str;
	}

	private function _getResizedImage($Obj, $width, $height, $quality = 100) {
		if(is_object($Obj)){
			if (! $Obj->getImage ())
				$imageUrl = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "category" . DS ."no_image.gif";
			else
				$imageUrl = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "category" . DS . $Obj->getImage ();
		}
		else{
			if (! $Obj)
				$imageUrl = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "category" . DS ."no_image.gif";
			else
				$imageUrl = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "category" . DS . $Obj;
		}
		if (! is_file ( $imageUrl ))
			return false;
		$file = pathinfo($imageUrl);
		//Zend_Debug::dump($file);die;	
		$imageName = $file['filename']."_".$width."x".$height.".".$file['extension'];
		$imageResized = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "product" . DS . "cache" . DS . "cat_resized" . DS . $imageName;// Because clean Image cache function works in this folder only
		if (!file_exists ( $imageResized ) && file_exists ( $imageUrl ) || file_exists($imageUrl) && filemtime($imageUrl) > filemtime($imageResized)) {
			$imageObj = new Varien_Image ( $imageUrl );
			$imageObj->constrainOnly ( false );
			$imageObj->keepAspectRatio ( false );
			$imageObj->keepFrame ( false );
			$imageObj->quality ( $quality );
			$imageObj->resize ( $width, $height );
			$imageObj->save ( $imageResized );
		}
		
		if(file_exists($imageResized)){
			return Mage::getBaseUrl ( 'media' ) ."/catalog/product/cache/cat_resized/" . $imageName;
		}elseif(file_exists($Obj->getImageUrl())){
			return $Obj->getImageUrl();
		}else{
			return $this->getSkinUrl('sm/megamenu/images/no_image.gif');
		}
	}

	
}


