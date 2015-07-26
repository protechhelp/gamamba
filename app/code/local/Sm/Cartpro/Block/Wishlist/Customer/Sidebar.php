<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist sidebar block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Sm_Cartpro_Block_Wishlist_Customer_Sidebar extends Mage_Wishlist_Block_Customer_Sidebar
{


    /**
     * Prepare before to html
     *
     * @return string
     */
    protected function _toHtml()
    {
		if(version_compare(Mage::getVersion(),'1.4.0.1','>=')){
			if ($this->_getHelper()->hasItems()) {
				return parent::_toHtml();
			}
			//return ''; //default return '' va` gay ra hien tuong la mat box mini wishlist duoi box minicart, va khi add wishlist se ko tim thay box nay ,nen o duoi' ta da return ve 1 box mini wishlist va dat o che do hidden neu ko co item nao trong wishlist
			return '<div class="block block-wishlist" style="display:none;"><div class="block-title"><strong><span>My Wishlist <small>(0)</small></span></strong></div><div class="block-content"><p class="block-subtitle">Last Added Items</p><p class="empty">You have no items in your wishlist.</p></div></div>';
		}else{
			if( sizeof($this->getWishlistItems()->getItems()) > 0 ){
				return parent::_toHtml();
			}
			return '<div class="box base-mini mini-wishlist" style="display:none;"><div class="block-title"><strong><span>My Wishlist <small>(0)</small></span></strong></div><div class="block-content"><p class="block-subtitle">Last Added Items</p><p class="empty">You have no items in your wishlist.</p></div></div>';
		}
        
    }


}
