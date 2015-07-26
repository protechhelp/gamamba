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
class MageWorx_SeoSuite_Helper_Richsnippet extends Mage_Core_Helper_Abstract
{

    const XML_PATH_RICHSNIPPET_ENABLED = 'mageworx_seo/seosuite/enable_rich_snippets';

    function isRichsnippetEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_RICHSNIPPET_ENABLED);
    }

    function isProductPage()
    {
        if (strpos(Mage::app()->getRequest()->getRequestUri(), 'catalog/product/view') === false) {
            return false;
        }
        return true;
    }

}
