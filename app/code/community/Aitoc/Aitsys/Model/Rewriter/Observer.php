<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 * @author Andrei
 */
class Aitoc_Aitsys_Model_Rewriter_Observer
{
    public function init($observer)
    {try{
        Aitoc_Aitsys_Model_Rewriter_Autoload::register();
        }catch(Exception $e) { Mage::log((string)$e, false, 'rewriter.log', true);}
    }
    
    public function clearCache($observer)
    {
        //not clear if rewriter is disabled
        if(Mage::getConfig()->getNode('default/aitsys/rewriter_status') != 1){
            return false;
        }
        // this part for flush magento cache
        $tags = $observer->getTags();
        $rewriter = new Aitoc_Aitsys_Model_Rewriter();
        if (null !== $tags) {
            if (empty($tags) || !is_array($tags) || in_array('aitsys', $tags)) {
                return $rewriter->prepare();
            }
        }
        
        // this part for mass refresh
        $cacheTypes = Mage::app()->getRequest()->getParam('types');
        if ($cacheTypes) {
            $cacheTypesArray = $cacheTypes;
            if (!is_array($cacheTypesArray)) {
                $cacheTypesArray = array($cacheTypesArray);
            }
            if (in_array('aitsys', $cacheTypesArray)) {
                return $rewriter->prepare();
            }
        }
        
        // this part is for flush cache storage
        if (null === $cacheTypes && null === $tags) {
            return $rewriter->prepare();
        }
    }

    public function hideCacheOnPage($observer)
    {
        if(($block = $observer->getBlock()) instanceof Mage_Adminhtml_Block_Cache_Grid && !Mage::helper('aitsys/rewriter')->getRewriterStatus())
        {
            $config = Mage::getConfig()->getNode('global/cache/types');
            if ($config) {
                foreach ($config->children() as $type=>$node) {
                    if($type == 'aitsys'){
                        $dom=dom_import_simplexml($node);
                        $dom->parentNode->removeChild($dom);
                    }
                }
            }
        }
    }
}