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

class MageWorx_SeoSuite_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    public function getUrl() {
        if(Mage::getStoreConfig('mageworx_seo/seosuite/disable_layered_rewrites')) return parent::getUrl();
        $request = Mage::app()->getRequest();
        if ($request->getModuleName() == 'catalogsearch') {
            return parent::getUrl();
        }

        if ($this->getFilter() instanceof Mage_Catalog_Model_Layer_Filter_Category) {
            $category = Mage::getModel('catalog/category')->load($this->getValue());

            $query = array(
                Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
            );

            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');            
            $catpart = $category->getUrl();
            $catpart = ($suffix && substr($catpart, -(strlen($suffix)))==$suffix?substr($catpart, 0, -(strlen($suffix))):$catpart);            
            if (preg_match('/\/l\/.+/', Mage::app()->getRequest()->getOriginalPathInfo(), $matches)) {
                $layeredpart = ($suffix && substr($matches[0], -(strlen($suffix)))==$suffix?substr($matches[0], 0, -(strlen($suffix))):$matches[0]);
            } else {
                $layeredpart = '';
            }
			$catpart = str_replace('?___SID=U','',$catpart);
			$catpart = trim($catpart);
			$layeredpart = trim($layeredpart);
			$catpart = str_replace($suffix,'',$catpart);
			$url = $catpart . $layeredpart . $suffix;
			
	    return $url;
            
        } else {
            $var = $this->getFilter()->getRequestVar();
            $request = Mage::app()->getRequest();

            $labelValue = strpos($request->getRequestUri(), 'catalogsearch') !== false ? $this->getValue()
                    : $this->getLabel();

            $attribute = $this->getFilter()->getData('attribute_model'); //->getAttributeCode()
            if ($attribute) {
                $value = ($attribute->getAttributeCode() == 'price' || $attribute->getBackendType() == 'decimal')
                        ? $this->getValue() : $labelValue;
            } else {
                $value = $labelValue;
            }
            $query = array(
                $var => $value,
                Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
            );
            return Mage::helper('seosuite')->getLayerFilterUrl(array('_current' => true, '_use_rewrite' => true, '_query' => $query));
        }
    }

    public function getRemoveUrl() {
        $request = Mage::app()->getRequest();
        if ($request->getModuleName() == 'catalogsearch') {
            return parent::getRemoveUrl();
        }

        $query = array($this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue());
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return Mage::helper('seosuite')->getLayerFilterUrl($params);
    }

}
