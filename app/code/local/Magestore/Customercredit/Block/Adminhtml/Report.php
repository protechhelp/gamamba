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
 * Customercredit Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customercredit/report/index.phtml');

    }
	
	protected function _prepareLayout()
    {
        $this->setChild('statistics-credit',
                $this->getLayout()->createBlock('customercredit/adminhtml_statisticscredit')
        );
        $this->setChild('max-balance',
                $this->getLayout()->createBlock('customercredit/adminhtml_maxbalance')
        );
        $this->setChild('customer-credit', 
                        $this->getLayout()->createBlock('customercredit/adminhtml_report_dashboard'));
        parent::_prepareLayout();
    }
}