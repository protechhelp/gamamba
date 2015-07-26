<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 * @author Andrei
 */
class Aitoc_Aitsys_Model_Aitpatch_Observer
{
    /**
     * replace design to default path
     *
     * @param $observer
     */
    public function replaceDesignPath($observer)
    {
        if(($block = $observer->getBlock()) instanceof Mage_Core_Block_Template)
        {
            //return design path for block
            if(Mage::registry('design_path_for_'.get_class($block).'_'.$block->getId()))
            {
                Mage::getConfig()->getOptions()->setData('design_dir', Mage::registry('design_path_for_'.get_class($block).'_'.$block->getId()));
            }
            elseif(Mage::registry('design_path_aitoc') && Mage::registry('design_path_aitoc') != Mage::getBaseDir('design'))
            {
                Mage::getConfig()->getOptions()->setData('design_dir', Mage::registry('design_path_aitoc'));
            }
        }
    }

    /**
     * @param $observer
     */
    public function pathTemplateReplaceBeforeToHtml($observer)
    {
        if(($block = $observer->getBlock()) instanceof Mage_Core_Block_Template)
        {
            if(!Mage::registry('design_path_aitoc'))
            {
                $currentViewDirAll = Mage::getBaseDir('design');
                Mage::register('design_path_aitoc', $currentViewDirAll);
            }
            $currentViewDir = Mage::registry('design_path_aitoc');
            $fileName = $block->getTemplateFile();
            //save base dir before this block
            if(Mage::registry('design_path_for_'.get_class($block).'_'.$block->getId()))
            {
                Mage::unregister('design_path_for_'.get_class($block).'_'.$block->getId());
            }
            Mage::register('design_path_for_'.get_class($block).'_'.$block->getId(), Mage::getBaseDir('design'));
            if (false !== strpos($fileName, 'aitcommonfiles'))
            {
                if (Mage::getStoreConfigFlag('aitsys/settings/use_dynamic_patches')
                    || !file_exists($currentViewDir . DS . $fileName)) // if there is a file in app/, we should do nothing. will use it.
                {
                    $newViewDir = Aitoc_Aitsys_Model_Aitpatch::getPatchesCacheDir() . 'design';
                    if (file_exists($newViewDir . DS . $fileName))
                    {
                        $currentViewDir = $newViewDir; // replacing view dir
                    }
                    else
                    {
                        // also trying with 'default' folder instead of 'base' (for compatibility with 1.3 and 1.4 in one version)
                        $fileNameDef = str_replace(DS . 'base' . DS, DS . 'default' . DS, $fileName);
                        if (file_exists($newViewDir . DS . $fileNameDef))
                        {
                            $currentViewDir = $newViewDir; // replacing view dir
                            $fileName = $fileNameDef; // forcing use 'default' instead of 'base'
                        }
                    }
                }
            }
            $block->setTemplateFile($fileName);
            Mage::getConfig()->getOptions()->setData('design_dir', $currentViewDir);
        }
        else
        {
            if(Mage::registry('design_path_aitoc') && Mage::registry('design_path_aitoc') != Mage::getBaseDir('design'))
            {
                Mage::getConfig()->getOptions()->setData('design_dir', Mage::registry('design_path_aitoc'));
            }
        }
    }
}