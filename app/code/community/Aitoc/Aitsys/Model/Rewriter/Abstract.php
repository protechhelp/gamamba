<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 * @author Andrei
 */
abstract class Aitoc_Aitsys_Model_Rewriter_Abstract extends Aitoc_Aitsys_Abstract_Model
{
    protected $_etcDir          = '';
    protected $_codeDir         = '';
    protected $_rewriteDir      = '';
    protected $_checkClassDir   = array();
    protected $_phpcli          = false;
    protected $_conn;
    protected $_localConfig;
    
    const REWRITE_CACHE_DIR = '/ait_rewrite/';
    
    static public function getRewritesCacheDir()
    {
        return BP . DS . Aitoc_Aitsys_Model_Platform::getInstance()->getVarPath() . self::REWRITE_CACHE_DIR;
    }
    
    public function __construct()
    {
        $this->_etcDir      = BP . '/app/etc/';
        $this->_codeDir     = BP . '/app/code/';
        $this->_rewriteDir  = self::getRewritesCacheDir();
        
        $this->_checkClassDir[] = $this->_codeDir . 'local/';
        $this->_checkClassDir[] = $this->_codeDir . 'community/';
        $this->_checkClassDir[] = $this->_codeDir . 'core/';
        
        if (!file_exists($this->_rewriteDir))
        {
            $this->tool()->filesystem()->mkDir($this->_rewriteDir);
        }
    }
}