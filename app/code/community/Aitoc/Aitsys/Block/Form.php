<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Block_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @var Aitoc_Aitsys_Block_Form_Element_Renderer
     */
    protected $_elementRenderer;
    
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function initForm()
    {   
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('module_list', array(
            'legend' => Mage::helper('aitsys')->__('AITOC Modules')
        ));
        $fieldset->getRenderer()->setTemplate('aitcore/fieldset.phtml');

        $aitsysModel = new Aitoc_Aitsys_Model_Aitsys();
        $modulesList = $aitsysModel->getAitocModuleList();
        
        $this->_elementRenderer = $this->getLayout()->createBlock('aitsys/form_element_renderer');

        if ($modulesList) {
            foreach ($modulesList as $module) {
                $this->_addModule($module, $fieldset);
            }
        }

        $this->setForm($form);

        return $this;
    }
    
    /**
     * @param Aitoc_Aitsys_Model_Module $module
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    protected function _addModule(Aitoc_Aitsys_Model_Module $module, Varien_Data_Form_Element_Fieldset $fieldset)
    {
        if($module->isIgnore())
        {
            return false;
        }

        $aModule = $module;
        $label = $module->getInfo()->getLabel().($module->getInfo()->getVersion()?' v'.$module->getInfo()->getVersion():'');
        $message = '';
        $messageType = 'notice-msg';
        $isDemo = false;
        
        if ($this->tool()->platform()->hasDemoMode()) {
            $xml = simplexml_load_file(Mage::getBaseDir()."/aitmodules.xml");
            $link = (string) $xml->modules->$aModule['key'];
            if ($link == '') {
                $link = $this->tool()->getAitocUrl();
            }
            $message = Mage::helper('aitsys')->__("The extension is already enabled on this Demo Magento installation and can't be disabled for security reasons. Please proceed to the next step outlined in the extension's <a href='%s' target='_blank'>User Manual</a> to see how it works.", $link);
            $isDemo = true;
        } elseif (defined('COMPILER_INCLUDE_PATH')) {
            $compilerUrl = version_compare(Mage::getVersion(), '1.5.0.0', '>=') ? Mage::helper('adminhtml')->getUrl('adminhtml/compiler_process/index/') : Mage::helper('adminhtml')->getUrl('compiler/process/index/');
            $message = Mage::helper('aitsys')->__('Before activating or deactivating the extension please turn off the compiler at <br /><a href="%s">System > Tools > Compilation</a>', $compilerUrl);
            $messageType = 'warning-msg';
        } elseif(!$module->getInfo()->isMagentoCompatible()) {
            $message = Mage::helper('aitsys/strings')->getString( 'ER_ENT_HASH' );
        } elseif(!$module->getAccess()) {
            $message = Mage::helper('aitsys')->__('File does not have write permissions: %s', $aModule['file']);
            $messageType = 'error-msg';
        }

        if($module->getInfo()->getSerial())
        {
            $info = 'S/N: '.$module->getInfo()->getSerial();
        }

        if($aModule['key'] == 'Aitoc_Common')
        {
            $info = 'Used by other AITOC\'s modules. Do not disable.';
        }
        if ($message && $messageType != 'notice-msg' || $isDemo) {
            $field = $fieldset->addField('ignore_'.$aModule['key'], 'note', array(
                'name'  => 'ignore['.$aModule['key'].']',
                'label' => $label,
                'info'  => empty($info)?'':$info,
                'note'  => '<ul class="messages"><li class="'.$messageType.'"><ul><li>' . $message . '</li></ul></li></ul>',
                'module'  => $module
            ));
            if(!$isDemo) {
                $field->setRenderer($this->_elementRenderer);
            }
            return;
        }
        
        $fieldset->addField('hidden_enable_'.$aModule['key'], 'hidden', array(
            'name'  => 'enable['.$aModule['key'].']',
            'value' => 0,
        ));

        $fieldset->addField('enable_'.$aModule['key'], 'checkbox', array(
            'name'    => ($module->getAccess() ? 'enable' : 'ignore') . '['.$aModule['key'].']',
            'label'   => $label,
            'value'   => 1,
            'checked' => $aModule['value'],
            'note'  => $message?'<ul class="messages"><li class="'.$messageType.'"><ul><li>' . $message . '</li></ul></li></ul>':'',
            'info'  => empty($info)?'':$info,
            'module'  => $module
        ))->setRenderer($this->_elementRenderer);
    }
    
    /**
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function tool()
    {
        return Aitoc_Aitsys_Abstract_Service::get();
    }
 }