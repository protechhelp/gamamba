<?php
include 'app/Mage.php';
Mage::app();
$setup = Mage::getResourceModel('catalog/setup','catalog_setup');
$setup->removeAttribute('catalog_product','serial_code_customer_groups');
$setup->removeAttribute('catalog_product','serial_code_email_template');
$setup->removeAttribute('catalog_product','serial_code_email_type');
$setup->removeAttribute('catalog_product','serial_code_invoiced');
$setup->removeAttribute('catalog_product','serial_code_low_warning');
$setup->removeAttribute('catalog_product','serial_code_not_available');
$setup->removeAttribute('catalog_product','serial_code_pool');
$setup->removeAttribute('catalog_product','serial_code_send_copy');
$setup->removeAttribute('catalog_product','serial_code_send_email');
$setup->removeAttribute('catalog_product','serial_code_send_warning');
$setup->removeAttribute('catalog_product','serial_code_serialized');
$setup->removeAttribute('catalog_product','serial_code_show_order');
$setup->removeAttribute('catalog_product','serial_code_type');
$setup->removeAttribute('catalog_product','serial_code_update_stock');
$setup->removeAttribute('catalog_product','serial_code_use_customer');
$setup->removeAttribute('catalog_product','serial_code_use_voucher');
$setup->removeAttribute('catalog_product','serial_code_warning_level');
$setup->removeAttribute('catalog_product','serial_code_warning_template');
