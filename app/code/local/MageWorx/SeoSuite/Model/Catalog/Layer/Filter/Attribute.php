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

class MageWorx_SeoSuite_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    protected function _getOptionId($label)
    {
        if ($source = $this->getAttributeModel()->getSource()){
            return $source->getOptionId($label);
        }
        return false;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        if(Mage::getStoreConfig('mageworx_seo/seosuite/disable_layered_rewrites')) return parent::apply($request, $filterBlock);
        $text = $request->getParam($this->_requestVar);  
        if (is_array($text)) {
            return $this;
        }
        
         
        $filter = $this->_getOptionId($text);
        if ($filter && $text) {

            $layeredNavigationCanonical = $this->getAttributeModel()->getLayeredNavigationCanonical();
            if ($layeredNavigationCanonical==1) {
                $layerCanonicalFilter = Mage::registry('layer_canonical_filter');
                if (!$layerCanonicalFilter) $layerCanonicalFilter = array();
                $attributeCode = $this->getAttributeModel()->getAttributeCode();
                $layerCanonicalFilter[$attributeCode] = $text; //$layeredNavigationCanonical;
                Mage::unregister('layer_canonical_filter');
                Mage::register('layer_canonical_filter', $layerCanonicalFilter);
            }
            
            if (method_exists($this, '_getResource')){
                $this->_getResource()->applyFilterToCollection($this, $filter);
            } else {
                Mage::getSingleton('catalogindex/attribute')->applyFilterToCollection(
                    $this->getLayer()->getProductCollection(),
                    $this->getAttributeModel(),
                    $filter
                );
            }
            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            $this->_items = array();
        }
        return $this;
    }
}
