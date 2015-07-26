<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Block_Patch_View extends Mage_Adminhtml_Block_Abstract
{
    /**
     * @var array
     */
    protected $_collectedFiles = array();
    
    /**
     * @var integer
     */
    protected $_filesNumber = 0;

    protected function _construct()
    {
        $pathes    = array();
        $designDir = Mage::getBaseDir('design');
        $source    = array(
            'admin' => $designDir . DS . 'adminhtml' . DS,
            'front' => $designDir . DS . 'frontend'  . DS
        );
        
        // design area level
        foreach ($source as $type => $src) {
            $paths = glob($src . '*');
            if ($paths) {
                // package level
                foreach ($paths as $path) {
                    $package = pathinfo($path, PATHINFO_FILENAME);
                    $paths   = glob($path . DS . '*');
                    if ($paths) {
                        // theme level
                        foreach ($paths as $path) {
                            $theme = pathinfo($path, PATHINFO_FILENAME);
                            $tmp   = $path . DS . 'template' . DS . 'aitcommonfiles' . DS;
                            if (!isset($pathes[$type][$package][$theme])) {
                                $pathes[$type][$package][$theme] = array();
                            }
                            $tmps = glob($tmp . '*');
                            if ($tmps) {
                                // customized templates found
                                foreach ($tmps as $file) {
                                    $pathes[$type][$package][$theme][] = $file;
                                    $this->_filesNumber++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->_collectedFiles = $pathes;
    }
    
    /**
     * @return string
     */
    protected function _getPatchesDir()
    {
        return Aitoc_Aitsys_Model_Platform::getInstance()->getVarPath() . Aitoc_Aitsys_Model_Aitpatch::PATCH_DIR;
    }
    
    /**
     * @return array
     */
    public function getAitcommonThemes()
    {
        return $this->_collectedFiles;
    }
    
    /**
     * @return integer
     */
    public function getFilesNumber()
    {
        return $this->_filesNumber;
    }
}