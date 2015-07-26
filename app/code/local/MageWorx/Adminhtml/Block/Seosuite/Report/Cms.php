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

class MageWorx_Adminhtml_Block_Seosuite_Report_Cms extends Mage_Adminhtml_Block_Widget_Container
{
    protected function _prepareLayout() {
        $this->_addButton('generate', array(
            'label'   => Mage::helper('seosuite')->__('Generate'),
            'onclick' => "setLocation('{$this->getUrl('*/*/generate')}')",
            'class'   => 'generate'
        ));
        if(!Mage::app()->getRequest()->getParam('store')) {
			Mage::app()->getRequest()->setParam('store',1);
		}
        $this->setChild('grid', $this->getLayout()->createBlock('mageworx/seosuite_report_cms_grid', 'seosuite.report.cms.grid'));
        return parent::_prepareLayout();
    }        
    
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }    

}
