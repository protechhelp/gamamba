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


class AW_Helpdesk3_Model_Ticket_History_Event_Assignee extends AW_Helpdesk3_Model_Ticket_History_Event_Abstract
{
    const TYPE = 1;

    protected $_eventData = array();

    public function getType()
    {
        return self::TYPE;
    }

    public function setEventData($data)
    {
        $this->_eventData = $data;
        return $this;
    }

    public function getEventData()
    {
        return $this->_eventData;
    }

    public function isSystem()
    {
        return true;
    }

    /**
     * @param AW_Helpdesk3_Model_Ticket $ticket
     *
     * @return bool
     */
    public function isEventHappened($ticket)
    {
        if (!$ticket->isObjectNew()
            && $ticket->getOrigData('department_agent_id') != $ticket->getDepartmentAgentId()
        ) {
            $this->setEventData(
                array(
                    'from' => $ticket->getOrigData('department_agent_id'),
                    'to'   => $ticket->getDepartmentAgentId(),
                )
            );
            return true;
        }
        return false;
    }

    /**
     * @param string                            $data
     * @param AW_Helpdesk3_Model_Ticket_History $history
     *
     * @return $this
     */
    public function process($data, $history)
    {
        $notification = $history->getTicket()->getDepartment()->getEmailNotification();

        //if current agent = getPrimaryAgent no email
        if (Mage::helper('aw_hdu3/ticket')->getCurrentDepartmentAgent()->getId()
            != $history->getTicket()->getDepartment()->getPrimaryAgent()->getId()
        ) {
            $notification->sendToPrimaryAgentNotificationReassign($history);
        }

        //if current agent = getDepartmentAgentId no email
        if (Mage::helper('aw_hdu3/ticket')->getCurrentDepartmentAgent()->getId() != $data['to']) {
            $notification->sendToNewAssigneeNotificationReassign($history);
        }
        $history->getTicket()->setIsAgentChanged(true);
        return $this;
    }
}