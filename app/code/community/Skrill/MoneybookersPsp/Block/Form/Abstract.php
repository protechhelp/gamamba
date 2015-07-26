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
class Skrill_MoneybookersPsp_Block_Form_Abstract extends Mage_Payment_Block_Form_Cc
{
    /**
     * Retrieve payment configuration object
     *
     * @return Mage_Payo_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('moneybookerspsp/config');
    }

    /**
     * Return payment logo image src
     *
     * @param string $payment Payment Code
     * @return string|bool
     */
    public function getPaymentImageSrc($payment)
    {
        $imageFilename = Mage::getDesign()
            ->getFilename('images' . DS . 'moneybookerspsp' . DS . $payment, array('_type' => 'skin'));

        if (file_exists($imageFilename . '.png')) {
            return $this->getSkinUrl('images/moneybookerspsp/' . $payment . '.png');
        } else if (file_exists($imageFilename . '.gif')) {
            return $this->getSkinUrl('images/moneybookerspsp/' . $payment . '.gif');
        }

        return false;
    }
}