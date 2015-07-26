<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Helpdesk3
 * @version    3.3.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Helpdesk3_Block_Adminhtml_Ticket_Edit_Popup_Priority extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aw_hdu3/ticket/edit/popup/priority.phtml');
    }

    /**
     * @return AW_Helpdesk3_Model_Ticket
     */
    public function getTicket()
    {
        return Mage::registry('current_ticket');
    }

    /**
     * @return array
     */
    public function getPriorityOptionArray()
    {
        return AW_Helpdesk3_Model_Source_Ticket_Priority::toOptionArray(Mage::app()->getStore()->getId());
    }

}

