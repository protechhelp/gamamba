<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Compare Helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Sm_Cartpro_Helper_Catalog_Product_Compare extends Mage_Catalog_Helper_Product_Compare
{

    /**
     * Retrieve remove item from compare list url
     *
     * @param   $item
     * @return  string
     */
    public function getRemoveUrl($item)
    {
		$continueUrl    = Mage::helper('core')->urlEncode(Mage::getBaseUrl());
        $params = array(
            'product'=>$item->getId(),
           // Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
			Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $continueUrl
        );
        return $this->_getUrl('catalog/product_compare/remove', $params);
    }
    public function getClearListUrl()
    {
		$continueUrl    = Mage::helper('core')->urlEncode(Mage::getBaseUrl());
        $params = array(
           // Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
		   Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED =>$continueUrl
        );
        return $this->_getUrl('catalog/product_compare/clear', $params);
    }
    public function getListUrl()
    {
		$continueUrl    = Mage::helper('core')->urlEncode(Mage::getBaseUrl());
        $itemIds = array();
        foreach ($this->getItemCollection() as $item) {
             $itemIds[] = $item->getId();
        }

        $params = array(
            'items'=>implode(',', $itemIds),
            //Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
			Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $continueUrl
        );

        return $this->_getUrl('catalog/product_compare', $params);
    }
}
