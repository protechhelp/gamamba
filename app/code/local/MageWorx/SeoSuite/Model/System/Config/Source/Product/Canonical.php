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
class MageWorx_SeoSuite_Model_System_Config_Source_Product_Canonical
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array(
                array('label'=> Mage::helper('seosuite')->__('URLs by Path Length'),'value'=> array(
                    array('value'=>'1', 'label'=> Mage::helper('seosuite')->__('Use Longest')),
                    array('value'=>'2', 'label'=> Mage::helper('seosuite')->__('Use Shortest')),
                    )
                ),
                array('label'=> Mage::helper('seosuite')->__('URLs by Categories Counter'),'value'=> array(
                    array('value'=>'4', 'label'=> Mage::helper('seosuite')->__('Use Longest')),
                    array('value'=>'5', 'label'=> Mage::helper('seosuite')->__('Use Shortest')),
                    )
                ),
                array('value'=>'3', 'label'=> Mage::helper('seosuite')->__('Use Root'))
            );
        }
       // echo "<pre>"; print_r($this->_options); exit;
        return $this->_options;
    }
}