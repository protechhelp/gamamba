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
class Magpleasure_Richsnippets_Block_Page_Html_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{
    /**
     * Helper
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
        $html = $this->_addMarkup($html);
        return $html;
    }

    /**
     * Add markup for breadcrumbs
     *
     * @param $html
     * @return mixed
     */
    protected function _addMarkup($html)
    {
        $type = $this->_helper()->getConfigValue('general', 'breadcrumbs');
        if (Magpleasure_Richsnippets_Model_System_Config_Source_Breadcrumbs::NONE == $type) {
            return $html;
        }

        if (Magpleasure_Richsnippets_Model_System_Config_Source_Breadcrumbs::DATA_VOCABULARY == $type) {
            $html = preg_replace('/(<li.*?)>[\s]*?(<a.*?\/a>)/', '$1 itemscope itemtype="http://data-vocabulary.org/Breadcrumb">$2', $html);
            $html = preg_replace('/(<a.*?)>/', '$1 itemprop="url">', $html);
            $html = preg_replace('/(<a.*?>)([\s\S]*?)(<\/a>)/', '$1$2$3 <meta itemprop="title" content="$2">', $html);
        } else if (Magpleasure_Richsnippets_Model_System_Config_Source_Breadcrumbs::SCHEMA == $type) {
            $html = preg_replace('/(<ul.*?)>/', '$1 itemscope itemtype="http://schema.org/BreadcrumbList">', $html);
            $html = preg_replace('/(<li.*?)>[\s]*?(<a.*?\/a>)/', '$1 itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">$2', $html);
            $html = preg_replace('/(<a.*?)>/', '$1 itemprop="item">', $html);
            $html = preg_replace('/(<a.*?>)([\s\S]*?)(<\/a>)/', '$1$2$3 <meta itemprop="name" content="$2">', $html);

            $html = preg_replace_callback('/(<a.*?>[\s\S]*?<\/a>)/', function ($matches) {
                static $position = 0;
                return $matches[0] . '<meta itemprop="position" content="' . ++$position . '">';
            }, $html);
        }

        return $html;
    }
}