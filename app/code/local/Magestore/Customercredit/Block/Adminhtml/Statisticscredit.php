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
class Magestore_Customercredit_Block_Adminhtml_Statisticscredit extends Mage_Core_Block_Template {

    /**
     * prepare block's layout
     *
     * @return Magestore_Bannerslider_Block_Adminhtml_Addbutton
     */
   public function __construct(){
		parent::__construct();
		$this->setTemplate('customercredit/statisticscredit.phtml');
	}
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }
	public function getTotalCredit(){
		$collections = Mage::getResourceModel('customer/customer_collection')
						->joinAttribute('credit_value', 'customer/credit_value', 'entity_id', null, 'left');
		foreach($collections as $item){
			if($item->getCreditValue()){
				$total_credit += $item->getCreditValue();
			}
		}
		return Mage::helper('core')->currency($total_credit);
	}
	public function getCreditUsed(){
		return Mage::getResourceModel('customercredit/transaction')->getCreditUsed();
	}
	
	public function getCustomerWithCredit(){
		$collections = Mage::getResourceModel('customer/customer_collection')
						->addAttributeToFilter('credit_value',array('gt' => 0.00));
		$num_customer = count($collections);
		return $num_customer;
	}
	
	public function percentCredit(){
		$collections = Mage::getResourceModel('customer/customer_collection');
		$total_customer = count($collections);
		$percent = ($this->getCustomerWithCredit() / $total_customer)*100;
		return round($percent,2);
	}
}