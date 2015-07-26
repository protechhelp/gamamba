<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Module_Info_Fallback extends Aitoc_Aitsys_Model_Module_Info_Abstract
{
    /**
     * @var bool
     */
    protected $_loaded = true;
    
    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->_getFallbackPlatform();
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '';
    }
    
    /**
     * @return string
     */
    public function getSerial()
    {
        return '';
    }
}