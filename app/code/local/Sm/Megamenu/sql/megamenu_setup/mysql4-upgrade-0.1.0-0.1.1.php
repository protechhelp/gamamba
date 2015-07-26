<?php


$installer = $this;

$installer->startSetup();
try {
	$installer->run("

		ALTER TABLE {$this->getTable('sm_menu_items')} ADD `custom_class` varchar( 255 ) NOT NULL default '';

	");
} catch (Exception $e) {

}


$installer->endSetup();
