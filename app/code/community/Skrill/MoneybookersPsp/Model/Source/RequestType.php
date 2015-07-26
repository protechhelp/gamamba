<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Skrill
 * @package    Skrill_MoneybookersPsp
 * @copyright  Copyright (c) 2012 Skrill Holdings Ltd. (http://www.skrill.com)
 */

class Skrill_MoneybookersPsp_Model_Source_RequestType
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE, 'label' => Mage::helper('moneybookerspsp')->__('Preauthorization')),
            array('value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE, 'label' => Mage::helper('moneybookerspsp')->__('Authorization')),
        );
    }
}