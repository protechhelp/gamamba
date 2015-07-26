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
class Magestore_Customercredit_Block_Adminhtml_Customer_Tab_Transaction extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('transactionGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
       // $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $customerId = $this->getRequest()->getParam('customer_id');
        if (!$customerId)
            $customerId = Mage::registry('current_customer')->getId();
        $collection = Mage::getModel('customercredit/transaction')->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $collection->getSelect()->joinLeft(
                array('table_type_transaction' => $collection->getTable('customercredit/typetransaction')),
                'table_type_transaction.type_transaction_id = main_table.type_transaction_id',
                array('type_transaction' => 'table_type_transaction.transaction_name')
                );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('transaction_id', array(
            'header' => Mage::helper('customercredit')->__('ID'),
            'align' => 'left',
            'width' => '50px',
            'type' => 'number',
            'index' => 'transaction_id',
        ));
        $this->addColumn('type_transaction', array(
            'header' => Mage::helper('customercredit')->__('Type of Transaction'),
            'align' => 'left',
            'filter_index' => 'table_type_transaction.transaction_name',
            'index' => 'type_transaction',
        ));

        $this->addColumn('detail_transaction', array(
            'header' => Mage::helper('customercredit')->__('Transaction Detail'),
            'align' => 'left',
            'index' => 'detail_transaction',
        ));
        
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $this->addColumn('amount_credit', array(
            'header' => Mage::helper('customercredit')->__('Added/ Subtracted'),
            'align' => 'left',
            'index' => 'amount_credit',
            'currency_code' => $currency,
            'type' => 'price',
        ));
           $this->addColumn('end_balance', array(
            'header' => Mage::helper('customercredit')->__('Credit Balance'),
            'align' => 'left',
            'index' => 'end_balance',
            'currency_code' => $currency,
            'type' => 'price',
        ));
           $this->addColumn('transaction_time', array(
            'header' => Mage::helper('customercredit')->__('Transaction Time'),
            'align' => 'left',
            'index' => 'transaction_time',
            'type' => 'datetime',
        ));
           
        $this->addColumn('status', array(
            'header' => $this->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
        ));
    }
}