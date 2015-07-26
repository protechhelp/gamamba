<?php
/**
 * Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 *
 * @extension   Bitcoin
 * @type        Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Magento
 * @package     Appmerce_Bitcoin
 * @copyright   Copyright (c) 2011-2014 Appmerce (http://www.appmerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Appmerce_Bitcoin_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Quote amount
     *
     * @var int
     */
    protected $_quoteAmount;

    /**
     * Block construction. Set block template.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('appmerce/bitcoin/form.phtml');
    }

    /**
     * Return payment API model
     *
     * @return Appmerce_Bitcoin_Model_Api
     */
    protected function getApi()
    {
        return Mage::getSingleton('bitcoin/api');
    }

    /**
     * Get Bitcoin quote amount from order payment
     *
     * @return string
     */
    public function getQuoteAmount()
    {
        if (is_null($this->_quoteAmount)) {
            $quote = Mage::getModel('checkout/cart')->getQuote();
            $this->_quoteAmount = $this->getApi()->getAmount($quote);
        }
        return $this->_quoteAmount;
    }

}
