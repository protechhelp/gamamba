<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Block_Rewriter_List extends Aitoc_Aitsys_Abstract_Adminhtml_Block
{
    /**
     * @var array
     */
    protected $_extensions = array();

    /**
     * @var array
     */
    protected $_groups = array();
    protected $_groupsBase = array();

    protected $_statuses = array(0=>'Disabled', 1=>'Enabled' );
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTitle('Rewrites Manager');
        
        $this->_prepareConflictGroups();
    }
    
    protected function _prepareLayout()
    {
        if ($this->getRewriterStatus()) {
            $this->setChild('change_status_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Disable'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/disable') . '\')',
                    'class'     => 'save'
                )));
        } else {
            $this->setChild('change_status_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Enable'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/enable') . '\')',
                    'class'     => 'save'
                )));
        }

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Save changes'),
                    'onclick'   => '$(\'rewritesForm\').submit()',
                    'class' => 'save',
                ))
        );
        
        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('aitsys')->__('Reset rewrites order to default values'),
                    'onclick'   => 'if (confirm(\'' . Mage::helper('aitsys')->__('Are you sure want to reset rewrites order?') . '\')) $(\'rewritesResetForm\').submit()',
                    'class' => 'cancel',
                ))
        );
        
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
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * @return string
     */
    public function getChangeStatusButton()
    {
        return $this->getChildHtml('change_status_button');
    }

    /**
     * @param $paramsMethod
     * @return bool
     */
    public function addClassesInGroupArray(&$paramsMethod)
    {
        if(!empty($paramsMethod['inheritedClasses']['_baseClasses']))
        {
            $this->_groupsBase[$paramsMethod['baseClass']] = $paramsMethod['inheritedClasses']['_baseClasses'];
            unset($paramsMethod['inheritedClasses']['_baseClasses']);
        }
        $this->_groups[$paramsMethod['baseClass']] = array_keys($paramsMethod['inheritedClasses']);
        rsort($this->_groups[$paramsMethod['baseClass']]);
        $this->_groups[$paramsMethod['baseClass']] = array_values($this->_groups[$paramsMethod['baseClass']]);

        return true;
    }

    /**
     * Retrieve conflicts data
     */
    protected function _prepareConflictGroups()
    {
        $allExtensions    = array();
        $currentExtension = Mage::app()->getRequest()->getParam('extension');


        $rewriteHelper = Mage::helper('aitsys/rewriter');
        $result = array();
        if ($rewriteHelper->analysisInheritedClasses($this, 'addClassesInGroupArray', $result, false))
        {
            $order = Mage::helper('aitsys/rewriter')->getOrderConfig();
            $groups = $this->_groups;
            foreach ($groups as $baseClass => $group) {
                $groups[$baseClass] = array_flip($group);
                $isCurrentFound = !(bool)$currentExtension;
                $savedRewritesValid = Mage::helper('aitsys/rewriter')->validateSavedClassConfig(
                    (isset($order[$baseClass]) ? $order[$baseClass] : array()),
                    array_keys($groups[$baseClass])
                );

                foreach ($groups[$baseClass] as $class => $i) {
                    if (isset($order[$baseClass][$class]) && $savedRewritesValid) {
                        $groups[$baseClass][$class] = $order[$baseClass][$class];
                    }
                    
                    // adding class to the list of all extensions
                    $key = substr($class, 0, strpos($class, '_', 1 + strpos($class, '_')));
                    //                                           ^^^^^^^^^^^^^^^^^^^^^  --- this is offset, so start searching second "_"
                    $allExtensions[] = $key;
                    if ($key == $currentExtension) {
                        $isCurrentFound = true;
                    }
                }
                
                $groups[$baseClass] = array_flip($groups[$baseClass]);
                ksort($groups[$baseClass]);
                if (!$isCurrentFound || in_array($baseClass, Mage::helper('aitsys/rewriter')->getExcludeClassesConfig())) {
                    // will display conflicts only for groups where current selected extension presents
                    // exclude conflicts for excluded base Magento classes
                    unset($groups[$baseClass]);
                }
            }
        }
        
        $aModuleList   = $this->tool()->platform()->getModules();
        $allExtensions = array_unique($allExtensions);
        foreach ($allExtensions as $key) {
            $moduleName = $key;
            foreach ($aModuleList as $moduleItem) {
                if ($key == $moduleItem->getKey()) {
                    $moduleName = (string)$moduleItem->getLabel();
                }
            }
            $this->_extensions[$this->getExtensionUrl($key)] = $moduleName;
        }

        if(!empty($groups))
        {
            $this->_groups = $groups;
        }
    }
    
    /**
     * @return array
     */
    public function getConflictGroups()
    {
        return $this->_groups;
    }
    
    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->_extensions;
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
    public function getResetUrl()
    {
        return $this->getUrl('*/*/reset', array('_current'=>true));
    }
    
    /**
     * @return string
     */
    public function getSelfUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true));
    }
    
    /**
     * @param string $extension
     * 
     * @return string
     */
    public function getExtensionUrl($extension)
    {
        if ($extension) {
            return $this->getUrl('*/*/*', array('extension' => $extension));
        }
        return $this->getUrl('*/*/*');
    }
    
    /**
     * @return string
     */
    public function getExcludedClasses()
    {
        $classes = Mage::helper('aitsys/rewriter')->getExcludeClassesConfig();
        $classes = implode("\n", $classes);
        return $classes;
    }

    /**
     * @return string
     */
    public function getRewriterStatus()
    {
        return Mage::helper('aitsys/rewriter')->getRewriterStatus();
    }

    /**
     * @return string
     */
    public function getRewriterCacheStatus()
    {
        if($this->getRewriterStatus())
        {
            return (int) Mage::app()->useCache('aitsys');
        }
        return 0;
    }

    public function getStatusLabel($id)
    {
        return $this->_statuses[$id];
    }

    protected function _getBaseClass($class, $classBaseEcho)
    {
        if(empty($this->_groupsBase[$class]))
        {
            return false;
        }

        if(empty($classBaseEcho))
        {
            return $this->_groupsBase[$class]['__topClass'];
        }

        return empty($this->_groupsBase[$class][$classBaseEcho])?false:$this->_groupsBase[$class][$classBaseEcho];
    }
}
