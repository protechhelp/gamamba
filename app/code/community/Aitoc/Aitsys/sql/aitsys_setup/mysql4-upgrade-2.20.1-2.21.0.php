<?php

/* @var $this Aitoc_Aitsys_Model_Mysql4_Setup */
$this->startSetup();

    $this->run("
    
    ALTER TABLE {$this->getTable('aitsys_performer')} ADD `path_hash` VARCHAR( 32 ) NOT NULL AFTER `product_id`;
    
    ");
    
$this->endSetup();