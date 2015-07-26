<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 * @author Andrei
 */
class Aitoc_Aitsys_Model_Rewriter extends Aitoc_Aitsys_Model_Rewriter_Abstract
{
    /**
     * @var array
     */
    protected $_excludedClasses;

    /**
     * @var Aitoc_Aitsys_Model_Rewriter_Merge
     */
    protected $_merge;
    /**
     * @var Aitoc_Aitsys_Model_Rewriter_Config
     */
    protected $_rewriterConfig;
    
    /**
     * @return array
     */
    protected function _getExcludedClasses()
    {
        if (is_null($this->_excludedClasses)) {
            $this->_excludedClasses = $this->tool()->db()->getConfigValue('aitsys_rewriter_exclude_classes', array());
        }
        return $this->_excludedClasses;
    }

    public function preRegisterAutoloader()
    {
        $configFile = $this->_rewriteDir . 'config.php';
        /**
        * Will re-generate each time if config does not exist, or cache is disabled
        */
        if (!$this->tool()->isPhpCli()) {
            if (!file_exists($configFile) || !Mage::app()->useCache('aitsys')) {
                $this->prepare();
            }
        }
    }

    public function prepare()
    {
        $this->_merge = new Aitoc_Aitsys_Model_Rewriter_Merge();
        $this->_rewriterConfig = new Aitoc_Aitsys_Model_Rewriter_Config();

        // first clearing current class rewrites
        Aitoc_Aitsys_Model_Rewriter_Autoload::instance()->clearConfig();
        $this->_merge->clear();

        $rewriteHelper = Mage::helper('aitsys/rewriter');
        $result = array();
        if ($rewriteHelper->analysisInheritedClasses($this, 'mergeFilesFromClass', $result))
        {
            $this->_rewriterConfig->commit();
        }
        
        Aitoc_Aitsys_Model_Rewriter_Autoload::instance()->setupConfig();
    }

    /**
     * @param $paramsMethod
     * @return bool
     */
    public function mergeFilesFromClass(&$paramsMethod)
    {
        if (in_array($paramsMethod['baseClass'], $this->_getExcludedClasses())) {
            return false;
        }

        $mergedFilename = $this->_merge->merge($paramsMethod['inheritedClasses']);
        if ($mergedFilename) {
            $this->_rewriterConfig->add($mergedFilename, $paramsMethod['rewriteClasses']);
        }
        return true;
    }

}
