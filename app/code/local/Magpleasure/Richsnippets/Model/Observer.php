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
class Magpleasure_Richsnippets_Model_Observer extends Mage_Core_Block_Abstract
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
     * Catalog product view predispatch action
     *
     */
    public function productPageBeforeLoad()
    {
        if (Mage::helper('core')->isModuleOutputEnabled('Magpleasure_Richsnippets') &&
            $this->_helper()->getConfigValue('general', 'enabled')
        ) {
            $node = Mage::getConfig()->getNode('global/blocks/catalog/rewrite');
            $richNode = Mage::getConfig()->getNode('global/blocks/catalog/richrewrite/product_view');
            $node->appendChild($richNode);

            $breadcrumbsNode = Mage::getConfig()->getNode('global/blocks/page/rewrite');
            $richBreadcrumsNode = Mage::getConfig()->getNode('global/blocks/page/richrewrite/html_breadcrumbs');
            $breadcrumbsNode->appendChild($richBreadcrumsNode);
        }
    }
}