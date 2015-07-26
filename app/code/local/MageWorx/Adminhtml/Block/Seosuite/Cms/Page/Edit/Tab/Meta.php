<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_Seosuite_Cms_Page_Edit_Tab_Meta extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Meta
{
    protected function _prepareForm() {
        parent::_prepareForm();        
        $form = $this->getForm();
        $fieldset = $form->getElements()->searchById('meta_fieldset');        
        
        $values = Mage::getModel('adminhtml/system_config_source_design_robots')->toOptionArray();
        array_unshift($values, array('label' => 'Use Config', 'value' => ''));
        $fieldset->addField('meta_robots', 'select', array(
            'name'      => 'meta_robots',
            'label'     => Mage::helper('seosuite')->__('Robots'),
            'values'    => $values,
        ));
                
        $fieldset->addField('meta_title', 'text', array(
            'name'      => 'meta_title',
            'label'     => Mage::helper('cms')->__('Title'),
            'title'     => Mage::helper('cms')->__('Title'),
            'required'  => false,
            'disabled'  => false
            ),
            '^'
        );
        
        $model = Mage::registry('cms_page');
        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }
}
