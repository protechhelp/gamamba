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

class Skrill_MoneybookersPsp_Model_Elv extends Skrill_MoneybookersPsp_Model_Abstract
{
    protected $_code = 'moneybookerspsp_elv';

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = false;

    protected $_paymentMethod		= 'DD';
    protected $_defaultLocale		= 'en';
    protected $_supportedLocales        = array('de', 'en');
    protected $_availableMethods        = array('DD');

    protected $_formBlockType = 'moneybookerspsp/form_elv';
    protected $_infoBlockType = 'moneybookerspsp/info_cc';
    
    protected function _isAvailable($quote = null)
    {
        return true;
    }
}