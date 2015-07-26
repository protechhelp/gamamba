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
 * At first attributes to the last element (without the link and containing a product name) are added,
 * then elements with links are consistently modified (_afterRender function)
 */
class MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Breadcrumbs extends MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Abstract
{

    protected $_crumbUri = 'seosuite/richsnippet_catalog_product_crumb';

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {

        $parentNode = $this->_findParentContainer($node);
        if ($parentNode) {
            $parentNode->itemscope = "";
            $parentNode->itemtype  = "http://data-vocabulary.org/Breadcrumb";
            return true;
        }
        return false;
    }

    protected function _afterRender()
    {
        $crumb  = Mage::getModel($this->_crumbUri);
        $answer = true;
        while ($answer) {
            $answer = $crumb->render($this->_html, $this->_block);
        }
        return true;
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        if (!$this->_findParentContainer($node)) {
            return false;
        }
        return $node;
    }

    protected function _checkBlockType()
    {
        return true;
    }

    protected function _getItemValues()
    {
        return array(Mage::registry('current_product')->getName());
    }

}