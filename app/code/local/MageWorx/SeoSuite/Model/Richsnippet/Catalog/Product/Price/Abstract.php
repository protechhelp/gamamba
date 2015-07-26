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

abstract class MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Price_Abstract extends MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Abstract
{

    protected $_validBlockType = 'Mage_Catalog_Block_Product_View_Abstract';

    protected function _getItemValues($_product = null)
    {
        if (!$_product) {
            $_product = $this->_product;
        }

        $_weeeHelper = Mage::helper('weee');
        $_taxHelper  = Mage::helper('tax');
        /* @var $_coreHelper Mage_Core_Helper_Data */
        /* @var $_weeeHelper Mage_Weee_Helper_Data */
        /* @var $_taxHelper Mage_Tax_Helper_Data */

        $_storeId           = $_product->getStoreId();
        $_simplePricesTax   = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
        $_minimalPriceValue = $_product->getMinimalPrice();
        $_minimalPrice      = $_taxHelper->getPrice($_product, $_minimalPriceValue, $_simplePricesTax);
        $prices             = array();


        if (!$_product->isGrouped()):
            $_weeeTaxAmount = $_weeeHelper->getAmountForDisplay($_product);
            if ($_weeeHelper->typeOfDisplay($_product,
                            array(Mage_Weee_Model_Tax::DISPLAY_INCL_DESCR, Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL,
                        4))):
                $_weeeTaxAmount = $_weeeHelper->getAmount($_product);
            endif;
            $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
            if ($_weeeHelper->isTaxable() && !$_taxHelper->priceIncludesTax($_storeId)):
                $_attributes             = $_weeeHelper->getProductWeeeAttributesForRenderer($_product, null, null,
                        null, true);
                $_weeeTaxAmountInclTaxes = $_weeeHelper->getAmountInclTaxes($_attributes);
            endif;

            $_price             = $_taxHelper->getPrice($_product, $_product->getPrice());
            $_regularPrice      = $_taxHelper->getPrice($_product, $_product->getPrice(), $_simplePricesTax);
            $_finalPrice        = $_taxHelper->getPrice($_product, $_product->getFinalPrice());
            $_finalPriceInclTax = $_taxHelper->getPrice($_product, $_product->getFinalPrice(), true);
            if ($_finalPrice >= $_price):
                if ($_taxHelper->displayBothPrices()):
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)): // including
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price;
                    else:
                        $prices[] = $_finalPriceInclTax;
                        if ($_finalPrice == $_price):
                            $prices[] = $_price;
                        else:
                            $prices[] = $_finalPrice;
                        endif;
                    endif;
                else:
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)): // including
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                        $prices[] = $_price;
                        $prices[] = $_price + $_weeeTaxAmount;
                    else:
                        if ($_finalPrice == $_price):
                            $prices[] = $_price;
                        else:
                            $prices[] = $_finalPrice;
                        endif;
                    endif;
                endif;
            else: /* if ($_finalPrice == $_price): */
                $_originalWeeeTaxAmount = $_weeeHelper->getOriginalAmount($_product);

                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)): // including
                    if ($_taxHelper->displayBothPrices()):
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_finalPrice + $_weeeTaxAmount;
                    else:
                        $prices[] = $_finalPrice + $_weeeTaxAmountInclTaxes;
                    endif;
                    $prices[] = $_regularPrice + $_originalWeeeTaxAmount;
                elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                    $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                    $prices[] = $_finalPrice + $_weeeTaxAmount;
                    $prices[] = $_regularPrice + $_originalWeeeTaxAmount;
                elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                    $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                    $prices[] = $_finalPrice + $_weeeTaxAmount;
                    $prices[] = $_regularPrice + $_originalWeeeTaxAmount;
                elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                    $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                    $prices[] = $_finalPrice;
                    $prices[] = $_regularPrice;
                else: // excl.
                    if ($_taxHelper->displayBothPrices()):
                        $prices[] = $_finalPriceInclTax;
                    else:
                        $prices[] = $_finalPrice;
                    endif;
                    $prices[] = $_regularPrice;
                endif;

            endif; /* if ($_finalPrice == $_price): */

            if ($_minimalPriceValue && $_minimalPriceValue < $_product->getFinalPrice()):

                $_minimalPriceDisplayValue = $_minimalPrice;
                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, array(0, 1, 4))):
                    $_minimalPriceDisplayValue = $_minimalPrice + $_weeeTaxAmount;
                endif;
                $prices[] = $_minimalPriceDisplayValue;
            endif; /* if ($block->getDisplayMinimalPrice() && $_minimalPrice && $_minimalPrice < $_finalPrice): */

        else: /* if (!$_product->isGrouped()): */

            $_exclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue);
            $_inclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue, true);

            if ($_minimalPriceValue):
                if ($_taxHelper->displayBothPrices()):
                    $prices[] = $_inclTax;
                    $prices[] = $_exclTax;
                else:
                    $_showPrice = $_inclTax;
                    if (!$_taxHelper->displayPriceIncludingTax()) {
                        $_showPrice = $_exclTax;
                    }

                    $prices[] = $_showPrice;
                endif;
            endif; /* if ($block->getDisplayMinimalPrice() && $_minimalPrice): */
        endif; /* if (!$_product->isGrouped()): */

        $modPrices = array();
        foreach ($prices as $price) {
            $modPrices = array_merge($modPrices, $this->_getModifyPrices($price));
        }

        return array_unique($modPrices);
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        $firstParentNode = $this->_findParentContainer($node);
        if ($firstParentNode) {
            $secondParentNode = $this->_findParentContainer($firstParentNode);
            if ($secondParentNode) {
                return true;
            }
        }
        return false;
    }

    protected function _getModifyPrices($price, $deep = 4)
    {
        $prices = array();
        switch ($deep) {
            case 4:
                $prices[] = Mage::helper('core')->currency($price, true, false);
            case 3:
                $prices[] = number_format($price, 2);
            case 2:
                $prices[] = number_format($price, 0);
            case 1:
                $prices[] = $price;
                break;
        }
        return array_unique($prices);
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $priceNode = $this->_findParentContainer($node);
        $offerNode = $this->_findParentContainer($priceNode);

        if ($offerNode && $priceNode) {
            $priceNode->itemprop  = 'price';
            $offerNode->itemtype  = 'http://schema.org/Offer';
            $offerNode->itemscope = '';
            $offerNode->itemprop  = 'offers';
        }

        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();

        if ($currency_code) {
            $offerNode->innertext = $offerNode->innertext .
                    "<meta itemprop='priceCurrency' content='{$currency_code}' />";
        }
        return true;
    }

    protected function _checkBlockType()
    {
        if (!($this->_block instanceof $this->_validBlockType)) {
            throw new Exception(Mage::helper('seosuite/richsnippet')->__('Richsnippets: wrong block type for price.'));
        }
    }

    protected function _afterRender()
    {
        $report = new Varien_Object(array('status' => 'success'));
        Mage::register('mageworx_richsnippet_price_report', $report, true);
        return parent::_afterRender();
    }

}
