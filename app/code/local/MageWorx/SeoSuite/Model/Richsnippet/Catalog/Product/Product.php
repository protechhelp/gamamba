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
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */
/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

/**
 * @see MageWorx_SeoSuite_Block_Catalog_Product_View
 */
class MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Product extends MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Abstract
{

//    protected $_imageUri                 = 'seosuite/catalog_product_richsnippet_image';
//    protected $_descriptionUri           = 'seosuite/catalog_product_richsnippet_description';
//    protected $_availabilityByContentUri = 'seosuite/catalog_product_richsnippet_availability';
    protected $_imageUri                 = 'seosuite/richsnippet_catalog_product_image';
    protected $_descriptionUri           = 'seosuite/richsnippet_catalog_product_description';
    protected $_availabilityByContentUri = 'seosuite/richsnippet_catalog_product_availability';

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $commonNode = $this->_findCommonContainer($node);
        $parentNode = $this->_findParentContainer($node);

        if ($commonNode && $parentNode) {
            $commonNode->itemtype  = "http://schema.org/Product";
            $commonNode->itemscope = "";
            $parentNode->itemprop  = "name";
            return true;
        }
        return false;
    }

    protected function _afterRender()
    {
        $description = Mage::getModel($this->_descriptionUri);
        if (!$description->render($this->_html, $this->_block)) {
            $this->_errorRenderer(Mage::helper('seosuite/richsnippet')->__("Product description property wasn't added."));
        }
        $image = Mage::getModel($this->_imageUri);
        if (!$image->render($this->_html, $this->_block)) {
            $this->_errorRenderer(Mage::helper('seosuite/richsnippet')->__("Product image property wasn't added."));
        }
        $availability = Mage::getModel($this->_availabilityByContentUri);
        if (!$availability->render($this->_html, $this->_block)) {
            $this->_errorRenderer(Mage::helper('seosuite/richsnippet')->__("Product availability property wasn't added."));
        }
        return true;
    }

    protected function _beforeInit($html)
    {
        if (!is_object($html)) {
            $html = $this->_magentoHtmlFix($html);
        }
        return parent::_beforeInit($html);
    }

    protected function _beforeRender($html)
    {
        $priceReport = $this->_getPriceReportObject();
        if (is_object($priceReport) && $priceReport->getStatus() != 'success') {
            return false;
        }
        return parent::_beforeRender($html);
    }

    /**
     * Find highest common container for nested items: offer and rating
     * @param simple_html_dom_node $node
     * @return simple_html_dom_node | false
     */
    protected function _findCommonContainer(simple_html_dom_node $node)
    {
        $priceFlag  = false;
        //If the rating wasn't added that generation proceeds...
        $ratingFlag = true;
        if (is_object($this->_getAggregateRatingReportObject())) {
            if ($this->_getAggregateRatingReportObject()->getStatus() == "success") {
                $ratingFlag = false;
            }
        }

        $node = clone $node;
        while ($node = $node->parent) {
            if ($node->tag == "root") {
                return false;
            }
            if (in_array($node->tag, $this->_container)) {
                if (!$ratingFlag) {
                    $resultRating = $node->find('*[itemprop=aggregateRating]');
                    if (is_array($resultRating) && count($resultRating)) {
                        $ratingFlag = true;
                    }
                }
                if (!$priceFlag) {
                    $resultPrice = $node->find('*[itemprop="offers"]');
                    if (is_array($resultPrice) && count($resultPrice)) {
                        $priceFlag = true;
                    }
                }
                if ($ratingFlag && $priceFlag && $node->parent->tag == 'root') {
                    return $node;
                }
            }
        }
        return false;
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        //will be product name property
        $parentNode = $this->_findParentContainer($node);
        if (!$parentNode) {
            return false;
        }
        //will be main product item
        if (!$this->_isNotInsideTypes($node)) {
            return false;
        }

        if (!$this->_findCommonContainer($parentNode)) {
            return false;
        }
        return $node;
    }

    /**
     * @return Varien_Object or false
     */
    protected function _getAggregateRatingReportObject()
    {
//        return new Varien_Object(array('status'=>'success', 'tag'=>'div'));
        return Mage::registry('mageworx_richsnippet_aggregate_rating_report');
    }

    /**
     * @return Varien_Object or false
     */
    protected function _getPriceReportObject()
    {
        return Mage::registry('mageworx_richsnippet_price_report');
    }

    protected function _checkBlockType()
    {
        return true;
    }

    protected function _getItemValues()
    {
        return array($this->_product->getName());
    }

    protected function _magentoHtmlFix($html)
    {
        /*
         * Magento code without space between |name="bundle_option[*]"| and |value="*"|
         * Html parser crop |value="*"|
         */

        /*
         * Example code:
          <div class="input-box">
          <ul class="options-list">
          <li><input type="radio" onclick="bundle.changeSelection(this)"
          class="radio validate-one-required-by-name change-container-classname"
          id="bundle-option-20-54" name="bundle_option[20]"value="54"/>
         */

        $html = str_replace("\"v", "\" v", $html);
        return $html;
    }

}