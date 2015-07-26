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
 * Customercredit Model
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Transaction extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('customercredit/transaction');
    }

    protected function _beforeSave() {
        $this->setTransactionTime(now());
        if (!$this->getStatus()) {
            $this->setStatus('Completed');
        }
        return parent::_beforeSave();
    }

    public function addTransactionHistory($customer_id, $transaction_type_id, $transaction_detail, $order_id, $amount_credit) {
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $customer_group_id = (float) $customer->getGroupId();

        $spent_credit = 0;
        $received_credit = 0;
        if ($transaction_type_id == Magestore_Customercredit_Model_TransactionType::TYPE_CHECK_OUT_BY_CREDIT) {
            $spent_credit = ($amount_credit > 0) ? $amount_credit : -$amount_credit;
        } elseif ($transaction_type_id == Magestore_Customercredit_Model_TransactionType::TYPE_REFUND_ORDER_INTO_CREDIT) {
            $received_credit = ($amount_credit > 0) ? $amount_credit : -$amount_credit;
        }

        if ($transaction_type_id == Magestore_Customercredit_Model_TransactionType::TYPE_BUY_CREDIT) {
            $received_credit = ($amount_credit > 0) ? $amount_credit : -$amount_credit;
        }
        
        $begin_balance = $customer->getCreditValue();
        $end_balance = $begin_balance + $amount_credit;
        if ($end_balance < 0) {
            $end_balance = 0;
        }
        $transaction = Mage::getModel('customercredit/transaction');
        $transaction->setCustomerId($customer_id)
                ->setTypeTransactionId($transaction_type_id)
                ->setDetailTransaction($transaction_detail)
                ->setOrderIncrementId($order_id)
                ->setAmountCredit($amount_credit)
                ->setBeginBalance($begin_balance)
                ->setEndBalance($end_balance)
                ->setCutomerGroupIds($customer_group_id)
                ->setSpentCredit($spent_credit)
                ->setReceivedCredit($received_credit);
        try {
            $transaction->save();
        } catch (Exception $ex) {
            $ex->getMessage();
        }
    }

    public function getTransactionByOrderId($order_id) {
        $transactions = Mage::getModel('customercredit/transaction')->getCollection()
                ->addFieldToFilter('order_increment_id', $order_id);
        return $transactions;
    }

    public function getTransactionCreditMemo($order_id, $type_id) {
        $transactions = Mage::getModel('customercredit/transaction')->getCollection()
                ->addFieldToFilter('order_increment_id', $order_id)
                ->addFieldToFilter('type_transaction_id', $type_id)
                ->addFieldToFilter('amount_credit', array('gt' => 0));
        return $transactions->getSize();
    }

}