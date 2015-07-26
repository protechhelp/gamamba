<?php

/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Richsnippets
 * @copyright  Copyright (c) 2014-2015 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */
class Magpleasure_Richsnippets_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{

    /**
     * Rich snippets helper
     *
     * @return Magpleasure_Richsnippets_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('richsnippets');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        if ($this->_helper()->getConfigValue('general', 'enabled')) {
            $html = $this->_insertRichSnippets($html);
        }
        return $html;
    }

    /**
     * Insert product schema.org markup
     *
     * @param $html
     * @return mixed
     */
    protected function _insertRichSnippets($html)
    {
        $markup = $this->getLayout()->getBlock('mp.richsnippets')->toHtml();
        $html = preg_replace('/(div.*?class=".*?product-view.*?".*?)>/', '$1 itemscope itemtype="http://schema.org/Product">' . $markup, $html);
        return $html;
    }
}