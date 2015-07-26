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

// PaySafe Card
class Skrill_MoneybookersPsp_Model_Va_Psc extends Skrill_MoneybookersPsp_Model_Va
{
    protected $_code            = 'moneybookerspsp_va_psc';
    
    
    protected function _initRequestParams($isOrderPlaced = true)
    {
        $params = parent::_initRequestParams($isOrderPlaced);
        
        $params['IDENTIFICATION.SHOPPERID'] = $this->getConfigData('merchantclientid');
        $params['CRITERION.PAYSAFECARD_countryRestriction'] = $params['ADDRESS.COUNTRY']; 

        $params['ACCOUNT.BRAND'] = 'PAYSAFECARD';
        $params['FRONTEND.COLLECT_DATA'] = 'false';
        $params['FRONTEND.PM.DEFAULT_DISABLE_ALL'] = '';
        $params['FRONTEND.PM.1.ENABLED'] = '';
        $params['FRONTEND.PM.1.SUBTYPES'] = '';
        $params['FRONTEND.PM.1.METHOD'] = '';

        return $params;
    }
    
    protected function _isAvailable($quote = null)
    {
        return parent::_isAvailable($quote);
    }
}

?>