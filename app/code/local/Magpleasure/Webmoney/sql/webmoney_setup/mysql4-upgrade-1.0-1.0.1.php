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
 * @package    Magpleasure_Adminlogger
 * @version    1.0.1
 * @copyright  Copyright (c) 2012-2013 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

$installer = $this;

$installer->startSetup();

/** @var $helper Magpleasure_Webmoney_Helper_Data */
$helper = Mage::helper('webmoney');

try {

    $salesVersion = "1.4.0.21";
    if (version_compare($helper->getCommon()->getMagento()->getModuleVersion('Mage_Sales'), $salesVersion, '>=')){

        /** @var $status Mage_Sales_Model_Order_Status */
        $status = Mage::getModel('sales/order_status');
        $status->load('pending_webmoney', 'status');

        if (!$status->getId()){
            $status
                ->setStatus('pending_webmoney')
                ->setLabel('Pending Webmoney')
                ->save()
                ;

            $status->assignState('pending_payment');
        }
    }

    # Custom Order Status compatibility
    if ($helper->getCommon()->getMagento()->isModuleEnabled('Magpleasure_Orderstatus')){

        /** @var $status Magpleasure_Orderstatus_Model_Orderstatus */
        $status = Mage::getModel('orderstatus/orderstatus');
        $status->load('pending_webmoney', 'value');
        if (!$status->getId()){
            $status
                ->setValue('pending_webmoney')
                ->setLabel('Pending Webmoney')
                ->setPosition(1000)
                ->setStates(array('pending_payment'))
                ->setStatus(Magpleasure_Orderstatus_Model_Status::STATUS_ENABLED)
                ->save()
            ;

        }
    }

} catch (Exception $e){
    $helper->getCommon()->getException()->logException($e);
}


$installer->endSetup();