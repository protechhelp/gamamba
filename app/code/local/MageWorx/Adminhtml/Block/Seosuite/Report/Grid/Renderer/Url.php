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

class MageWorx_Adminhtml_Block_Seosuite_Report_Grid_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{    
    public function render(Varien_Object $row) {

        $index = $this->getColumn()->getIndex();
        $urlKey = $row->getData($index);
        $url = Mage::app()->getStore($row->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$urlKey; 
  
        return '<a href="'.$url.'" title="'.$url.'" target="_blank">/'.$urlKey.'</a>';
    }
}
