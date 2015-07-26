<?php

class Sm_Megamenu_Block_Adminhtml_Menuitems_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected $_data;
    protected $_defaultElementType = 'text';
	
	protected function _prepareForm()
	{
		if ( Mage::getSingleton('adminhtml/session')->getMenuitemsData() ){
			$data = Mage::getSingleton('adminhtml/session')->getMenuitemsData();
			Mage::getSingleton('adminhtml/session')->setMenuitemsData(null);
		} elseif ( Mage::registry('menuitems_data') ) {
			$data = Mage::registry('menuitems_data');
		}	
		if($data->getId()){
			$nametable =  Mage::getSingleton('core/resource')->getTableName('megamenu/menuitems');
			$parentData = Mage::helper('megamenu')->getParentIdNode($data->getId(),$data->getGroupId(), $nametable , $data->getLft(), $data->getRgt());
			// Zend_Debug::dump( $parentData);die;
			$data->setParentId($parentData->getId());		// after set parent id, field parent_id will have value default =$parentData->getId()
			$col_max = $parentData->getColsNb();
			$childNodes = Mage::helper('megamenu')->getSinglePath($data->getId(),$data->getGroupId(), $nametable , $data->getLft(), $data->getRgt());
			// $childNodes->getAllIds();
			// Zend_Debug::dump($childNodes->getAllIds());die;
		}
		else{
			$data = new Varien_Object();
			$data ->setData( array(
				'cols_nb' => '1',
				'item_width'=> '250',
			));
		}
		// Zend_Debug::dump($data);die;
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$form->setHtmlIdPrefix('megamenu_');
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false,'files_browser_window_url'=>$this->getBaseUrl().'admin/cms_wysiwyg_images/index/'));
		$fieldset = $form->addFieldset('menuitems_form', array('legend'=>Mage::helper('megamenu')->__('Menu Items information')));

		$fieldset->addField('title', 'text', array(
			'label'     => Mage::helper('megamenu')->__('Title'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'title',
		));
		
		$fieldset->addField('show_title', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Show Title'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'show_title',
			'values'    => Mage::getModel('megamenu/system_config_source_status')->toOptionArray(),	//$this->_getGroup(),
			'note'		=> 'note: not affect with menu top!',
		));	
		
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Status'),
			'name'      => 'status',
			'values'    => Mage::getModel('megamenu/system_config_source_status')->toOptionArray(),
		));
		$des =	$fieldset->addField('description', 'textarea', array(
			'title'     => Mage::helper('megamenu')->__('Description'),
			'label'     => Mage::helper('megamenu')->__('Description'),
			// 'class'     => 'required-entry',
			// 'required'  => true,
			'style'     => 'width:600px; height:75px;',
			'name'      => 'description',
			// 'after_element_html' =>$this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg')->getAfterElementHtml(),
		));
		// add editor for field 
		// $fieldset->addField('content', 'editor', array(					'name'      => 'content',					'label'     => Mage::helper('megamenu')->__('Content'),					'title'     => Mage::helper('megamenu')->__('Content'),					'style'     => 'width:600px; height:250px;',					'state'     => 'html',					'config'    => $wysiwygConfig,					'required'  => true,					)); 
		
		$fieldset->addField('align', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Align'),
			// 'class'     => 'required-entry',
			// 'required'  => true,
			'name'      => 'align',
			'values'    => Mage::getModel('megamenu/system_config_source_align')->toOptionArray(),	//$this->_getGroup(),
		));
		
		// $fieldset->addField('show_as_group', 'select', array(
			// 'label'     => Mage::helper('megamenu')->__('Show As Group'),
			// // 'class'     => 'required-entry',
			// // 'required'  => true,
			// 'name'      => 'show_as_group',
			// 'values'    => Mage::getModel('megamenu/system_config_source_status')->toOptionArray(),
		// ));	
		
		$group = $fieldset->addField('group_id', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Menu Group'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'group_id',
			'values'    => Mage::getModel('megamenu/system_config_source_listGroup')->toOptionArray(),	//$this->_getGroup(),
			// 'onchange'	=> (!$data->getId())?'loadItems(this,loadPosItems)':'',		//if add new , allow add js function LoadItems else disable field group_id
			//  'value'     => $this->_getGroupId(); 
		));
		$item  =$fieldset->addField('parent_id', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Parent Items'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'parent_id',
			'values'    => ($data->getId())?$this->_getItemsByGroupId($data[$group->getId()]):$this->_getItemsByGroupId(),	//if exists data[group_id] do action: get tree of this group, assign tree to dropdown parent_id
			'onchange'	=> 'loadPosItems(this)',	
			// 'value'     => $this->_getItemsSelected(); 
		));
		
		$group->setOnchange((!$data->getId())?'loadItems(this,loadPosItems,$(\''.$form->getHtmlIdPrefix().$item->getId().'\'))':'');
		
		if($data->getId()){
			$group->setDisabled('disabled')->setValue('');				//in future , update againt, with this case, allow enable field group_id, allow assign item menu to other group
			// $item->setDisabled('disabled')->setValue('');
		}	
		else{
			$group->setAfterElementHtml('
				<script>
					function loadItems(element,callback,param_callback)
					{	
						group_val = element.value;
						if(!group_val){
							$("'.$form->getHtmlIdPrefix().$item->getId().'").disabled = groupItem.allowDisabled;	//disable list item option
						}
						else{
							$("'.$form->getHtmlIdPrefix().$item->getId().'").disabled = groupItem.allowEnabled;
						}
						if(typeof(groupItem.listItems[group_val])!="undefined"){
							//get list options
							$("'.$form->getHtmlIdPrefix().$item->getId().'").update(groupItem.listItems[group_val]);	//inner string options to select box menu items
							return true;
						}
						else{
							//ajax update menu items tree
							groupItem.updateItems("'.Mage::getUrl('megamenu/index/getitems').'",group_val,function(json_ops){ 	//{success:"1",items: [ {id:"1", title:"item1"}, {id:"2" , title:"item2"} ]}
								str_ops = groupItem.getOptions(groupItem.opsTemp, json_ops["items"]);
								groupItem.listItems[group_val] = str_ops;
								$("'.$form->getHtmlIdPrefix().$item->getId().'").update(str_ops);	//inner string options to select box menu items	
								callback(param_callback);
							});
						}
					}
				</script>');  
		}
		$group_id = $form->getHtmlIdPrefix().$group->getId();
		$group_val = "";
		if($data->getId()){
			$group_val = $data[$group->getId()];		
		}
		if(!$group_val){
			$item->setDisabled('disabled');
		}
		
		if(!$data->getId()){
			$jsAfterItem = '		
					<script type="text/javascript">
							// save list items option current
							// do action save html of parent_id for each group id with "--Please Select--"
							groupItem.listItems[($("'.$group_id.'").value)?$("'.$group_id.'").value:"0"] = $("'.$form->getHtmlIdPrefix().$item->getId().'").innerHTML;						
					</script>
					';		
		}
		else{
			$items_disabled = (count($childNodes->getAllIds())>1)?implode(',',$childNodes->getAllIds()):'"'.implode(',',$childNodes->getAllIds()).'"';
			$jsAfterItem = '	
					<script type="text/javascript">
							// groupItem.listItems[($("'.$group_id.'").value)?$("'.$group_id.'").value:"0"] = $("'.$form->getHtmlIdPrefix().$item->getId().'").innerHTML;						
							// console.log($("'.$form->getHtmlIdPrefix().$item->getId().'")	);
							var items_disabled = new Array('.$items_disabled.');
							$$("select#'.$form->getHtmlIdPrefix().$item->getId().' option").each(function(element){
								
								for(var i=0; i<items_disabled.length ; i++){
									if(element.value == items_disabled[i]){
										element.disabled = true;
									}
								}
							});
					</script>			
			';
		}

		
		$order  =$fieldset->addField('id', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Order Item'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'order_item',
			'values'    => ($data->getId())?$this->_getOrderByParentId($data[$item->getId()]):$this->_getOrderByParentId(),	//if exists data[group_id] do action: get tree of this group, assign tree to dropdown parent_id
			// 'onchange'	=> (!$data->getId())?'loadPosItems(this)':'',	
			// 'value'     => $this->_getItemsSelected(); 
		));
		$jsAfterItem.= 	'
				<script>
					function loadPosItems(element)
					{	
						parent_val = element.value;
						if(!parent_val){
							//disable list order item option
							$("'.$form->getHtmlIdPrefix().$order->getId().'").disabled = parentItem.allowDisabled;
						}
						else{
							$("'.$form->getHtmlIdPrefix().$order->getId().'").disabled = parentItem.allowEnabled;
						}
						if(typeof(parentItem.listItems[parent_val])!="undefined"){
							//get list options
							//str_ops = parentItem.getOptions(parentItem.opsTemp, parentItem.listItems[parent_val]);
							//inner string options to select box menu items
							$("'.$form->getHtmlIdPrefix().$order->getId().'").update(parentItem.listItems[parent_val]);
							return true;
						}
						else{
							//ajax update menu items tree
							// console.log(parent_val);
							parentItem.updateItems("'.Mage::getUrl('megamenu/index/getchilditems').'",
								parent_val,
								function(json_ops){ 	//{success:"1",items: [ {id:"1", title:"item1"}, {id:"2" , title:"item2"} ]}
									if(!json_ops["items"].length){
										json_ops["items"] = new Array( "{\"id\":\"0\", \"title\":\"'.Mage::helper('megamenu')->__('This item is first').'\"}");
									}
									str_ops = parentItem.getOptions(parentItem.opsTemp, json_ops["items"]);	//convert data json to html input select
									parentItem.listItems[parent_val] = str_ops;
									//inner string options to select box menu items	
									$("'.$form->getHtmlIdPrefix().$order->getId().'").update(str_ops);	//inner input select to $order
									filterCol(json_ops["col_max"]);
								}
							);
						}
					}
				</script>';
		$item ->setAfterElementHtml($jsAfterItem);
		$radioPositionHtml = Mage::helper('megamenu')->__("Insert Item : "). 
			Mage::helper('megamenu')->__("Before")."<input type='radio' name='position_item' value='".Sm_Megamenu_Model_System_Config_Source_Position::BEFORE."' /> ||
			".Mage::helper('megamenu')->__("After")." <input type='radio' name='position_item' value='".Sm_Megamenu_Model_System_Config_Source_Position::AFTER."' checked/> 
		";
		
		// $group_id = $form->getHtmlIdPrefix().$item->getId();
		$parent_id = $form->getHtmlIdPrefix().$item->getId();
		if(!$data->getId()){
			$jsAfterOrder = '		
					<script type="text/javascript">
							// save list items option current
							// do action save html of order input for each parent id with "--Please Select--"
							parentItem.listItems[($("'.$parent_id.'").value)?$("'.$parent_id.'").value:"0"] = $("'.$form->getHtmlIdPrefix().$order->getId().'").innerHTML;						
					</script>
					';		
		}
		else{
			$jsAfterOrder = '	
					<script type="text/javascript">
							// groupItem.listItems[($("'.$parent_id.'").value)?$("'.$parent_id.'").value:"0"] = $("'.$form->getHtmlIdPrefix().$order->getId().'").innerHTML;						
							// console.log($("'.$form->getHtmlIdPrefix().$order->getId().'")	);
							var items_disabled = new Array('.implode(',',$childNodes->getAllIds()).');
							$$("select#'.$form->getHtmlIdPrefix().$order->getId().' option").each(function(element){
								for(var i=0; i<items_disabled.length ; i++){
									if(element.value == items_disabled[i]){
										element.disabled = true;
									}
								}
							});
					</script>			
			';
		}
		$order ->setAfterElementHtml($radioPositionHtml.$jsAfterOrder);	
		
