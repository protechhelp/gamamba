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
 * @package    MageWorx_SearchAutocomplete
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SeoSuite_Model_Catalog_Product_Attribute_Source_Meta_Robots extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions(){
        if (!$this->_options) {
          $this->_options = array(
                array('value' => '', 'label' => 'Use Config'),
                array('value' => 'INDEX, FOLLOW', 'label' => 'INDEX, FOLLOW'),
                array('value' => 'INDEX, NOFOLLOW', 'label' => 'INDEX, NOFOLLOW'),
                array('value' => 'NOINDEX, FOLLOW', 'label' => 'NOINDEX, FOLLOW'),
                array('value' => 'NOINDEX, NOFOLLOW', 'label' => 'NOINDEX, NOFOLLOW'),
                array('value' => 'INDEX, FOLLOW, NOARCHIVE', 'label' => 'INDEX, FOLLOW, NOARCHIVE'),
                array('value' => 'INDEX, NOFOLLOW, NOARCHIVE', 'label' => 'INDEX, NOFOLLOW, NOARCHIVE'),
                array('value' => 'NOINDEX, NOFOLLOW, NOARCHIVE', 'label' => 'NOINDEX, NOFOLLOW, NOARCHIVE'),
          );
        }
        return $this->_options;
    }
}