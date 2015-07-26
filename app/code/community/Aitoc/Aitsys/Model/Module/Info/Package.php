<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Module_Info_Package extends Aitoc_Aitsys_Model_Module_Info_Xml_Abstract
{
    /**
     * @var string
     */
    protected $_pathSuffix = Aitoc_Aitsys_Model_Module::PACKAGE_FILE;
    
    /**
     * @return string
     */
    public function getVersion()
    {
        return (string)$this->version;
    }
    
    /**
     * @return string
     */
    public function getPlatform()
    {
        return strtolower((string)$this->platform);
    }
    
    /**
     * @return string
     */
    public function getSerial()
    {
        return (string)$this->license;
    }
    
    /**
     * @return string
     */
    public function getLabel()
    {
        return (string)$this->product;
    }
    
    /**
     * @return int
     */
    public function getProductId()
    {
        return (int)$this->product_id;
    }
}