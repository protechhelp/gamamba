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
class Magestore_Customercredit_Block_Adminhtml_Customer_Tab_Storecredit extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected $_customer_credit;

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        
        $fieldset = $form->addFieldset('customercredit_fieldset', array('legend' => Mage::helper('customercredit')->__('Credit Information')));

        $fieldset->addField('credit_balance', 'note', array(
            'label' => Mage::helper('customercredit')->__('Current Credit Balance'),
            'title' => Mage::helper('customercredit')->__('Current Credit Balance'),
            'text' => $this->getBalanceCredit(),
        ));
        $fieldset->addField('credit_value', 'text', array(
            'label' => Mage::helper('customercredit')->__('Add or subtract  a credit value'),
            'title' => Mage::helper('customercredit')->__('Add or subtract  a credit value'),
            'name' => 'credit_value',
            'note' => Mage::helper('customercredit')->__('You can add or subtract an amount from customer’s balance by entering a number. For e.g, enter “99” to add $99 and “-99” to subtract $99'),
        ));
          $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('customercredit')->__('Comment'),
            'title' => Mage::helper('customercredit')->__('Comment'),
            'name' => 'description',
        ));
        
        $form->addFieldset('balance_history_fieldset', array('legend' => Mage::helper('customercredit')->__('Transaction History')))->setRenderer($this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')->setTemplate('customercredit/transactionhistory.phtml'));

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getCredit() {
        if (is_null($this->_customer_credit)) {
            $customerId = Mage::registry('current_customer')->getId();
            $this->_customer_credit = Mage::getModel('customer/customer')->load($customerId);
        }
        return $this->_customer_credit;
    }

    public function getTabLabel() {
        return Mage::helper('customercredit')->__('Store Credit');
    }

    public function getTabTitle() {
        return Mage::helper('customercredit')->__('Store Credit');
    }

    public function canShowTab() {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden() {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }
   public function getBalanceCredit() {
       $customer_credit = $this->getCredit()->getCreditValue();
       return Mage::helper('core')->currency($customer_credit);
   }

}

