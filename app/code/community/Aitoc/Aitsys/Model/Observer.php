<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Observer extends Aitoc_Aitsys_Abstract_Model
{
    /**
     * @var bool
     */
    protected $_correctionDone = false;
    
    /**
     * Corrects modules' status and launches install/uninstall scripts if some module was enabled/disabled through xml.
     */
    public function correction()
    {
        if (!$this->_correctionDone) {
            $this->tool()->platform()->getModules(); // load modules
            if ($this->tool()->platform()->isNeedCorrection()) {
                $aitsysModel = new Aitoc_Aitsys_Model_Aitsys(); 
                $aitsysModel->correction(); 
            }
            $this->_correctionDone = true;
        }
    }
    
    public function errorRender()
    {
        $this->tool()->platform()->renderAdminError(true);
    }

    /**
     * @param Varien_Object $observer
     */
    public function coreBlockAbstractToHtmlAfter($observer)
    {
        $this->compatibility($observer);
        Mage::getSingleton('aitsys/aitpatch_observer')->replaceDesignPath($observer);

    }

    /**
     * @param Varien_Object $observer
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        Mage::getSingleton('aitsys/rewriter_observer')->hideCacheOnPage($observer);
        Mage::getSingleton('aitsys/aitpatch_observer')->pathTemplateReplaceBeforeToHtml($observer);

    }

    /**
     * Replacement for an old abstract rewrite necessary for some extensions.
     * Compatible with Magento CE 1.4.1+
     * 
     * @param Varien_Object $observer
     */
    public function compatibility($observer)
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0', 'ge')) {
            Mage::dispatchEvent('aitsys_block_abstract_to_html_after', $observer->getData());
        }
    }

    /**
     * Update news from www.aitoc.com
     */
    public function updateNews()
    {
        $news = new Aitoc_Aitsys_Model_News_Recent();
        $news->loadData();
        
        $important = new Aitoc_Aitsys_Model_News_Important();
        $important->loadData();
    }

    public function updateNotification()
    {
        return Mage::getModel('aitsys/feed')->checkUpdate();
    }
}