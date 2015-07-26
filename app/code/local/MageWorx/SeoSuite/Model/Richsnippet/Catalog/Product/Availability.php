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
 * Information is looked for in a product html, but availability tag is inserted in a offer tag (near a price tag).
 * Because in a default template availability information outside of a offer tag.
 *
 * If price richsnippet fail (status flag in register), product won't be rendered and this code won't be executed.
 *
 * @see MageWorx_SeoSuite_Model_Catalog_Product_Richsnippet_Product
 */
class MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Availability extends MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Abstract
{

    protected $_availabilityStatus = false;

    protected function _beforeRender($html)
    {
        $string = (is_object($html)) ? $html->innertext : $html;

        if (strpos($string, Mage::helper('catalog')->__('In stock')) !== false) {
            $this->_availabilityStatus = 'in';
        }
        elseif (strpos($string, Mage::helper('catalog')->__('Out of stock')) !== false) {
            $this->_availabilityStatus = 'out';
        }

        if (!$this->_availabilityStatus) {
            $this->_availabilityStatus = ($this->_product->isInStock()) ? 'in' : 'out';
        }
        return parent::_beforeRender($html);
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        return ($this->_availabilityStatus) ? true : false;
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        if ($this->_availabilityStatus == 'in') {
            $node->innertext = $node->innertext .
                    '<link itemprop="availability" href="http://schema.org/InStock">';
        }
        elseif ($this->_availabilityStatus == 'out') {
            $node->innertext = $node->innertext .
                    '<link itemprop="availability" href="http://schema.org/OutOfStock">';
        }
    }

    protected function _getItemConditions()
    {
        return array("*[itemtype=http://schema.org/Offer]");
    }

    function _checkBlockType()
    {
        return true;
    }

    /**
     * Changes $this->_html
     * @return boolean
     */
    protected function _doRender()
    {

        $nodes = $this->_createPossibleNodesArray();
        if (!count($nodes)) {
            return false;
        }

        $node = $this->_chooseNode($nodes);
        if (!$node) {
            return false;
        }

        if (!$this->_addAttributeForNodes($node)) {
            return false;
        }

        return true;
    }

    protected function _chooseNode(array $nodes)
    {
        foreach ($nodes as $node) {
            if ($this->_isValidNode($node)) {
                return $node;
            }
        }
        return false;
    }

}