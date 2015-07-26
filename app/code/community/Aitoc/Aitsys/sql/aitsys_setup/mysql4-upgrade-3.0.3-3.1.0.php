<?php
/* @var $this Aitoc_Aitsys_Model_Mysql4_Setup */
$this->startSetup();
if($this->getCoreConfig('aitsys/rewriter_status') === false)
{
    $this->setConfigData('aitsys/rewriter_status', 1);
}
$this->endSetup();