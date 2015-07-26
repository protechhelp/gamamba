<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Helper_Rewriter extends Aitoc_Aitsys_Helper_Data
{
    public function getOrderConfig()
    {
        $order = unserialize( (string) Mage::getConfig()->getNode('default/aitsys_rewriter_classorder') );
        if (!$order) {
            $order = array();
        }
        return $order;
    }
    
    public function saveOrderConfig($order)
    {
        Mage::getConfig()->saveConfig('aitsys_rewriter_classorder', serialize($order));
        Mage::app()->cleanCache();
    }
    
    public function mergeOrderConfig($order)
    {
        $currentOrder = unserialize( (string) Mage::getConfig()->getNode('default/aitsys_rewriter_classorder') );
        if (!$currentOrder)
        {
            $newOrder = $order;
        } else
        {
            $newOrder = array_merge($currentOrder, $order);
        }
        $this->saveOrderConfig($newOrder);
    }
    
    public function removeOrderConfig()
    {
        Mage::getConfig()->deleteConfig('aitsys_rewriter_classorder');
        Mage::app()->cleanCache();
    }
    
    public function saveExcludeClassesConfig($classes)
    {
        $classes = array_map('trim', preg_split("/[\n,]+/", $classes));
        Mage::getConfig()->saveConfig('aitsys_rewriter_exclude_classes', serialize($classes));
        Mage::app()->cleanCache();
    }
    
    public function getExcludeClassesConfig()
    {
        $configValue = (string) Mage::getConfig()->getNode('default/aitsys_rewriter_exclude_classes');
        $configValue = $this->tool()->unserialize($configValue);
        if (!$configValue) {
            $configValue = array();
        }
        return $configValue;
    }
    
    public function validateSavedClassConfig($savedClassConfig, $rewriteClasses)
    {
        if(!is_array($savedClassConfig) || !is_array($rewriteClasses))
        {
            return false;
        }
        
        $savedClasses = array_keys($savedClassConfig);
        
        if(count($rewriteClasses)!=count($savedClasses))
        {
                return false;
        }
        
        $diff1 = array_diff($rewriteClasses, $savedClasses);
        $diff2 = array_diff($savedClasses, $rewriteClasses);
        
        if(!empty($diff1) || !empty($diff2))
        {
                return false;
        }
        
        
        return true;        
    }

    /**
     * @return string
     */
    public function getRewriterStatus()
    {
        return Mage::getStoreConfig('aitsys/rewriter_status');
    }

    /**
     * Found all Inherited Classes and run method for analysis
     *
     * @param $object
     * @param $method
     * @param array $params
     * @param bool $useOrdering
     * @return bool
     */
    public function analysisInheritedClasses($object, $method, &$params = array(), $useOrdering = true)
    {
        $rewriterConflictModel = new Aitoc_Aitsys_Model_Rewriter_Conflict();
        $conflicts = $rewriterConflictModel->getConflictList();

        // will combine rewrites by alias groups
        if (!empty($conflicts)) {
            $rewriterClassModel = new Aitoc_Aitsys_Model_Rewriter_Class();
            $rewriterInheritanceModel = new Aitoc_Aitsys_Model_Rewriter_Inheritance();
            $result = false;
            foreach($conflicts as $groupType => $modules) {
                $groupType = substr($groupType, 0, -1);
                foreach($modules as $moduleName => $moduleRewrites) {
                    foreach($moduleRewrites['rewrite'] as $moduleClass => $rewriteClasses) {
                        // building inheritance tree
                        $alias              = $moduleName . '/' . $moduleClass;
                        $baseClass          = $rewriterClassModel->getBaseClass($groupType, $alias);
                        $inheritedClasses   = $rewriterInheritanceModel->build($rewriteClasses, $baseClass, $useOrdering);
                        if ($inheritedClasses == false) {
                            continue;
                        }
                        $paramsMethod = array(
                            'rewriteClasses'    => $rewriteClasses,
                            'alias'             => $alias,
                            'baseClass'         => $baseClass,
                            'inheritedClasses'  => $inheritedClasses,
                            'params'            => &$params
                        );
                        $result = $object->$method($paramsMethod)?true:$result;
                    }
                }
            }
            return $result;
        }

        return false;
    }

}
