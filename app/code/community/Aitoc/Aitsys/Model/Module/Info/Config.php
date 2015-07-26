<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Module_Info_Config extends Aitoc_Aitsys_Model_Module_Info_Xml_Abstract
{
    /**
     * @var string
     */
    protected $_pathSuffix = 'etc/config.xml';
    
    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->isLoaded() ? (string)$this->modules->{$this->getModule()->getKey()}->version : '';
    }
    
    /**
     * @return string
     */
    public function getPlatform()
    {
        if ($this->isLoaded() && !$this->_platform) {
            $platform = strtolower((string)$this->modules->{$this->getModule()->getKey()}->platform);
            if ($platform && in_array($platform, $this->_platforms)) {
                $this->_platform = $platform;
            } else {
                $this->_platform = $this->_getFallbackPlatform();
            }
        }
        return $this->_platform;
    }
    
    /**
     * @return string
     */
    public function getSerial()
    {
        return '';
    }
}