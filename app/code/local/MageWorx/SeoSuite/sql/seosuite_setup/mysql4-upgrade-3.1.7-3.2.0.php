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
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('seosuite_report_product')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_report_product')}` ( 
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sku` varchar(64) NOT NULL,
  `url_path` varchar(255) NOT NULL,
  `type_id` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `prepared_name` varchar(255) NOT NULL,
  `name_dupl` smallint(5) unsigned NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `prepared_meta_title` varchar(255) NOT NULL,
  `meta_title_len` tinyint(3) unsigned NOT NULL,
  `meta_title_dupl` smallint(5) unsigned NOT NULL,
  `meta_descr_len` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`,`store_id`),
  KEY `prepared_name` (`prepared_name`(8)),
  KEY `prepared_meta_title` (`prepared_meta_title`(8)), 
  CONSTRAINT `FK_SEOSUITE_PEPORT_PRODUCT` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='';


DROP TABLE IF EXISTS `{$this->getTable('seosuite_report_category')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_report_category')}` ( 
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL,
  `url_path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `prepared_name` varchar(255) NOT NULL,
  `name_dupl` smallint(5) unsigned NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `prepared_meta_title` varchar(255) NOT NULL,
  `meta_title_len` tinyint(3) unsigned NOT NULL,
  `meta_title_dupl` smallint(5) unsigned NOT NULL,
  `meta_descr_len` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`,`store_id`),
  KEY `prepared_name` (`prepared_name`(8)),
  KEY `prepared_meta_title` (`prepared_meta_title`(8))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='';

  
DROP TABLE IF EXISTS `{$this->getTable('seosuite_report_cms')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_report_cms')}` ( 
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` smallint(6) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `url_path` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `prepared_heading` varchar(255) NOT NULL,
  `heading_dupl` smallint(5) unsigned NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `prepared_meta_title` varchar(255) NOT NULL,
  `meta_title_len` tinyint(3) unsigned NOT NULL,
  `meta_title_dupl` smallint(5) unsigned NOT NULL,
  `meta_descr_len` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`,`page_id`,`store_id`),
  KEY `prepared_heading` (`prepared_heading`(8)),
  KEY `prepared_meta_title` (`prepared_meta_title`(8)), 
  CONSTRAINT `FK_SEOSUITE_PEPORT_CMS` FOREIGN KEY (`page_id`) REFERENCES `{$this->getTable('cms_page')}` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='';  
  
");

  

$installer->addAttribute('catalog_product', 'canonical_cross_domain', array(
    'group'             => 'Meta Information',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Cross Domain Canonical URL',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'seosuite/system_config_source_crossdomain',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '0',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,                    
    'sort_order'        => 50
));  
  
$installer->addAttribute('catalog_product', 'canonical_url', array(
    'group'             => 'Meta Information',
    'type'              => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Canonical URL',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'seosuite/catalog_product_attribute_source_meta_canonical',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
//   'used_in_product_listing'=>true,
    'unique'            => false,                    
    'sort_order'        => 60
)); 
  
  
if (!$installer->getConnection()->tableColumnExists($installer->getTable('cms_page'), 'meta_title')) {
    $installer->getConnection()->addColumn($installer->getTable('cms_page'), 'meta_title', "varchar(255) NOT NULL DEFAULT ''");
}

$installer->endSetup();