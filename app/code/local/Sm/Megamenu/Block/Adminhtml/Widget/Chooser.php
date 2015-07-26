<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Block_Adminhtml_Widget_Chooser extends Mage_Adminhtml_Block_Template
{
    /**
     * Chooser source URL getter
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->_getData('source_url');
    }

    /**
     * Chooser form element getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_getData('element');
    }

    /**
     * Convert Array config to Object
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if ($this->_getData('config') instanceof Varien_Object) {
            return $this->_getData('config');
        }

        $configArray = $this->_getData('config');
        $config = new Varien_Object();
        $this->setConfig($config);
        if (!is_array($configArray)) {
            return $this->_getData('config');
        }

        // define chooser label
        if (isset($configArray['label'])) {
            $config->setData('label', $this->getTranslationHelper()->__($configArray['label']));
        }

        // chooser control buttons
        $buttons = array(
            'open'  => Mage::helper('widget')->__('Choose...'),
            'close' => Mage::helper('widget')->__('Close')
        );
        if (isset($configArray['button']) && is_array($configArray['button'])) {
            foreach ($configArray['button'] as $id => $label) {
                $buttons[$id] = $this->getTranslationHelper()->__($label);
            }
        }
        $config->setButtons($buttons);

        return $this->_getData('config');
    }

    /**
     * Helper getter for translations
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getTranslationHelper()
    {
        if ($this->_getData('translation_helper') instanceof Mage_Core_Helper_Abstract) {
            return $this->_getData('translation_helper');
        }
        return $this->helper('widget');
    }

    /**
     * Unique identifier for block that uses Chooser
     *
     * @return string
     */
    public function getUniqId()
    {
        return $this->_getData('uniq_id');
    }

    /**
     * Form element fieldset id getter for working with form in chooser
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return $this->_getData('fieldset_id');
    }

    /**
     * Flag to indicate include hidden field before chooser or not
     *
     * @return bool
     */
    public function getHiddenEnabled()
    {
        return $this->hasData('hidden_enabled') ? (bool)$this->_getData('hidden_enabled') : true;
    }

    /**
     * Return chooser HTML and init scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
		if(is_null(Mage::registry('megamenu_adminhtml_widget_chooser'))){
			return parent::_toHtml();
		}
		//unregister
		Mage::unregister('megamenu_adminhtml_widget_chooser');
        $element   = $this->getElement();
		$htmlIdPrefix = $element->getForm()->getHtmlIdPrefix();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        // $fieldset  = $element->getForm()->getElement($this->getFieldsetId());// getForm >> getFieldset >> field
        $chooserId = $this->getUniqId();	// echo $chooserId;die;	//options_fieldset_megamenu_sm_megamenu_product_id44c9ff3c66b2985cea68c24a0e23ef5b
		// $sourceUrl chua' megamenu gay loi khi ajax	 :http://127.0.0.1/mage1510/index.php/*megamenu*/catalog_product_widget/chooser/uniq_id/options_fieldset_megamenu_id_pathc0244322fc3e6acc4edb
		$admin = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
		$SourceUrl = str_replace("/megamenu/","/$admin/",$this->getSourceUrl());
		$SourceUrl = str_replace("/uniq_id/","/uniq_id/".$htmlIdPrefix,$SourceUrl);
		$this->setSourceUrl($SourceUrl);
	
        $config    = $this->getConfig();

        // add chooser element to fieldset
        // $chooser = $fieldset->addField('chooser' . $element->getId(), 'note', array(
            // 'label'       => $config->getLabel() ? $config->getLabel() : '',
            // 'value_class' => 'value2',
        // ));

        $hiddenHtml = '';
        if ($this->getHiddenEnabled()) {
            $hidden = new Varien_Data_Form_Element_Hidden($element->getData());
            $hidden->setId("{$chooserId}value")->setForm($element->getForm());
            if ($element->getRequired()) {
                $hidden->addClass('required-entry');
            }
            $hiddenHtml = $hidden->getElementHtml();
            $element->setValue('');
        }
		// $config->getData() ;// array(1) {    ["buttons"] => array(2) {      ["open"] => string(17) "Select Product..."      ["close"] => string(5) "Close"    }  }

        $configJson = Mage::helper('core')->jsonEncode($config->getData());
		$js= '
            <script type="text/javascript">
                '.$htmlIdPrefix.$chooserId.' = new WysiwygWidget.chooser("'.$htmlIdPrefix.$chooserId.'", "'.$this->getSourceUrl().'", '.$configJson.');
                // $("'.$chooserId.'value").advaiceContainer = "'.$chooserId.'advice-container";
            </script>
        ';
        $buttons = $config->getButtons();

        $chooseButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setId($htmlIdPrefix.$chooserId . 'control')
            ->setClass('btn-chooser')
            ->setLabel($buttons['open'])
            ->setOnclick($htmlIdPrefix.$chooserId.'.choose();$$(\'.'.$hidden->getName().'\')[0].id=\'\';$$(\'.data_type\')[0].id=\''.$htmlIdPrefix.$chooserId.'value\';');
			// ->setOnclick($htmlIdPrefix.$chooserId.'.choose();$$(\'.'.$hidden->getName().'\')[0].name=$$(\'.data_type\')[0].name;');
        // $chooser->setData('after_element_html', $hiddenHtml . $chooseButton->toHtml().$js);


        // render label and chooser scripts
        $configJson = Mage::helper('core')->jsonEncode($config->getData());
        return '<div id="'.$htmlIdPrefix.'box_'.$chooserId.'">
            <label style="background-color: #EEE2BE; float: left; font-size: 15px; height: 21px; width: 46%; margin-right:3px; overflow:hidden;" class="widget-option-label" id="'.$htmlIdPrefix.$chooserId . 'label">'.($this->getLabel() ? $this->getLabel() : Mage::helper('widget')->__('Not Selected')).'</label>
            <div id="'.$htmlIdPrefix.$chooserId . 'advice-container" class="hidden"></div>
        '.$hiddenHtml . $chooseButton->toHtml().$js.
		'</div>';
    }
}
