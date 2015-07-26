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

class MageWorx_SeoSuite_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category {

    protected function _getCategoryByName($filter) {
        return Mage::getModel('seosuite/catalog_category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByAttribute('name', $filter);
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        if(Mage::getStoreConfig('mageworx_seo/seosuite/disable_layered_rewrites')) return parent::apply($request, $filterBlock);
        $filter = $request->getParam($this->getRequestVar());
        if (is_null($filter)) {
            return parent::apply($request, $filterBlock);
        }
        if (!is_numeric($filter)) {
            if (Mage::registry('current_category')) {
                $collection = Mage::getModel('catalog/category')->getCollection()
                        ->addAttributeToFilter('parent_id', Mage::registry('current_category')->getId())
                        ->addAttributeToFilter('is_active', 1)
                        ->addAttributeToSelect('name')
                        ->addAttributeToFilter('name', $filter);
                if (count($collection)==0) return parent::apply($request, $filterBlock);
                $this->_appliedCategory = $collection->getFirstItem();
                if (!$this->_appliedCategory->getProductCollection()->count()) {
                    $this->_appliedCategory = $this->_getCategoryByName($filter);
                }
            } else {
                $this->_appliedCategory = $this->_getCategoryByName($filter);
            }

            if ($this->_appliedCategory) {
                $this->_categoryId = $filter = $this->_appliedCategory->getId();
            }
        } else {
            $this->_categoryId = $filter;
            $this->_appliedCategory = Mage::getModel('catalog/category')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($filter);
        }

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()
                    ->addCategoryFilter($this->_appliedCategory);

            $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_appliedCategory->getName(), $filter)
            );
        }

        return $this;
    }

    protected function _isValidCategory($category) {
        return ($category instanceof Varien_Object && $category->getId());
    }

}
