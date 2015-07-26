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
class Magestore_Customercredit_Block_Order_Totals extends Mage_Core_Block_Template
{
    public function initTotals(){
        $order = $this->getParentBlock()->getOrder();
       // Zend_debug::dump($order->getCustomercreditDiscount());die();
        if($order->getCustomercreditDiscount()>0)
        {
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'  =>  $this->getCode(),
                'value' => -$order->getCustomercreditDiscount(),
                'base_value'    => -$order->getBaseCustomercreditDiscount(),
                'label' => 'Customer Credit',
            )),'subtotal');
        }
    }
}
