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

class MageWorx_SeoSuite_Model_Catalog_Product_Template_Description extends MageWorx_SeoSuite_Model_Catalog_Product_Template_Abstract
{
    protected $_useDefault = array();
    protected $_defaultProduct = null;

    public function process() {
        if (!$this->_product instanceof Mage_Catalog_Model_Product){
            return;
        }
        $store = Mage::app()->getStore($this->_product->getStoreId());
        $this->_product->setStore($store);
        if (!empty($this->_useDefault) || $this->_product->getStore()->getId() > 0){
            $this->_defaultProduct = Mage::getModel('catalog/product')->load($this->_product->getId());
        }

        try {
            $string = $this->__compile($this->getTemplate());
        } catch (Exception $e){}

        return $string;
    }

    public function setUseDefault($array) {
        $this->_useDefault = (array) $array;
        return $this;
    }

}