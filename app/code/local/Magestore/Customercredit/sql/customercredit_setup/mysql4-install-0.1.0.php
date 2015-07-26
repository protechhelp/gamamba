<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
/* Add credit value attribute*/
$data = array(
    'type'	=>	'decimal',
    'input'	=>	'text',
    'label'	=>	'Credit Balance',
    'backend'	=>	'',
    'frontend'	=>	'',
    'source'	=> '',
    'is_visible'	=>	0,
    'is_visible_on_front' => 0,
    'required'	=>	0,
    'user_defined'	=>	0,
    'is_searchable'	=>	1,
    'is_filterable'	=>	0,
    'is_comparable'	=>	0,
    'position'	=> 2,
    'unique'	=>	0,
    'default'	=> 0.00,
    'is_global'	=>	''
);
$setup->addAttribute('customer', 'credit_value', $data);
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer','credit_value');
$attribute->setDefaultValue('0.00');
$attribute->save();

$setup->removeAttribute('catalog_product','credit_amount');
$attr = array(
    'group'		=> 'Prices',
    'type'		=> 'text',
    'input'		=> 'text',
    'label'		=> 'Credit Amount',
    'backend'	=> '',
    'frontend'	=> '',
    'source'	=> '',
    'visible'	=> 1,
    'user_defined'			=> 1,
    'used_for_price_rules'	=> 1,
    'position'				=> 2,
    'unique'				=> 0,
    'default'				=> '',
    'sort_order'			=> 101,
    'note'          => 'For e.g:
                        <br/>Fixed price: 100 
                        <br/>Option Price: 10,20,30 
                        <br/>Price range: 1-100
                        ',
);
$setup->addAttribute('catalog_product','credit_amount',$attr);

$creditAmount = Mage::getModel('catalog/resource_eav_attribute')->load($setup->getAttributeId('catalog_product','credit_amount'));

$creditAmount->addData(array(
    'is_global'		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required'	=> 0,
    'apply_to'		=> array('customercredit'),
    'is_configurable'	=> 1,
    'is_searchable'		=> 1,
    'is_visible_in_advanced_search'	=> 1,
    'is_comparable'		=> 0,
    'is_filterable'		=> 0,
    'is_filterable_in_search'	=> 1,
    'is_used_for_promo_rules'	=> 1,
    'is_html_allowed_on_front'	=> 0,
    'is_visible_on_front'		=> 0,
    'used_in_product_listing'	=> 1,
    'used_for_sort_by'			=> 0,
    'backend_type'				=> 'text',
))->save();

$tax = Mage::getModel('catalog/resource_eav_attribute')->load($setup->getAttributeId('catalog_product','tax_class_id'));
$applyTo = explode(',',$tax->getData('apply_to'));
$applyTo[] = 'customercredit';
$tax->addData(array('apply_to' => $applyTo))->save();
/**
 * create customercredit table
 */
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('credit_transaction')};
DROP TABLE IF EXISTS {$this->getTable('credit_code')};
DROP TABLE IF EXISTS {$this->getTable('type_transaction')};
	
CREATE TABLE {$this->getTable('credit_transaction')} (
  `transaction_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL ,
  `type_transaction_id` int(11) NULL,
  `detail_transaction` varchar(255)  NULL default '',
  `order_increment_id` varchar(30) NULL default '',
  `amount_credit` decimal(12,4) default '0',
  `begin_balance` decimal(12,4) default '0',
  `end_balance` decimal(12,4) default '0',
  `transaction_time` datetime NULL,
  `customer_group_ids` int(10) default '0',
  `status` varchar(20) default '',
  `spent_credit` decimal(12,4) NOT NULL,
  `received_credit` decimal(12,4) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  INDEX (`customer_id`),
  FOREIGN KEY (`customer_id`)
  REFERENCES {$this->getTable('customer_entity')} (`entity_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('credit_code')} (
  `credit_code_id` int(11) unsigned NOT NULL auto_increment,
  `credit_code` varchar(255) NOT NULL default '',
  `currency` varchar(45) default '',
  `description` text default '',
  `transaction_time` datetime NULL,
  `status` varchar(20) default '',
  `amount_credit` decimal(12,4) default '0',
  `recipient_email` varchar(200) default '',
  `customer_id` int(10) unsigned NOT NULL ,
  PRIMARY KEY (`credit_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('type_transaction')} (
  `type_transaction_id` int(11) unsigned NOT NULL auto_increment,
  `transaction_name` varchar(255) default '',
  PRIMARY KEY (`type_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT  INTO {$this->getTable('type_transaction')}(`type_transaction_id`,`transaction_name`) 
VALUES  (1,'Changed by admin'),
        (2,'Send credit to friends'),
        (3,'Receive Credit from Friends'),
        (4,'Redeem Credit'),
        (5,'Receive order refund by credit'),
        (6,'Check Out by Credit'),
        (7,'Cancel sending credit'),
        (8,'Customer Buy Credit'),
        (9,'Cancel Order'),
        (10,'Refund Credit Product')
;

ALTER TABLE {$this->getTable('sales_flat_order')} ADD `customercredit_discount` decimal(12,4) NOT NULL;
ALTER TABLE {$this->getTable('sales_flat_order')} ADD `base_customercredit_discount` decimal(12,4) NOT NULL;
");

$installer->endSetup();

