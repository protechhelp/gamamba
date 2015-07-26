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
 * Customercredit Status Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Status extends Varien_Object
{
    const STATUS_UNUSED    = 1;
    const STATUS_USED    = 2;
    const STATUS_CANCELLED    = 3;
	const STATUS_AWAITING_VERIFICATION = 4;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_UNUSED    => Mage::helper('customercredit')->__('Unused'),
            self::STATUS_USED    => Mage::helper('customercredit')->__('Used'),
            self::STATUS_CANCELLED    => Mage::helper('customercredit')->__('Cancelled'),
			self::STATUS_AWAITING_VERIFICATION => Mage::helper('customercredit')->__('Awaiting verification')
        );
    }
    
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptions()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }

    public function toOptionArray(){
        return self::getOptions();
    }

}