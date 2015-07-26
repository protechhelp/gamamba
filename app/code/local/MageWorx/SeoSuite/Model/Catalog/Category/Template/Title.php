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

class MageWorx_SeoSuite_Model_Catalog_Category_Template_Title extends MageWorx_SeoSuite_Model_Catalog_Category_Template_Abstract
{
    protected function __compile($template) {
        $vars = $this->__parse($template);
        foreach ($vars as $key => $params) {
            foreach ($params['attributes'] as $n => $attribute) {
                $value = '';
                switch ($attribute) {
                    case 'category':
                        $value = $this->_category->getName();
                    case 'price':                        
                    case 'special_price':
                        break;
                    case 'categories':
                        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator');
                        $separator = ' ' . $separator . ' ';
                        $paths = explode('/',$this->_category->getPath());
                        if(is_array($paths)) {
                            foreach ($paths as $categoryId) {
                                $category = Mage::getModel('catalog/category')->load($categoryId);
                                $path[] = trim($category->getName());
                                 
                            }
                        }
                        else {
                            $categories = $this->_category->getParentCategories();
                            // add category path breadcrumb
                            foreach ($categories as $categoryId) {
                                $category = Mage::getModel('catalog/category')->load($categoryId->getId());
                                $path[] = trim($category->getName());
                                 
                            }
                        }
                        
                        $path = array_filter($path);
                        //print_r($path); exit;
                        //if($this->_category->getId() == 39) { print_r($path); exit; }
                        if(count($path)>0) {
                            array_shift($path);
                        $value = join($separator, array_reverse($path));
                        }
                        else {
                            $value = '';
                        }
                      //    
                        break;
                    case 'store_view_name':
                        $value = Mage::app()->getStore($this->_category->getStoreId())->getName();
                        break;
                    case 'page_number':
                        $value = Mage::getBlockSingleton('page/html_pager')->getCurrentPage();
                        if($value<2) {
                            $value='';
                        }
                        break;
                    case 'store_name':
                        $value = Mage::app()->getStore($this->_category->getStoreId())->getGroup()->getName();
                        break;
                    case 'parent_category':
                        $value = '';
                        if($this->_category->getParentId()) {
                            $cat = Mage::getModel('catalog/category')->load($this->_category->getParentId());
                            if($cat->getId() !== Mage::app()->getWebsite(Mage::app()->getStore($this->_category->getStoreId())->getWebsite()->getId())->getDefaultStore()->getRootCategoryId()) {
                                $value = $cat->getName();
                            }
                        }
                        break;
                    case 'website_name':
                        $value = Mage::app()->getStore($this->_category->getStoreId())->getWebsite()->getName();
                        break;
                    default:
                        if ($_attr = $this->_category->getResource()->getAttribute($attribute)) {
                            $value = $_attr->getSource()->getOptionText($this->_category->getData($attribute));
                        }
                        if (!$value) {
                            $value = $this->_category->getData($attribute);
                        }
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                }
                
                if ($value) {
                    $value = $params['prefix'] . $value . $params['suffix'];
                    break;
                }
            }
            $template = str_replace($key, $value, $template);
        }
       // echo $template;
        return $template;
    }
}