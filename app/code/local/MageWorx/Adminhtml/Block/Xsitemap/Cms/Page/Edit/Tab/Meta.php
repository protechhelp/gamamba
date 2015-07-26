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
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_Xsitemap_Cms_Page_Edit_Tab_Meta extends MageWorx_Adminhtml_Block_Xsitemap_Cms_Page_Edit_Tab_Meta_Abstract
{
    protected function _prepareForm() {
        parent::_prepareForm();        
        $form = $this->getForm();
        $fieldset = $form->getElements()->searchById('meta_fieldset');
        
        $values = Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions();
        $fieldset->addField('exclude_from_sitemap', 'select', array(
            'name'      => 'exclude_from_sitemap',
            'label'     => Mage::helper('xsitemap')->__('Exclude from XML Sitemap'),
            'values'    => $values,
        ));                
        
        $model = Mage::registry('cms_page');
        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }
}
