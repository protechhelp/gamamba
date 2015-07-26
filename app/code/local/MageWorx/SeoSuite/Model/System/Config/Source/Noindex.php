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
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_SeoSuite_Model_System_Config_Source_Noindex
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $this->_options = array(
                array('value'=>'^checkout_.+', 'label'=> Mage::helper('seosuite')->__('Checkout Pages')),
                array('value'=>'^contacts_.+', 'label'=> Mage::helper('seosuite')->__('Contact Us Page')),
                array('value'=>'^customer_.+', 'label'=> Mage::helper('seosuite')->__('Customer Account Pages')),
                array('value'=>'^catalog_product_compare_.+', 'label'=> Mage::helper('seosuite')->__('Product Compare Pages')),
                //array('value'=>'^review.+', 'label'=> Mage::helper('seosuite')->__('Product Review Pages')),
                array('value'=>'^rss_.+', 'label'=> Mage::helper('seosuite')->__('RSS Feeds')),
                array('value'=>'^catalogsearch_.+', 'label'=> Mage::helper('seosuite')->__('Search Pages')),
                array('value'=>'.*?_product_send$', 'label'=> Mage::helper('seosuite')->__('Send Product Pages')),
                array('value'=>'^tag_.+', 'label'=> Mage::helper('seosuite')->__('Tag Pages')),
                array('value'=>'^wishlist_.+', 'label'=> Mage::helper('seosuite')->__('Wishlist Pages')),
            );
        }

        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}