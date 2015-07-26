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
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_SeoSuite_Model_Template_Adapter_Abstract extends Mage_Core_Model_Abstract
{
    protected $_defaultStore;
    
    public function setModel($model)
    {
        $this->setData('current_model',$model);
        return $this;
    }
    
    public function apply($from,$limit,$template,$attribute_name)
    {
        return $this;
    }
    
    protected function _getStoreIdByIndex($stores, $current) {
        $storeKeys = array_keys($stores);
        $storeId = null;
        if (isset($storeKeys[$current])) {
            $store = $stores[$storeKeys[$current]];
            $storeId = $store->getId();
        }
        return $storeId;
    }
}