<?php
/**
 * Backward compatibility with some extensions
 * 
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Abstract_Adminhtml_Controller extends Mage_Adminhtml_Controller_Action
implements Aitoc_Aitsys_Abstract_Model_Interface
{
    /**
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function tool()
    {
        return Aitoc_Aitsys_Abstract_Service::get();
    }
}