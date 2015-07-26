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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Adminhtml_Block_Xsitemap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'sitemap_id';
        $this->_blockGroup = 'mageworx';
        $this->_controller = 'xsitemap';

        parent::__construct();

        $this->_addButton('generate', array(
            'label'   => Mage::helper('xsitemap')->__('Save & Generate'),
            'onclick' => "$('generate').value=1; editForm.submit();",
            'class'   => 'add',
        ));
    }

    public function getHeaderText()
    {
        if (Mage::registry('sitemap_sitemap')->getId()) {
            return Mage::helper('xsitemap')->__('Edit Sitemap');
        }
        else {
            return Mage::helper('xsitemap')->__('New Sitemap');
        }
    }
}
