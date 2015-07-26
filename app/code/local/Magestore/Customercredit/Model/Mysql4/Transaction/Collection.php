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
 * Customercredit Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customercredit/transaction');
    }
    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false) {
        $dateEnd = Mage::app()->getLocale()->date();
        $dateStart = clone $dateEnd;

        // go to the end of a day
        $dateEnd->setHour(23);
        $dateEnd->setMinute(59);
        $dateEnd->setSecond(59);

        $dateStart->setHour(0);
        $dateStart->setMinute(0);
        $dateStart->setSecond(0);

        switch ($range) {
            case '24h':
                $dateEnd = Mage::app()->getLocale()->date();
                $dateEnd->addHour(1);
                $dateStart = clone $dateEnd;
                $dateStart->subDay(1);
                break;

            case '7d':
                // substract 6 days we need to include
                // only today and not hte last one from range
                $dateStart->subDay(6);
                break;

            case '1m':
                $dateStart->setDay(Mage::helper('customercredit')->getReportConfig('mtd_start'));
                break;

            case 'custom':
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd = $customEnd ? $customEnd : $dateEnd;
                break;

            case '1y':
            case '2y':
                $startMonthDay = explode(',', Mage::helper('customercredit')->getReportConfig('ytd_start'));
                $startMonth = isset($startMonthDay[0]) ? (int) $startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int) $startMonthDay[1] : 1;
                $dateStart->setMonth($startMonth);
                $dateStart->setDay($startDay);
                if ($range == '2y') {
                    $dateStart->subYear(1);
                }
                $dateEnd->setDay(1);
                $dateEnd->addMonth(1);
                $dateEnd->subDay(1);
                break;
        }

        $dateStart->setTimezone('Etc/UTC');
        $dateEnd->setTimezone('Etc/UTC');

        if ($returnObjects) {
            return array($dateStart, $dateEnd);
        } else {
            return array('from' => $dateStart, 'to' => $dateEnd, 'datetime' => true);
        }
    }

    public function prepareCustomercredit($range, $customStart, $customEnd) {
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $this->getSelect()->columns(array(
            'spent_credit' => 'SUM(spent_credit)',
            'received_credit' => 'SUM(received_credit)',
        ));
        $dateRange = $this->getDateRange($range, $customStart, $customEnd);
        $this->getSelect()->columns(array('range' => $this->_getRangeExpressionForAttribute($range, 'transaction_time')))
                ->order('range', Zend_Db_Select::SQL_ASC)
                ->group('range');
        $this->addFieldToFilter('transaction_time', $dateRange);
        return $this;
    }

    protected function _getRangeExpressionForAttribute($range, $attribute) {
        $expression = $this->_getRangeExpression($range);
        return str_replace('{{attribute}}', $this->getConnection()->quoteIdentifier($attribute), $expression);
    }

    protected function _getRangeExpression($range) {
        switch ($range) {
            case '24h':
                $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d %H:00\')';
                break;
            case '7d':
            case '1m':
                $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d\')';
                break;
            case '1y':
            case '2y':
            case 'custom':
            default:
                $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m\')';
                break;
        }

        return $expression;
    }
}