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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
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
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_SeoSuite_Model_Mysql4_Report_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {        
        $this->_init('seosuite/report_product');
    }
    
    public function addFieldToFilter($field, $condition=null) {
        if ($field=='meta_title_error') {
            if ($condition=='missing') {
                $field = 'prepared_meta_title';
                $condition = array('eq' => '');
            } elseif ($condition=='long') {
                $field = 'meta_title_len';
                $condition = array('gt' => '70');
            } elseif ($condition=='duplicate') {
                $field = 'meta_title_dupl';
                $condition = array('gt' => '1');
            }
        } elseif ($field=='name_error') {
            if ($condition=='duplicate') {
                $field = 'name_dupl';
                $condition = array('gt' => '1');
            }
        } elseif ($field=='meta_descr_error') {
            if ($condition=='missing') {
                $field = 'meta_descr_len';
                $condition = array('eq' => '0');
            } elseif ($condition=='long') {
                $field = 'meta_descr_len';
                $condition = array('gt' => '150');
            }
        }
        return parent::addFieldToFilter($field, $condition);                
    }
    
}