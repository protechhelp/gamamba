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
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_SeoSuite_System_Config_Frontend_Url_Apply extends Mage_Adminhtml_Block_System_Config_Form_Field
{    
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {        
        //$html = $element->getElementHtml();
        $this->setElement($element);
        return $this->_getAddRowButtonHtml();
    }

    protected function _getAddRowButtonHtml($sku='') {        
        $url = Mage::helper('adminhtml')->getUrl('mageworx/seosuite/applyUrl/');
        return $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setLabel($this->__('Apply Product Url Template'))
                ->setOnClick("window.open('" . $url . "')")
                ->toHtml();
    }

}