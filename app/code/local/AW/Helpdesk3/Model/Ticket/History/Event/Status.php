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


class AW_Helpdesk3_Model_Ticket_History_Event_Status extends AW_Helpdesk3_Model_Ticket_History_Event_Abstract
{
    const TYPE = 7;

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
        return false;
    }

    /**
     * @param AW_Helpdesk3_Model_Ticket $ticket
     *
     * @return false
     */
    public function isEventHappened($ticket)
    {
        if (!$ticket->isObjectNew() && $ticket->getOrigData('status') != $ticket->getStatus()) {
            $this->setEventData(
                array(
                    'from' => $ticket->getOrigData('status'),
                    'to'   => $ticket->getStatus(),
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
        $history->getTicket()->setIsStatusChanged(true);
        return $this;
    }
}