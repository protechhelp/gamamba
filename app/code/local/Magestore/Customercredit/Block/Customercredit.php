<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customercredit Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Customercredit extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_Customercredit_Block_Customercredit
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function addTopLinkStores() {
        $toplinkBlock = $this->getParentBlock();
        if ($toplinkBlock)
            $toplinkBlock->addLink($this->__('Buy Store Credit'), 'customercredit/index/index', $this->__('Buy Store Credit'), true, array(), 10);
    }


}