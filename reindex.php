<?php
require_once 'app/Mage.php';  
$app = Mage::app('admin');  
umask(0);  
for ($index = 1; $index <= 8; $index++) {  
     $process = Mage::getModel('index/process')->load($index);  
     $process->reindexAll();  
}
?>