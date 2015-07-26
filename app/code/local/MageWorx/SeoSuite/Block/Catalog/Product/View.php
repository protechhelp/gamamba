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
class MageWorx_SeoSuite_Block_Catalog_Product_View extends MageWorx_SeoSuite_Block_Catalog_Product_View_Abstract
{

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            if ($robots = $this->getProduct()->getMetaRobots()) {
                $headBlock->setRobots($robots);
            }
            $headBlock->setProductDescription(strip_tags($this->getProduct()->getShortDescription()));
        }
        $this->_changeDescription('description');
        $this->_changeDescription('short_description');
        return parent::_prepareLayout();
    }

    private function _changeDescription($dataName)
    {
        $product     = $this->getProduct();
        $description = $product->getData($dataName);
        $matches     = array();
        $prices      = array('price', 'cost', 'tax', 'special_price');
        preg_match_all('~(\[(.*?)\])~', $description, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $data = '';
            $data = $product->getData($match[2]);
            if (in_array($match[2], $prices)) {
                $data = Mage::helper('core')->currency($data);
            }
            $description = str_replace($match[0], $data, $description);
        }
        return $product->setData($dataName, $description);
    }

    function _toHtml()
    {
        $html = parent::_toHtml();
        if (Mage::helper('seosuite/richsnippet')->isRichsnippetEnabled() && $this->getNameInLayout() == 'product.info') {
            $model = Mage::getModel('seosuite/richsnippet_catalog_product_product');
            $updateHtml = $model->render($html, $this, true);
            if ($updateHtml) {
                return $updateHtml;
            }
        }
        return $html;
    }
}