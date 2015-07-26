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
$installer->run("
INSERT INTO `{$this->getTable('seosuite_template')}` ( `template_code`, `template_name`, `comment`,`status`) VALUES
('category_meta_description', 'Category Meta Description', '<b>Template variables</b>\r\n[category],[website_name],[parent_category],[categories],[store_name],[store_view_name]',0),
('category_meta_keywords', 'Category Meta Keywords', '<b>Template variables</b>\r\n[category],[website_name],[parent_category],[categories],[store_name],[store_view_name]',0),
('category_description', 'Category Description', '<b>Template variables</b>\r\n[category],[website_name],[parent_category],[categories],[store_name],[store_view_name]',0);
");
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'orig_name', 'label', 'Original Product Name');
$installer->endSetup();