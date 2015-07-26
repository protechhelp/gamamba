<?php
/*
* ModifyMage Solutions (http://ModifyMage.com)
* Serial Codes - Serial Numbers, Product Codes, PINs, and More
*
* NOTICE OF LICENSE
* This source code is owned by ModifyMage Solutions and distributed for use under the
* provisions, terms, and conditions of our Commercial Software License Agreement which
* is bundled with this package in the file LICENSE.txt. This license is also available
* through the world-wide-web at this URL: http://www.modifymage.com/software-license
* If you do not have a copy of this license and are unable to obtain it through the
* world-wide-web, please email us at license@modifymage.com so we may send you a copy.
*
* @category		Mmsmods
* @package		Mmsmods_Serialcodes
* @author		David Upson
* @copyright	Copyright 2013 by ModifyMage Solutions
* @license		http://www.modifymage.com/software-license
*/

$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('serialcodes')};
CREATE TABLE {$this->getTable('serialcodes')} (
  `serialcodes_id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(255) NOT NULL default '',
  `sku` varchar(255) NOT NULL default '',  
  `code` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `note` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`serialcodes_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	ALTER TABLE {$this->getTable('sales_flat_order_item')} ADD COLUMN `serial_codes` TEXT CHARACTER SET utf8 DEFAULT NULL AFTER `name`;
	ALTER TABLE {$this->getTable('sales_flat_order_item')} ADD COLUMN `serial_code_type` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL AFTER `name`;
");
$installer->endSetup();
$installer->installEntities();