<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Block_Edit extends Mage_Adminhtml_Block_Widget
{
    /**
     * @var bool
     */
    protected $_allowInstall = true;
    
    /**
     * @var array
     */
    protected $_errorList = array();
    
    public function _construct()
    {
        $aitsysModel = new Aitoc_Aitsys_Model_Aitsys(); 
        $this->_errorList = $aitsysModel->getAllowInstallErrors();
        $this->setTitle('Aitoc Modules Manager v%s');
    }
    
    /**
     * Get current module version
     * 
     * @return string
     */
    public function getVersion()
    {
        return Aitoc_Aitsys_Model_Platform::getInstance()->getVersion();
    }
    
    /**
     * Get new permissions link param
     * 
     * @return string
     */
    public function getPermLink()
    {
        $mode = $this->tool()->filesystem()->getPermissionsMode();
        
        if ($mode === Aitoc_Aitsys_Model_Core_Filesystem::MODE_ALL) {
            return Aitoc_Aitsys_Model_Core_Filesystem::MODE_NORMAL;
        } else {
            return Aitoc_Aitsys_Model_Core_Filesystem::MODE_ALL;
        }
    }

    /**
     * Get new permissions link description
     * 
     * @return string
     */
    public function getPermLinkTitle()
    {
        $mode = $this->tool()->filesystem()->getPermissionsMode();

        if ($mode === Aitoc_Aitsys_Model_Core_Filesystem::MODE_ALL) {
            return 'Grant restricted write permissions';
        } else {
            return 'Grant full write permissions';
        }
    }

    protected function _prepareLayout()
    {
        if ($this->_allowInstall) {
            $this->setChild('save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('aitsys')->__('Save modules settings'),
                        'onclick'   => 'configForm.submit()',
                        'class' => 'save',
                    ))
            );

            $this->setChild('form',
                $this->getLayout()->createBlock('aitsys/form')
                    ->initForm()
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    /**
     * @return string
     */
    public function getInstallText()
    {
        $installText = '';
        if (!empty($this->_errorList)) {
            $installText .= '<ul class="messages"  style="width:50%">';
            
            foreach ($this->_errorList as $errorMsg) {
                $installText .= '<li class="'.$errorMsg['type'].'"><ul><li>'.$errorMsg['message'] . '<br /></li></ul></li>';
            }
            $installText .= '</ul>';
        }
        
        return $installText;
    }
    
    /**
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function tool()
    {
        return Aitoc_Aitsys_Abstract_Service::get($this);
    }
}