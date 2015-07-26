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
class Magestore_Customercredit_Block_History extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_Customercredit_Block_Customercredit
     */
    public function _construct(){
        parent::_construct();
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection =  Mage::getModel('customercredit/transaction')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id);
        $collection->setOrder('transaction_time', 'DESC');
        $this->setCollection($collection);
    }

    public function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('page/html_pager', 'customercredit.history.pager')->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getTransactionType($trans_type_id)
    {
        return Mage::getModel('customercredit/typetransaction')->load($trans_type_id)->getTransactionName();
    }
    public function getCurrencyLabel($credit)
    {
        $credit = Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($credit);
        return Mage::getModel('customercredit/customercredit')->getLabel($credit);
    }

}