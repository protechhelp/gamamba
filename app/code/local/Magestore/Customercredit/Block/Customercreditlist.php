<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customercredit Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Customercreditlist extends Mage_Catalog_Block_Product_List
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    protected function _getProductCollection()
    {
        $this->_productCollection =  Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addFieldToFilter('type_id', 'customercredit');
        return $this->_productCollection;
    }
	
	public function getAddToCartUrlForCredit($_product){
		return Mage::helper('checkout/cart')->getAddUrl($_product);
	}

}
