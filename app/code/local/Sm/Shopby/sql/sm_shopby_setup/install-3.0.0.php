<?php

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('sm_shopby/attribute_url_key');
$installer->getConnection()->dropTable($tableName);

$table = $installer->getConnection()
    ->newTable($tableName)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'auto_increment' => true,
        ), 'Id')
    ->addColumn('attribute_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Attribute Code')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        ), 'Store Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        ), 'Option Id')
    ->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Url Key')
    ->setComment('Tag');
$installer->getConnection()->createTable($table);

$installer->endSetup();