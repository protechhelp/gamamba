<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('sm_menu_groups')};
CREATE TABLE {$this->getTable('sm_menu_groups')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('sm_menu_items')};
CREATE TABLE {$this->getTable('sm_menu_items')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `show_title` smallint(6) NOT NULL default '1',
  `description` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '1',
  `align`smallint(6) NOT NULL default '1',
  `show_as_group` smallint(6) NOT NULL default '1', 
  `rgt` int(10) NOT NULL default '0',
  `lft` int(10) NOT NULL default '0',
  `depth` int(10) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `cols_nb` int(10) NOT NULL default '0',
  `item_width` int(10) NOT NULL default '0',
  `cols_width` int(10)	NOT NULL default '0',
  `icon_url` varchar(255) NOT NULL default '',
  `target` varchar(255) NOT NULL default '1',  
  `type` int(1) NOT NULL default '0',  
  `data_type` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 