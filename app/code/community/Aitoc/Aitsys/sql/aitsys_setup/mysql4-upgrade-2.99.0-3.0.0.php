<?php
/* @var $this Aitoc_Aitsys_Model_Mysql4_Setup */
$this->startSetup();
$this->run("
    
    DROP TABLE IF EXISTS `{$this->getTable('aitsys_performer')}`;
    DROP TABLE IF EXISTS `{$this->getTable('aitsys_notification')}`;
    
");
$this->endSetup();