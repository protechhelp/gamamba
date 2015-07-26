<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Helpdesk3
 * @version    3.3.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

interface AW_Lib_Model_AdminhtmlPdf_Sales_Order_Pdf_CreditmemoInterface
{
    /**
     * Check is can draw section
     *
     * @return bool
     */
    public function canDraw();

    /**
     * Set order model
     *
     * @param Mage_Sales_Model_Order $order
     * @return mixed
     */
    public function setOrderModel(Mage_Sales_Model_Order $order);

    /**
     * Check if needed rendering after address
     *
     * @return bool
     */
    public function hasAfterAddressSection();

    /**
     * Get data for render section after addresses
     * Return Format:
     * array(
     *     'layout_type' => AW_Lib_Model_Sales_Order_Pdf_Creditmemo::ONE_COLUMN_LAYOUT | AW_Lib_Model_Sales_Order_Pdf_Creditmemo::TWO_COLUMN_LAYOUT,
     *     'column'      => array(
     *         'left'  => array(
     *             'title'  => (string) 'Left Column Section Title',
     *             'values' => array(
     *                 array(
     *                     'value' => (string) 'Value string',
     *                     'type'  => 'label' | 'value',
     *                 ),
     *                 ...,
     *                 ...,
     *                 ...,
     *             ),
     *         ),
     *         'right' => array(
     *             'title'  => (string) 'Right Column Section Title',
     *             'values' => array(
     *                 array(
     *                     'value' => (string) 'Value string',
     *                     'type'  => 'label' | 'value',
     *                 ),
     *                 ...,
     *                 ...,
     *                 ...,
     *             ),
     *         ),
     *     )
     * )
     *
     * @return array
     */
    public function getAddressSectionData();

    /**
     * Check if needed rendering after Payment and shipment
     *
     * @return bool
     */
    public function hasAfterPaymentShipmentSection();

    /**
     * Get data for render section after Payment and shipment
     * Return Format:
     * array(
     *     'layout_type' => AW_Lib_Model_Sales_Order_Pdf_Creditmemo::ONE_COLUMN_LAYOUT | AW_Lib_Model_Sales_Order_Pdf_Creditmemo::TWO_COLUMN_LAYOUT,
     *     'column'      => array(
     *         'left'  => array(
     *             'title'  => (string) 'Left Column Section Title',
     *             'values' => array(
     *                 array(
     *                     'value' => (string) 'Value string',
     *                     'type'  => 'label' | 'value',
     *                 ),
     *                 ...,
     *                 ...,
     *                 ...,
     *             ),
     *         ),
     *         'right' => array(
     *             'title'  => (string) 'Right Column Section Title',
     *             'values' => array(
     *                 array(
     *                     'value' => (string) 'Value string',
     *                     'type'  => 'label' | 'value',
     *                 ),
     *                 ...,
     *                 ...,
     *                 ...,
     *             ),
     *         ),
     *     )
     * )
     *
     * @return array
     */
    public function getPaymentShipmentSectionData();

    /**
     * Check if needed rendering custom sections
     *
     * @return bool
     */
    public function hasCustomSection();

    /**
     * Get data for render custom sections
     * Return Format:
     * array(
     *     'layout_type' => AW_Lib_Model_Sales_Order_Pdf_Creditmemo::ONE_COLUMN_LAYOUT | AW_Lib_Model_Sales_Order_Pdf_Creditmemo::TWO_COLUMN_LAYOUT,
     *     'column'      => array(
     *         'left'  => array(
     *             'title'  => (string) 'Left Column Section Title',
     *             'values' => array(
     *                 array(
     *                     'value' => (string) 'Value string',
     *                     'type'  => 'label' | 'value',
     *                 ),
     *                 ...,
     *                 ...,
     *                 ...,
     *             ),
     *         ),
     *         'right' => array(
     *             'title'  => (string) 'Right Column Section Title',
     *             'values' => array(
     *                 array(
     *                     'value' => (string) 'Value string',
     *                     'type'  => 'label' | 'value',
     *                 ),
     *                 ...,
     *                 ...,
     *                 ...,
     *             ),
     *         ),
     *     )
     * )
     * )
     *
     * @return array
     */
    public function getCustomSectionData();
}