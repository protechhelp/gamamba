<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Module_Info_Factory
{
    /**
     * @var array
     */
    protected static $_sources = array(
        'Package', // package info file
        'Config',  // main config.xml file 
        'Fallback' // this model should be used if no other sources of info have been found
    );
    
    /**
     * @param Aitoc_Aitsys_Model_Module $module Module entity
     * @param string $source Source type
     * @param string $codepool Module codepool. Default: local
     * 
     * @return Aitoc_Aitsys_Model_Module_Info_Abstract
     * @throws Aitoc_Aitsys_Model_Module_Info_Exception
     */
    public static function getModuleInfoFromSource(Aitoc_Aitsys_Model_Module $module, $source, $codepool = Aitoc_Aitsys_Model_Module::DEFAULT_CODEPOOL)
    {
        if (!in_array($source, self::$_sources)) {
            throw new Aitoc_Aitsys_Model_Module_Info_Exception ('Incorrect module info source type. Module: ' . $moduleKey . '. Source: ' . $source);
        }
        $class = 'Aitoc_Aitsys_Model_Module_Info_'.$source;
        return new $class($module, $codepool);
    }
    
    /**
     * Get module info from the first available source
     * 
     * @param Aitoc_Aitsys_Model_Module $module Module entity
     * @param string $codepool Module codepool. Default: local
     * 
     * @return Aitoc_Aitsys_Model_Module_Info_Abstract
     * @throws Aitoc_Aitsys_Model_Module_Info_Exception
     */
    public static function getModuleInfo(Aitoc_Aitsys_Model_Module $module, $codepool = Aitoc_Aitsys_Model_Module::DEFAULT_CODEPOOL)
    {
        foreach (self::$_sources as $source) {
            $info = self::getModuleInfoFromSource($module, $source, $codepool);
            if ($info->isLoaded()) {
                return $info;
            }
        }
        throw new Aitoc_Aitsys_Model_Module_Info_Exception ('No module info sources available. Module: ' . $moduleKey);
    }
}