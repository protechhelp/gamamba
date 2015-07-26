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
class MageWorx_SeoSuite_Model_Richsnippet_Observer
{
    protected $_blockTypes = array(
       "product.info.simple"       => "seosuite/richsnippet_catalog_product_price_default",
       "product.info.virtual"      => "seosuite/richsnippet_catalog_product_price_default",
       "product.info.downloadable" => "seosuite/richsnippet_catalog_product_price_default",
       "product.info.configurable" => "seosuite/richsnippet_catalog_product_price_default",
       "product.info.grouped"      => "seosuite/richsnippet_catalog_product_price_grouped",
       "product.info.bundle"       => "seosuite/richsnippet_catalog_product_price_bundle",
       "breadcrumbs"               => "seosuite/richsnippet_catalog_product_breadcrumbs"
    );

    public function createRichsnippet($observer)
    {
        if(Mage::helper('seosuite/richsnippet')->isRichsnippetEnabled())
        {
            $block = $observer->getBlock();
            if (array_key_exists($block->getNameInLayout(), $this->_blockTypes)) {
                if($block->getNameInLayout() == 'breadcrumbs'){
                    if(!Mage::helper('seosuite/richsnippet')->isProductPage()){
                        return;
                    }
                }
                $transport    = $observer->getTransport();
                $normalOutput = $observer->getTransport()->getHtml();
                $modelUri     = $this->_blockTypes[$block->getNameInLayout()];
                $model        = Mage::getModel($modelUri);
                $modifyOutput = $model->render($normalOutput, $block, true);
                if ($modifyOutput) {
                    $transport->setHtml($modifyOutput);
                }
            }
        }
    }
}
