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
  `entity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
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
  PRIMARY KEY (`entity_id`),
  KEY `prepared_name` (`prepared_name`(8)),
  KEY `prepared_meta_title` (`prepared_meta_title`(8)),
  KEY `entity_id` (`entity_id`,`product_id`,`store_id`),
  CONSTRAINT `FK_SEOSUITE_PEPORT_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `{$this->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{$this->getTable('seosuite_template')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_template')}` ( 
 `template_id` int(2) NOT NULL AUTO_INCREMENT,
  `template_code` varchar(255) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`template_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    
-- DROP TABLE IF EXISTS `{$this->getTable('seosuite_template_store')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_template_store')}` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(2) NOT NULL,
  `store_id` int(5) NOT NULL,
  `template_key` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`entity_id`),
  KEY `template_id` (`template_id`,`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

");

$installer->run("
INSERT INTO `{$this->getTable('seosuite_template')}` (`template_code`, `template_name`, `comment`) VALUES
('product_name', 'Product Name Template',  '<b>Template variables</b>\r\n<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available\r\n<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value\r\n<p>[prefix {attribute} suffix] or\r\n<p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone\r\n<p>Additional variables available for Product Meta Title and Description only: [category], [categories], [store_name], [website_name]\r\n<p>\r\n<p>\r\n<b>Examples</b>\r\n<p>Product URL Key\r\n<p>[name] [by {manufacturer|brand}] [{color} color] [for {price}] will be transformed into\r\n<p>htc-touch-diamond-by-htc-black-color-for-517-50\r\n<p><b>Product Meta Title</b>\r\n<p>[name] [by {manufacturer|brand}] [({color} color)] [for {price}] [in {categories}] will be transformed into\r\n<p>HTC Touch Diamond by HTC (Black color) for € 517.50 in Cell Phones - Electronics'),
('product_url', 'Product URL Keys',  '<b>Template variables</b>\r\n<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available\r\n<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value\r\n<p>[prefix {attribute} suffix] or\r\n<p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone\r\n<p>\r\n<p><b>Examples</b>\r\n<p>Product URL Key\r\n<p>[name] [by {manufacturer|brand}] [{color} color] [for {price}] will be transformed into\r\n<p>htc-touch-diamond-by-htc-black-color-for-517-50'),
('product_short_description', 'Product Short Description',  '<b>Template variables</b>\r\n<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available\r\n<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value\r\n<p>[prefix {attribute} suffix] or\r\n<p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone\r\n<p>Additional variables available for Product Meta Title and Description only: [category], [categories], [store_name], [website_name]\r\n<p>\r\n<p><b>Product Description</b>\r\n<p>Buy [name] [by {manufacturer|brand}] [of {color} color] [for only {price}] [in {categories}] at [{store_name},] [website_name]. [short_description] will be transformed into\r\n<p>Buy HTC Touch Diamond by HTC of Black color for only € 517.50 in Cell Phones - Electronics at Digital Store, Digital-Store.com. HTC Touch Diamond signals a giant leap forward in combining hi-tech prowess with intuitive usability and exhilarating design.'),
('product_description', 'Product Description',  '<b>Template variables</b>\r\n<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available\r\n<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value\r\n<p>[prefix {attribute} suffix] or\r\n<p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone\r\n<p>Additional variables available for Product Meta Title and Description only: [category], [categories], [store_name], [website_name]\r\n<p>\r\n<p>\r\n<b>Examples</b>\r\n<p>Product URL Key\r\n<p>[name] [by {manufacturer|brand}] [{color} color] [for {price}] will be transformed into\r\n<p>htc-touch-diamond-by-htc-black-color-for-517-50\r\n<p><b>Product Meta Title</b>\r\n<p>[name] [by {manufacturer|brand}] [({color} color)] [for {price}] [in {categories}] will be transformed into\r\n<p>HTC Touch Diamond by HTC (Black color) for € 517.50 in Cell Phones - Electronics'),
('product_meta_title', 'Product Meta Title',  '<b>Template variables</b>\r\n<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available\r\n<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value\r\n<p>[prefix {attribute} suffix] or\r\n<p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone\r\n<p>Additional variables available for Product Meta Title and Description only: [category], [categories], [store_name], [website_name]\r\n<p>\r\n<p>\r\n<b>Examples</b>\r\n<p>Product URL Key\r\n<p>[name] [by {manufacturer|brand}] [{color} color] [for {price}] will be transformed into\r\n<p>htc-touch-diamond-by-htc-black-color-for-517-50\r\n<p><b>Product Meta Title</b>\r\n<p>[name] [by {manufacturer|brand}] [({color} color)] [for {price}] [in {categories}] will be transformed into\r\n<p>HTC Touch Diamond by HTC (Black color) for € 517.50 in Cell Phones - Electronics'),
('product_meta_description', 'Product Meta Description',  '<b>Template variables</b>\r\n<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available\r\n<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value\r\n<p>[prefix {attribute} suffix] or\r\n<p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone\r\n<p>Additional variables available for Product Meta Title and Description only: [category], [categories], [store_name], [website_name]\r\n<p>\r\n<p><b>Product Description</b>\r\n<p>Buy [name] [by {manufacturer|brand}] [of {color} color] [for only {price}] [in {categories}] at [{store_name},] [website_name]. [short_description] will be transformed into\r\n<p>Buy HTC Touch Diamond by HTC of Black color for only € 517.50 in Cell Phones - Electronics at Digital Store, Digital-Store.com. HTC Touch Diamond signals a giant leap forward in combining hi-tech prowess with intuitive usability and exhilarating design.'),
('product_meta_keywords', 'Product Meta Keywords', '<b>Template variables</b>\r\n<p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available\r\n<p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value\r\n<p>[prefix {attribute} suffix] or\r\n<p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone\r\n<p>Additional variables available for Product Meta Title and Description only: [category], [categories], [store_name], [website_name]\r\n<p>\r\n<p>\r\n<b>Examples</b>\r\n<p>Product URL Key\r\n<p>[name] [by {manufacturer|brand}] [{color} color] [for {price}] will be transformed into\r\n<p>htc-touch-diamond-by-htc-black-color-for-517-50\r\n<p><b>Product Meta Title</b>\r\n<p>[name] [by {manufacturer|brand}] [({color} color)] [for {price}] [in {categories}] will be transformed into\r\n<p>HTC Touch Diamond by HTC (Black color) for € 517.50 in Cell Phones - Electronics');
");
$string = "INSERT INTO `{$this->getTable('seosuite_template_store')}` (`entity_id`, `template_id`, `store_id`, `template_key`,`last_update`) VALUES";
$listQuery = array();

$config = array();
foreach (Mage::getModel('core/config_data')->getCollection() as $item) {
if(!isset($config[$item->getScopeId()])) {
    $config[$item->getScopeId()] = array();
}
$config[$item->getScopeId()][$item->getPath()] = $item->getValue();
}

$i=0;
foreach (Mage::getModel('core/store')->getCollection()->setLoadDefault(true) as $store) 
{
    $i++;
    $templateUrl = '';
    $templateMetaTitle = '';
    $templateMetaDescr = '';
    if(isset($config[$store->getStoreId()]) && isset($config[$store->getStoreId()]['mageworx_seo/seosuite/product_url_key'])) {
        $templateUrl = $config[$store->getStoreId()]['mageworx_seo/seosuite/product_url_key'];
    }
    if(isset($config[$store->getStoreId()]) && isset($config[$store->getStoreId()]['mageworx_seo/seosuite/product_meta_title'])) {
        $templateMetaTitle = $config[$store->getStoreId()]['mageworx_seo/seosuite/product_meta_title'];
    }
    if(isset($config[$store->getStoreId()]) && isset($config[$store->getStoreId()]['mageworx_seo/seosuite/product_meta_description_template'])) {
        $templateMetaDescr = $config[$store->getStoreId()]['mageworx_seo/seosuite/product_meta_description_template'];
    }
   
    if($templateUrl) {
        $listQuery[] ="('$i', '2', ".$store->getStoreId().", ".$installer->getConnection()->quote($templateUrl).",NOW())";
        $i++;
    }
    if($templateMetaTitle !=="") {
        $listQuery[] ="('$i', '5', ".$store->getStoreId().", ".$installer->getConnection()->quote($templateMetaTitle).",NOW())";
        $i++;
    }
    if($templateMetaDescr !=="") {
        $listQuery[] ="('$i', '6', ".$store->getStoreId().", ".$installer->getConnection()->quote($templateMetaDescr).",NOW())";
    }
}
$string .= join(",\n",$listQuery).";";

if(sizeof($listQuery)>0) {
    $installer->run($string);
}

$installer->addAttribute('catalog_product', 'canonical_url', array(
    'group'             => 'Meta Information',
    'type'              => 'text',
    'backend'           => 'seosuite/catalog_product_attribute_backend_meta_canonical',
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
    'unique'            => false,                    
    'sort_order'        => 60
)); 

$installer->updateAttribute('catalog_product', 'meta_keyword', 'note', 'Leave blank to use Product Meta Keywords Template');

$installer->endSetup();