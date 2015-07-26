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

class Magpleasure_Common_Block_System_Entity_Form_Element_File_Upload_Render_Template
    extends Mage_Adminhtml_Block_Template
{

    public function getRemoveButtonHtml()
    {
        /** @var $button Magpleasure_Common_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('magpleasure/adminhtml_widget_button')
            ->setData(array(
                'label'                 => $this->__("Remove"),
                'title'                 => $this->__("Remove"),
                'class'                 => 'scalable delete',
                'additional_attributes' => 'ng-click="clearData()"',
                'onclick'               => 'return false;',
            ));

        return $button->toHtml();

    }
}