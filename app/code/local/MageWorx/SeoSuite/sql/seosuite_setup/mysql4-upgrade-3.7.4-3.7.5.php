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
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

$installer = $this;
/* @var $installer MageWorx_SeoSuite_Model_Resource_Eav_Mysql4_Setup */
/* @todo check on CE ver */
// If you using Magento EE uncomment next lines
/*
if($installer->getTable('enterprise_cms_page_revision')) {
    if (!$installer->getConnection()->tableColumnExists($installer->getTable('enterprise_cms_page_revision'), 'meta_title')) {
        $installer->getConnection()->addColumn($installer->getTable('enterprise_cms_page_revision'), 'meta_title', "varchar(255) NOT NULL DEFAULT ''");
    }
}
 */