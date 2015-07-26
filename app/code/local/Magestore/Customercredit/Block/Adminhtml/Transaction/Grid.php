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
class Magestore_Customercredit_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('customercreditGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Customercredit_Block_Adminhtml_Customercredit_Grid
     */
    protected function _prepareCollection() {
        $fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
        $ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
        $collection = Mage::getModel('customercredit/transaction')->getCollection();
        $collection->getSelect()->joinLeft(
                array('table_type_transaction' => $collection->getTable('customercredit/typetransaction')), 'table_type_transaction.type_transaction_id = main_table.type_transaction_id', array('type_transaction' => 'table_type_transaction.transaction_name')
        );
        $collection->getSelect()
                ->joinLeft(
                        array('table_customer' => $collection->getTable('customer/entity')), 'table_customer.entity_id = main_table.customer_id', array('customer_email' => 'table_customer.email'))
                ->joinLeft(array('table_cev1' => 'customer_entity_varchar'), 'table_cev1.entity_id=main_table.customer_id', array('firstname' => 'value'))
                ->where('table_cev1.attribute_id=' . $fn->getAttributeId())
                ->joinLeft(array('table_cev2' => 'customer_entity_varchar'), 'table_cev2.entity_id=main_table.customer_id', array('lastname' => 'value'))
                ->where('table_cev2.attribute_id=' . $ln->getAttributeId())
                ->columns(new Zend_Db_Expr("CONCAT(`table_cev1`.`value`, ' ',`table_cev2`.`value`) AS customer_name"));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('transaction_id', array(
            'header' => Mage::helper('customercredit')->__('Transaction_Id'),
            'align' => 'left',
            'width' => '10px',
            'type' => 'number',
            'index' => 'transaction_id',
        ));

        $type_arr = array();
        $coll_trans = Mage::getModel('customercredit/typetransaction')->getCollection();
        $count = 0;
        foreach($coll_trans as $item){
            $count++;
            $type_arr[$count] = $item->getTransactionName();
        }
        
        $this->addColumn('type_transaction_id', array(
            'header' => Mage::helper('customercredit')->__('Transaction Type'),
            'align' => 'left',
            'filter_index' => 'table_type_transaction.type_transaction_id',
            'index' => 'type_transaction_id',
            'type' => 'options',
            'options' => $type_arr,
        ));

        $this->addColumn('detail_transaction', array(
            'header' => Mage::helper('customercredit')->__('Transaction Detail'),
            'align' => 'left',
            'index' => 'detail_transaction',
        ));
        
        $this->addColumn('customer_name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'index' => 'customer_name',
            'filter_index' => 'CONCAT(`table_cev1`.`value`, " ",`table_cev2`.`value`)',
        ));
        $this->addColumn('customer_email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150px',
            'filter_index' => 'table_customer.email',
            'index' => 'customer_email',
            'renderer' => 'customercredit/adminhtml_customer_renderer_customeremail'
        ));
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $this->addColumn('amount_credit', array(
            'header' => Mage::helper('customercredit')->__('Added/Deducted'),
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

    public function getGridUrl() {
        return $this->getUrl('*/*/index', array('_current' => true));
    }

}