$col =	$fieldset->addField('cols_nb', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Column Number'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'cols_nb',
			'values'    => Mage::getModel('megamenu/system_config_source_listNumCol')->toOptionArray(),
		));

		$fieldset->addField('custom_class', 'text', array(
		'label'     => Mage::helper('megamenu')->__('Custom Class '),
		'class'     => '',
		'required'  => false,
		'name'      => 'custom_class',
		));
		
		$jsAfterOrder = '		
				<script type="text/javascript">
						function filterCol(col_max){
							$$("select#'.$form->getHtmlIdPrefix().$col->getId().' option").each(function(element){
									if(element.value > col_max){
										element.disabled = false;
									}
							});
						}
				</script>
				';		
		if($data->getId()){
			$jsAfterOrder .= '	
					<script type="text/javascript">
							var col_max = '.$col_max.';
							$$("select#'.$form->getHtmlIdPrefix().$col->getId().' option").each(function(element){
									if(element.value > col_max){
										element.disabled = false;
									}
							});
					</script>			
			';
		}	
		$col ->setAfterElementHtml($jsAfterOrder);	
		
		// $fieldset->addField('item_width', 'text', array(
			// 'label'     => Mage::helper('megamenu')->__('Item Width '),
			// 'class'     => 'required-entry',
			// 'required'  => true,
			// 'name'      => 'item_width',
		// ));	
		// $fieldset->addField('cols_width', 'text', array(
			// 'label'     => Mage::helper('megamenu')->__('Column Width'),
			// 'class'     => 'required-entry',
			// 'required'  => true,
			// 'name'      => 'cols_width',
		// ));	
		$icon = $fieldset->addField('icon_url', 'text', array(
			'label'     => Mage::helper('megamenu')->__('Icon'),
			// 'class'     => 'required-entry',
			// 'required'  => true,
			'name'      => 'icon_url',
		));
		$url = Mage::helper('adminhtml')->getUrl('adminhtml/cms_wysiwyg_images/index');
		$storeId = null;
		$visible = true;

		$buttonsInsertImageHtml = Mage::getSingleton('core/layout')
			->createBlock('adminhtml/widget_button', '', array(
				'title'	  => Mage::helper('megamenu')->__('Insert Image...'),	
				'label'   => Mage::helper('megamenu')->__('Insert Image...'),
				// 'disabled' => false,
				'type'		=> 'button',
				'class' 	=> 'add-image plugin',
				'style'     => $visible ? '' : 'display:none',
				'onclick' => "MediabrowserUtility.openDialog('" .
								$url .
							   "target_element_id/" . $form->getHtmlIdPrefix().$icon->getId() . "/" .
								((null !== $storeId)
									? ('store/' . $storeId . '/')
									: '')
							   . "')",
			))->toHtml();		
		// $buttonsInsertImageHtml .= $this->_getJs($textarea);
		$icon ->setAfterElementHtml($buttonsInsertImageHtml. $this->_getJs($icon));
		// $icon->setAfterElementHtml('
			// <button style="" onclick="MediabrowserUtility.openDialog(\''.$url.'\')" class="scalable add-image plugin" type="button"><span>Insert Image...</span></button>												
		// ');

		$fieldset->addField('target', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Target window'),
			'name'      => 'target',
			'class'     => 'required-entry',
			'required'  => true,			
			'values'    => Mage::getModel('megamenu/system_config_source_linkTargets')->toOptionArray(),
		));	

		$type =	$fieldset->addField('type', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Menu Type'),
			'name'      => 'type',
			'class'     => 'required-entry',
			'required'  => true,			
			'values'    => Mage::getModel('megamenu/system_config_source_type')->toOptionArray(),
			'onchange'	=>'CheckType(this)',
		));	
		
		$data_type = $fieldset->addField('data_type', 'text', array(
			'label'     => Mage::helper('megamenu')->__('Data Type'),
			'class'     => 'data_type',
			'required'  => true,
			// 'disabled'	=> true,
			'name'      => 'data_type',
		));	
		
	
		$addwidget = Mage::getSingleton('core/layout')->createBlock('megamenu/adminhtml_widget_addField');		
		$addwidget->addFieldWidget(array(
				'id' 			=> 'product_id',
				'sort_order'	=> '10',
				'label'			=> 'Product',
				'button'		=> array( 'text' => array('open'=> 'Select Product...'),
										  'type' => 'adminhtml/catalog_product_widget_chooser'),
		),$fieldset);


		$addwidget->addFieldWidget(array(
				'id' 			=> 'category_id',
				'sort_order'	=> '11',
				'label'			=> 'Category',
				'button'		=> array( 'text' => array('open'=> 'Select Category...'),
										  'type' => 'adminhtml/catalog_category_widget_chooser'),
		),$fieldset);	

		$addwidget->addFieldWidget(array(
				'id' 			=> 'page_id',
				'sort_order'	=> '12',
				'label'			=> 'CMS Page',
				'button'		=> array( 'text' => array('open'=> 'Select Page...'),
										  'type' => 'adminhtml/cms_page_widget_chooser'),
		),$fieldset);	

		$addwidget->addFieldWidget(array(
				'id' 			=> 'block_id',
				'sort_order'	=> '13',
				'label'			=> 'CMS Block',
				'button'		=> array( 'text' => array('open'=> 'Select Block...'),
										  'type' => 'adminhtml/cms_block_widget_chooser'),
		),$fieldset);		
		
		$textarea =	$fieldset->addField('content', 'textarea', array(
			'title'     => Mage::helper('megamenu')->__('Content'),
			'label'     => Mage::helper('megamenu')->__('Content'),
			// 'class'     => 'required-entry ',
			'style'     => 'width:600px; height:150px;',
			// 'required'  => true,
			'name'      => 'content',
			'note'		=> 'Content width must match the number of column pixel in the Column Number field',
			// 'after_element_html' =>$this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg')->getAfterElementHtml(),
		));

		$html = Mage::getSingleton('core/layout')
			->createBlock('adminhtml/widget_button', '', array(
				'label'   => Mage::helper('catalog')->__('WYSIWYG Editor'),
				'type'    => 'button',
				'disabled' => false,
				'class' => (false) ? 'disabled' : $form->getHtmlIdPrefix().'box_content',
				'onclick' => 'catalogWysiwygEditor.open(\''.Mage::helper('adminhtml')->getUrl('adminhtml/catalog_category/wysiwyg').'\', \''.$form->getHtmlIdPrefix().$textarea->getId().'\')'
			))->toHtml();
        // $wysiwyg_js = Mage::getSingleton('core/layout')->createBlock('catalog.wysiwyg.js');
        // if ($wysiwyg_js) {
            // $wysiwyg_js->setStoreId($storeId);
        // }	
		
		$block_js = Mage::getSingleton('core/layout')
							->createBlock('core/template')
							->setTemplate('catalog/wysiwyg/js.phtml');
		// echo $block_js->toHtml();die;
		$html .= $block_js->toHtml();
		
		// $block->append($blockjs);		
		// echo $html;die;
		// $editor = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg');//->getAfterElementHtml();
		
		$textarea ->setAfterElementHtml($html);
		//end process textarea content

		$type_val = "";
		$js_type = "";
		if($data->getId()){
			$type_val = $data[$type->getId()];	
			$data_type_val = $data[$data_type->getId()];
			$js_type = '
					data_val['.$type_val.'] = "'.$data_type_val.'";
					CheckType($(\''.$form->getHtmlIdPrefix().$type->getId().'\'));		
			';			
		}	
		
		$type->setAfterElementHtml('									
					<script type="text/javascript">										
						// check type 	
						var data_val = new Array();
						window.onload = function(){
							$$("div[id^=\''.$form->getHtmlIdPrefix().'box_\']").each(function(element){element.up().up().hide();});	
							$$("[id^=\''.$form->getHtmlIdPrefix().'content\']").each(function(element){element.up().up().hide();});	
							$$(".data_type").each(function(element){element.up().up().hide();element.removeClassName("required-entry");});	
							$(\''.$form->getHtmlIdPrefix().$type->getId().'\').observe("focus",function(event){
								var element = Event.element(event);
								data_val[element.value] = $$(".data_type")[0].value;
							});
							'.$js_type.'
						}												
						function CheckType(element){							
							type = element.value;
							if(typeof(data_val[type]) !="undefined"){
								$$(".data_type")[0].value = data_val[type];
							}								
							else{								
								$$(".data_type")[0].value ="";	
							}											
							$$("div[id^=\''.$form->getHtmlIdPrefix().'box_\']").each(function(element){element.up().up().hide();});	
							$$(".data_type").each(function(element){element.up().up().hide();element.removeClassName("required-entry");});			
							$$("[id^=\''.$form->getHtmlIdPrefix().'content\']").each(function(element){element.up().up().hide();element.removeClassName("required-entry");});
							if(type=='.Sm_Megamenu_Model_System_Config_Source_Type::CONTENT.'){	
								$$("[id^=\''.$form->getHtmlIdPrefix().'content\']").each(function(element){element.up().up().show();element.addClassName("required-entry");});
							}																					
							else if(type!='.Sm_Megamenu_Model_System_Config_Source_Type::NORMAL.'){																			
								$$(".data_type").each(function(element){element.up().up().show();element.addClassName("required-entry");});					
							}																				
							if(type=='.Sm_Megamenu_Model_System_Config_Source_Type::PRODUCT.'){	
								$$("div[id^=\''.$form->getHtmlIdPrefix().'box_product_id\']")[0].up().up().show();								
							}																							
							if(type=='.Sm_Megamenu_Model_System_Config_Source_Type::CATEGORY.'){													
								$$("div[id^=\''.$form->getHtmlIdPrefix().'box_category_id\']")[0].up().up().show();												
							}																						
							if(type=='.Sm_Megamenu_Model_System_Config_Source_Type::CMSPAGE.'){									
								$$("div[id^=\''.$form->getHtmlIdPrefix().'box_page_id\']")[0].up().up().show();									
							}																							
							if(type=='.Sm_Megamenu_Model_System_Config_Source_Type::CMSBLOCK.'){										
								$$("div[id^=\''.$form->getHtmlIdPrefix().'box_block_id\']")[0].up().up().show();										
							}																	
						}																	
					</script>															
		');		
			
		
		if ( $data )
		{
			$form->setValues($data);
			$this->_data = $data;
		} 
	
		return parent::_prepareForm();
	}
	protected function _getItemsByGroupId($group_id=''){
		// get array list group id
    	$arr[] = array(
			'value'			=>	'',
			'label'     	=>	Mage::helper('megamenu')->__('--Please Select--'),
		);	
		if($group_id){
			$items = Mage::helper('megamenu')->getNodesByGroupId($group_id, true);
			foreach ($items as $item) 
			{   
				$item_id = $item ->getId();
				$title = $item ->getName();
				$arr[] = array(
									'value'			=>	$item_id,
									'label'     	=>	$title,
								);
			}
			
		}
		return $arr;		
	}	
	protected function _getOrderByParentId($parent_id){
		// get array list group id
    	$arr[] = array(
			'value'			=>	'',
			'label'     	=>	Mage::helper('megamenu')->__('--Please Select--'),
		);		
		if($parent_id){
			$childItems = Mage::helper('megamenu')->getChildsDirectlyByItem( Mage::getModel('megamenu/menuitems')->load($parent_id), 2);
			foreach ($childItems as $item) 
			{   
				$item_id = $item ->getId();
				$title = '('.$item_id.') '.$item ->getTitle();
				$arr[] = array(
									'value'			=>	$item_id,
									'label'     	=>	$title,
								);
			}
			
		}
		// Zend_Debug::dump($arr);die;
		return $arr;	
	}
	public function _toHtml(){

        // Get the default HTML for this option
        $html = parent::_toHtml();
		$modelMenuitems = false;
		if($this->_data){
			$data = $this->_data;
			if($data->getId()){
				$modelMenuitems = true;
			}
		}
		//  tam thoi khoa' objTreeitems khi edit item, 
		// if(!$modelMenuitems){
			$html = '
			<script type="text/javascript">
				if(typeof objTreeitems=="undefined") {
					var objTreeitems = {};
				}
				var objTreeitems=Class.create();
				objTreeitems.prototype=	{
					initialize: function(){
						this.opsTemp=\'<option  value="#{id}">#{title}</option>\';	
						this.listItems = [
							//"group_id", "listOps"
							//{"1": [ {id:"1", title:"item1"}, {id:"2" , title:"item2"} ]},
							// "1" => "<option value=\'1\' selected=\'selected\'>menuitem1</option>"
						];
						this.allowDisabled = 1;
						this.allowEnabled = 0;
					},
					updateItems: function(url,group_value,callback){	//update cac items = ajax
						new Ajax.Request(url,{encoding:"UTF-8",method:"post", 
							parameters:{
								group:group_value //param for request
								,addprefix:true
							},
							onSuccess: function(resp){	//resp chua du lieu tra ve cua request
								resp = resp.responseText.evalJSON();	// loc lay text
								callback(resp);
								//respStripScripts = resp.stripScripts();	//loai bo javascript trong chuoi
								//$("div-id").innerHTML =  respStripScripts;	//dua du lieu vao div 
							},
							onLoading : function(){
								$("loading-mask").show();	
							},
							onFailure : function(resp){
								console.log(resp.responseText); //Element.setInnerHTML( display, resp.responseText);
							},
							onComplete: function(){
								$("loading-mask").hide();	
							}						
						});
					},
					getOptions: function(temp, list_ops){	//getOptions.bindAsEventListener(temp, list_ops), temp is template build <option...>...<> ,list_ops = [ {id:"1", title:"item1"}, {id:"2" , title:"item2"} ]
						//var element = Event.element(event);		//get this
						ops_temp = new Template(temp);	// initialize instanl template
						var ops_html = "";
						//console.log(list_ops.responseText.evalJSON());
						for(var i=0; i< list_ops.length; i++){
							//console.log(list_ops[i].evalJSON());
							ops_html += ops_temp.evaluate(list_ops[i].evalJSON());			//fill data to template
						}
						return ops_html;
					}
				}
				var groupItem= new objTreeitems();
				var parentItem = new objTreeitems();
				var columnItem = new objTreeitems();
			</script>'
			.$html	;
		// }
		return $html;
	}
 
	protected function _getJs($element){
            // $jsSetupObject = 'wysiwyg' .$element->getForm()->getHtmlIdPrefix(). $element->getHtmlId();	
		$js = '
            <script type="text/javascript">
            //<![CDATA[
                openEditorPopup = function(url, name, specs, parent) {
                    if ((typeof popups == "undefined") || popups[name] == undefined || popups[name].closed) {
                        if (typeof popups == "undefined") {
                            popups = new Array();
                        }
                        var opener = (parent != undefined ? parent : window);
                        popups[name] = opener.open(url, name, specs);
                    } else {
                        popups[name].focus();
                    }
                    return popups[name];
                }

                closeEditorPopup = function(name) {
                    if ((typeof popups != "undefined") && popups[name] != undefined && !popups[name].closed) {
                        popups[name].close();
                    }
                }
            //]]>
            </script>';			
		return $js;
	}
}