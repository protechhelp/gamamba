<?php

$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'meta_robots', array(
    'group'             => 'General Information',
    'type'              => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Meta Robots',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'seosuite/catalog_product_attribute_source_meta_robots',
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

$installer->endSetup();