<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Widget_AddField extends Mage_Core_Block_Template
{
	protected	$_p ;
	protected	$_b ;

	public function __construct(){
		$this->_p = new Varien_Object();
		$this->_b = new Varien_Object();		
	}
	public function addFieldWidget($arr, $fieldset){
		$param = $this->_p;
		$button = $this->_b;	
		$param->setKey(($arr['id'])?$arr['id']:'empty_id');
		$param->setVisible(($arr['visible'])?$arr['visible']:1);
		$param->setRequired(($arr['required'])?$arr['required']:1);
		$param->setType(($arr['type'])?$arr['type']:'Label');
		$param->setSortOrder(($arr['sort_order'])?$arr['sort_order']:1);
		$param->setValues(($arr['values'])?$arr['values']:array());
		$param->setLabel(($arr['label'])?$arr['label']:'Empty');

		$button->setButton(($arr['button']['text'])?$arr['button']['text']:array('open'=>'Select...'));
		$button->setType(($arr['button']['type'])?$arr['button']['type']:'');
		$param->setHelperBlock($button);
		return $this->_addField($param,$fieldset);
	}
    public function getMainFieldset($fieldset)
    {
        if ($this->_getData('main_fieldset') instanceof Varien_Data_Form_Element_Fieldset) {
            return $this->_getData('main_fieldset');
        }
        $this->setData('main_fieldset', $fieldset);
        return $fieldset;
    }	
    public function _addField($parameter,$fieldset)
    {
        $form = $this->getForm();
        $fieldset = $this->getMainFieldset($fieldset); //$form->getElement('options_fieldset');

        // prepare element data with values (either from request of from default values)
        $fieldName = $parameter->getKey();
        $data = array(
            //'name'      => $form->addSuffixToName($fieldName, 'parameters'),
			'name'      => $fieldName,
            'label'     => Mage::helper('megamenu')->__($parameter->getLabel()),
            // 'required'  => $parameter->getRequired(),
            'class'     => 'widget-option '.$fieldName,
            'note'      => Mage::helper('megamenu')->__($parameter->getDescription()),
        );
		
        if ($values = $this->getWidgetValues()) {
            $data['value'] = (isset($values[$fieldName]) ? $values[$fieldName] : '');
        }
        else {
            $data['value'] = $parameter->getValue();
            //prepare unique id value
            if ($fieldName == 'unique_id' && $data['value'] == '') {
                // $data['value'] = md5(microtime(1));
				$data['value'] = microtime(1);
            }
        }

        // prepare element dropdown values
        if ($values  = $parameter->getValues()) {
            // dropdown options are specified in configuration
            $data['values'] = array();
            foreach ($values as $option) {
                $data['values'][] = array(
                    'label' => Mage::helper('megamenu')->__($option['label']),
                    'value' => $option['value']
                );
            }
        }
        // otherwise, a source model is specified
        elseif ($sourceModel = $parameter->getSourceModel()) {
            $data['values'] = Mage::getModel($sourceModel)->toOptionArray();
        }

        // prepare field type or renderer
        $fieldRenderer = null;
        $fieldType = $parameter->getType();
        // hidden element
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
        }
        // just an element renderer
        elseif (false !== strpos($fieldType, '/')) {
            $fieldRenderer = $this->getLayout()->createBlock($fieldType);
            $fieldType = $this->_defaultElementType;
        }

        // instantiate field and render html
        // $field = $fieldset->addField($this->getMainFieldsetHtmlId() . '_' . $fieldName, $fieldType, $data);
		$field = $fieldset->addField($fieldName, $fieldType, $data);
        if ($fieldRenderer) {
            $field->setRenderer($fieldRenderer);
        }

        // extra html preparations
        if ($helper = $parameter->getHelperBlock()) {
			Mage::register('megamenu_adminhtml_widget_chooser',1);	// cho phep block Sm_Megamenu_Block_Adminhtml_Widget_Chooser check widget available for megamenu
            $helperBlock = $this->getLayout()->createBlock($helper->getType(), '', $helper->getData());
            if ($helperBlock instanceof Varien_Object) {
                $helperBlock->setConfig($helper->getData())
                    ->setFieldsetId($fieldset->getId())
                    ->setTranslationHelper(Mage::helper('megamenu'))
                    ->prepareElementHtml($field);
            }
		}

        // dependencies from other fields
        // $dependenceBlock = $this->getChild('form_after');
        // $dependenceBlock->addFieldMap($field->getId(), $fieldName);
        // if ($parameter->getDepends()) {
            // foreach ($parameter->getDepends() as $from => $row) {
                // $values = isset($row['values']) ? array_values($row['values']) : (string)$row['value'];
                // $dependenceBlock->addFieldDependence($fieldName, $from, $values);
            // }
        // }
		
        return $field;
    }
	
	// $buttonsInsertImageHtml = $this->_getButtonHtml(
	// array(
	// 'title'     => Mage::helper('megamenu')->__('Insert Image...'),
	// 'onclick'   => "MediabrowserUtility.openDialog('" .
					// $url .
				   // "target_element_id/" . $form->getHtmlIdPrefix().$icon->getId() . "/" .
					// ((null !== $storeId)
						// ? ('store/' . $storeId . '/')
						// : '')
				   // . "')",
	// 'class'     => 'add-image plugin',
	// 'style'     => $visible ? '' : 'display:none',
	// ));	
		// //initialize object param and object button 
		// $param = new Varien_Object();
		// $button = new Varien_Object();		
		// $param->setVisible(1);
		// $param->setRequired(1);
		// $param->setType('Label');
		// $param->setSortOrder(10);
		// $param->setValues(array());
		
		// //initialize for field Select product
		// $param->setLabel('Product');
		// $button->setButton(array('open'=>'Select Product...'));
		// $button->setType('adminhtml/catalog_product_widget_chooser');
		// $param->setHelperBlock($button);
		// $param->setKey('product_id');
		// $this->_addField($param,$fieldset);//add field Select product
		
		// //initialize for field Select Category
		// $param->setLabel('Category');
		// $button->setButton(array('open'=>'Select Category...'));
		// $button->setType('adminhtml/catalog_category_widget_chooser');
		// $param->setHelperBlock($button);
		// $param->setKey('category_id');
		// $this->_addField($param,$fieldset);	

		// $param->setLabel('CMS Page');
		// $button->setButton(array('open'=>'Select Page...'));
		// $button->setType('adminhtml/cms_page_widget_chooser');
		// $param->setHelperBlock($button);
		// $param->setKey('page_id');
		// $this->_addField($param,$fieldset);		
		
		// $param->setLabel('CMS Block');
		// $button->setButton(array('open'=>'Select Block...'));
		// $button->setType('adminhtml/cms_block_widget_chooser');
		// $param->setHelperBlock($button);
		// $param->setKey('block_id');
		// $this->_addField($param,$fieldset);	
    protected function _getButtonHtml($data)
    {
        $html = '<button type="button"';
        $html.= ' class="scalable '.(isset($data['class']) ? $data['class'] : '').'"';
        $html.= isset($data['onclick']) ? ' onclick="'.$data['onclick'].'"' : '';
        $html.= isset($data['style']) ? ' style="'.$data['style'].'"' : '';
        $html.= isset($data['id']) ? ' id="'.$data['id'].'"' : '';
        $html.= '>';
        $html.= isset($data['title']) ? '<span>'.$data['title'].'</span>' : '';
        $html.= '</button>';

        return $html;
    }	
	
}			