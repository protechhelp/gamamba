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
 * Customercredit Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('customercredit/transaction', 'transaction_id');
    }
    public function getCreditUsed(){
		$table = $this->getMainTable();
        $select = $this->_getReadAdapter()
					->select()->from($table)
					->reset('columns')
					->columns(new Zend_Db_Expr('SUM(spent_credit)'));
		$spent_credit = $this->_getReadAdapter()->fetchOne($select);
		return Mage::helper('core')->currency($spent_credit);
	}
}