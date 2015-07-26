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

/*
 * @see MageWorx_SeoSuite_Model_Catalog_Product_Richsnippet_Product
 */
class MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Image extends MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Abstract
{

    protected function _isValidNode(simple_html_dom_node $node)
    {
        //will be main product item
        $properties = array('http://schema.org/AggregateRating', 'http://schema.org/Offer');
        if (!$this->_isNotInsideTypes($node, $properties)) {
            return false;
        }

        if (!$this->_isInsideTypes($node, array('http://schema.org/Product'))) {
            return false;
        }

        return $node;
    }

    protected function _isInsideTypes(simple_html_dom_node $node, array $types)
    {
        $node = clone $node;
        while ($node = $node->parent) {
            foreach ($types as $key => $itemtype) {
                if ($node->itemtype == $itemtype) {
                    unset($types[$key]);
                }
            }
            if (!count($types)) {
                return true;
            }
        }
        return false;
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $node->itemprop  = "image";
        return true;
    }

    protected function _getItemConditions()
    {
        return array('img');
    }

    function _checkBlockType(){

    }
}