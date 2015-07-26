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
class Magestore_Customercredit_Model_Customergroup
{
    public function toOptionArray()
    {
		$customergroup=Mage::getModel('customer/group')->getCollection();
		
		$array_list = array();
		$count=0;
		foreach($customergroup as $group){
			if($group->getCustomerGroupId()){
				$array_list[$count]=array('value'=>$group->getCustomerGroupId(), 'label'=>$group->getCustomerGroupCode());
				$count++;
			}
		}
        return $array_list;
    }
}