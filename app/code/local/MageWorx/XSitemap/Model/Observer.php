<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @author     MageWorx Dev Team
 */
class MageWorx_XSitemap_Model_Observer
{
    const XML_PATH_GENERATION_ENABLED = 'mageworx_seo/google_sitemap/enabled';
    const XML_PATH_CRON_EXPR = 'crontab/jobs/generate_sitemaps/schedule/cron_expr';
    const XML_PATH_ERROR_TEMPLATE  = 'mageworx_seo/google_sitemap/error_email_template';
    const XML_PATH_ERROR_IDENTITY  = 'mageworx_seo/google_sitemap/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT = 'mageworx_seo/google_sitemap/error_email';
    const PROCESS_ID = 'xsitemap';
    public $indexProcess;

    public function __construct()
    {
        $this->indexProcess = Mage::getModel('index/process');//new Mage_Index_Model_Process();
        $this->indexProcess->setId(self::PROCESS_ID);
    }
    
    public function unlock() {
        $this->indexProcess->unlock();
    }
    
    public function scheduledGenerateSitemaps($schedule)
    {
        if ($this->indexProcess->isLocked())
        {
            return;
        }
        // Set an exclusive lock.
        $this->indexProcess->lockAndBlock();
        register_shutdown_function(array($this, "unlock")); 
        $errors = array();

        if (!Mage::getStoreConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }
		$steps = array('category', 'product', 'tag', 'cms', 'additional_links', 'blog', 'sitemap_finish');
        $collection = Mage::getModel('xsitemap/sitemap')->getCollection();
	    /* @var $collection Mage_Sitemap_Model_Mysql4_Sitemap_Collection */
        foreach ($collection as $sitemap) {
            /* @var $sitemap Mage_Sitemap_Model_Sitemap */

            try {
                foreach ($steps as $step) {
                $sitemap->generateXml($step);
		  	    	while ($sitemap->_currentInc<$sitemap->_totalProducts) {
					 	$sitemap->generateXml($step);
						sleep(1);
					}
				}
            }
            catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /* @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $errors))
                );

            $translate->setTranslateInline(true);
        }
    }
}