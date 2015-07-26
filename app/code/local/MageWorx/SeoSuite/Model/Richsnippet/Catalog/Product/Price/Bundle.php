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
class MageWorx_Seosuite_Model_Richsnippet_Catalog_Product_Price_Bundle extends
      MageWorx_SeoSuite_Model_Richsnippet_Catalog_Product_Price_Abstract{

    protected function _getItemValues($_product = null)
    {
//        Mage_Bundle_Block_Catalog_Product_Price
        $prices = array();
        $_priceModel  = $this->_product->getPriceModel();
        list($_minimalPriceTax, $_maximalPriceTax) = $_priceModel->getTotalPrices($this->_product, null, null, false);
        list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_priceModel->getTotalPrices($this->_product, null, true, false);

        $_weeeTaxAmount = 0;

        if ($this->_product->getPriceType() == 1) {
            $_weeeTaxAmount = Mage::helper('weee')->getAmount($this->_product);
            $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
            if (Mage::helper('weee')->isTaxable()) {
                $_attributes = Mage::helper('weee')->getProductWeeeAttributesForRenderer($this->_product, null, null, null, true);
                $_weeeTaxAmountInclTaxes = Mage::helper('weee')->getAmountInclTaxes($_attributes);
            }
            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($this->_product, array(0, 1, 4))) {
                $_minimalPriceTax += $_weeeTaxAmount;
                $_minimalPriceInclTax += $_weeeTaxAmountInclTaxes;
            }
            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($this->_product, 2)) {
                $_minimalPriceInclTax += $_weeeTaxAmountInclTaxes;
            }
        }

        if ($this->_product->getPriceView()):
            if ($this->_displayBothPrices()):
                    $prices[] = $_minimalPriceInclTax;
                    $prices[] = $_minimalPriceTax;
            else:
                $prices[] = $_minimalPriceTax;
                if (Mage::helper('weee')->typeOfDisplay($this->_product, 2) && $_weeeTaxAmount):
                    $prices[] = $_minimalPriceInclTax;
                endif;
            endif;
        else:
            if ($_minimalPriceTax <> $_maximalPriceTax):
                if ($this->_displayBothPrices()):
                    $prices[] = $_minimalPriceInclTax;
                    $prices[] = $_minimalPriceTax;
                else:
                    $prices[] = $_minimalPriceTax;
                    if (Mage::helper('weee')->typeOfDisplay($this->_product, 2) && $_weeeTaxAmount):
                        $prices[] = $_minimalPriceInclTax;
                    endif;
                endif;

                   if ($this->_product->getPriceType() == 1) {
                       if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($this->_product, array(0, 1, 4))) {
                           $_maximalPriceTax += $_weeeTaxAmount;
                           $_maximalPriceInclTax += $_weeeTaxAmountInclTaxes;
                       }
                       if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($this->_product, 2)) {
                           $_maximalPriceInclTax += $_weeeTaxAmountInclTaxes;
                       }
                   }

                    if ($this->_displayBothPrices()):
                            $prices[] = $_maximalPriceInclTax;
                            $prices[] = $_maximalPriceTax;
                    else:
                        $prices[] = $_maximalPriceTax;
                        if (Mage::helper('weee')->typeOfDisplay($this->_product, 2) && $_weeeTaxAmount):
                            $prices[] = $_maximalPriceInclTax;
                        endif;
                    endif;
            else:
                if ($this->_displayBothPrices()):
                        $prices[] = $_minimalPriceInclTax;
                        $prices[] = $_minimalPriceTax;
                else:
                    $prices[] = $_minimalPriceTax;
                    if (Mage::helper('weee')->typeOfDisplay($this->_product, 2) && $_weeeTaxAmount):
                        $prices[] = $_minimalPriceInclTax;
                    endif;
                endif;
            endif;
        endif;

        $modPrices = array();
        foreach($prices as $price){
            $modPrices = array_merge($modPrices, $this->_getModifyPrices($price));
        }

        return array_unique($modPrices);
    }


    protected function _displayBothPrices()
    {
        if ($this->_product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC &&
            $this->_product->getPriceModel()->getIsPricesCalculatedByIndex() !== false) {
            return false;
        }
        return Mage::helper('tax')->displayBothPrices();
    }

/*
    protected function _getItemValues()
    {
        if ($this->_product->getTypeInstance(true) instanceof Mage_Bundle_Model_Product_Type) {
            $priceModel = $this->_product->getPriceModel();
            $pricesInclTax = $priceModel->getTotalPrices($this->_product, null, true, false);
            $pricesExclTax = $priceModel->getTotalPrices($this->_product, null, null, false);

            $prices = array();
            $prices[] = $pricesInclTax[0];
            $prices[] = $pricesExclTax[0];

            $resultPrices = array();
            foreach($prices as $price){
                $resultPrices[] = $price;
                $resultPrices = array_merge($resultPrices, $this->_getModifyPrices($price));
            }
            return $resultPrices;
        }
    }
 *
 */
}