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

// Direct debit Lastschrift (ELV)
class Skrill_MoneybookersPsp_Model_Va_Did extends Skrill_MoneybookersPsp_Model_Va
{
    protected $_code = 'moneybookerspsp_va_did';

    protected function _initRequestParams($isOrderPlaced = true)
    {
        $params = parent::_initRequestParams($isOrderPlaced);
        $params['CRITERION.MONEYBOOKERS_payment_methods'] = 'DID';

        return $params;
    }
}

?>
