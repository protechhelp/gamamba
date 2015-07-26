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
 * @package    Magpleasure_Common
 * @version    0.8.1
 * @copyright  Copyright (c) 2012-2015 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Common_Controller_Action_Widget_Dialog extends Magpleasure_Common_Controller_Action
{
    protected $_dialog;

    /**
     * Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * Retrieves Dialog Block
     *
     * @return Magpleasure_Common_Block_Widget_Dialog
     */
    protected function _getDialog()
    {
        if (!$this->_dialog){
            $this->_dialog = $this->getLayout()->createBlock('magpleasure/widget_dialog');
        }
        return $this->_dialog;
    }

    protected function _prepareDialog()
    {
        # Add Blocks to Dialog
        # $this->_getDialog()->addBeforeButtonsBlock('module/block');
    }

    public function windowAction()
    {
        $result = array();

        $width = $this->getRequest()->getParam('width');
        $height = $this->getRequest()->getParam('height');

        $result['width'] = $this->_commonHelper()->getCore()->urlDecode($width);
        $result['height'] = $this->_commonHelper()->getCore()->urlDecode($height);

        try {
            $this->_prepareDialog();
            $result['html'] = $this->_getDialog()->toHtml();
            $result['success'] = true;
        } catch (Exception $e) {
            $this->getCustomerSession()->addError($e->getMessage());
            $result['message'] = $this->_getMessageBlockHtml();
        }
        $this->_ajaxResponse($result);
    }

    public function postAction(){}
}