<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Webmoney
 * @version    1.0.1
 * @copyright  Copyright (c) 2010-2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Webmoney_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Customer mutex path
     */
    const CUSTOMER_MUTEX_PATH = 'mp_wm_cust_mutex';

    /**
     * Retrives customer session
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Set up customer mutex
     * @return Magpleasure_Webmoney_Helper_Data
     */
    public function setUpCustomerMutex()
    {
        $this->_getCustomerSession()->setData(self::CUSTOMER_MUTEX_PATH, true);
        return $this;
    }

    /**
     * Retrieves MUTEX value
     *
     * @param bool $save
     * @return mixed
     */
    public function getCustomerMutex($save = false)
    {
        $ret = $this->_getCustomerSession()->getData(self::CUSTOMER_MUTEX_PATH);
        if (!$save){
            $this->_getCustomerSession()->setData(self::CUSTOMER_MUTEX_PATH, null);
        }
        return $ret;
    }

    /**
     * Magpleasure Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    public function getCommon()
    {
        return Mage::helper('magpleasure');
    }

}
