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

class MageWorx_Adminhtml_Block_Xsitemap extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_blockGroup = 'mageworx';
        $this->_controller = 'xsitemap';
        $this->_headerText = Mage::helper('xsitemap')->__('Google Sitemap (Extended)');
        $this->_addButtonLabel = Mage::helper('xsitemap')->__('Add Sitemap');
        parent::__construct();
    }
}
