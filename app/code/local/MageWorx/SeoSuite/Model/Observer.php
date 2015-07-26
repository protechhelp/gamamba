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

class MageWorx_SeoSuite_Model_Observer {

    public function prepareAttributeEditForm($observer) {
        $helper = Mage::helper('seosuite');
        $form = $observer->getEvent()->getForm();
        
        $fieldset = $form->getElements()->searchById('front_fieldset');
        if (!is_null($fieldset)) {
            $fieldset->addField('layered_navigation_canonical', 'select', array(
                'name' => 'layered_navigation_canonical',
                'label' => $helper->__('Canonical Tag for Pages Filtered by Layered Navigation Leads to'),
                'title' => $helper->__('Canonical Tag for Pages Filtered by Layered Navigation Leads to'),
                'values' => Mage::getModel('seosuite/system_config_source_layer_canonical')->toOptionArray(),
                    ), 'is_filterable_in_search');
        }
        return $this;
    }
    
    public function setMetaDescription(Varien_Event_Observer $observer) {
        $shortDescription = trim($observer->getEvent()->getProduct()->getShortDescription());
        if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/product_meta_description') && !empty($shortDescription)) {
            Mage::getSingleton('catalog/session')->setData('seosuite_meta_description', strip_tags($shortDescription));
        }
    }

    public function registerProductId(Varien_Event_Observer $observer) {
        $product = $observer->getEvent()->getProduct();
        Mage::unregister('seosuite_product_id');
        if ($product) Mage::register('seosuite_product_id', $product->getId());
    }

    
    public function redirectHome(Varien_Event_Observer $observer) {
        $front = $observer->getEvent()->getFront();
        $origUri = $front->getRequest()->getRequestUri();
        $origUri = explode('?', $origUri, 2);
        $uri = preg_replace('~(?:index\.php/+home/*|index\.php/*|(/)+home/*)$~i', '', $origUri[0]);
       // echo "<pre>"; print_r($origUri);
        //if ($uri=='/') return ; // fix Vladimir Z.
        if (strpos($origUri[0], '/downloader/index.php') !== false) {
            return;
        }
        if ($uri == $origUri[0]){
            return;
        }
        $uri = rtrim($uri, '/') . '/';
        $uri .= ( (isset($origUri[1]) && $origUri[1]!=="___SID=U") ? '?' . $origUri[1] : '');
        $front->getResponse()
                ->setRedirect($uri)
                ->setHttpResponseCode(301)
                ->sendResponse();
        exit;
    }
    
    public function productSaveAfter(Varien_Event_Observer $observer) {
        if (Mage::helper('seosuite')->getProductReportStatus()) Mage::helper('seosuite')->setProductReportStatus(0);
    }
    public function categorySaveAfter(Varien_Event_Observer $observer) {
        if (Mage::helper('seosuite')->getCategoryReportStatus()) Mage::helper('seosuite')->setCategoryReportStatus(0);
    }
    public function cmsPageSaveAfter(Varien_Event_Observer $observer) {
        if (Mage::helper('seosuite')->getCmsReportStatus()) Mage::helper('seosuite')->setCmsReportStatus(0);
    }
    public function addJsToAttribute(Varien_Event_Observer $observer) 
    {
        $form = $observer->getEvent()->getForm();
        $eventElem = $form->getElement('canonical_url');
        $html = "<div style='padding-top:5px;'>
                    <input type='text' value='' style='display:none; width:275px' name='canonical_url_custom' id='canonical_url_custom'>
                </div>\n
            <script type='text/javascript'>
            function listenCU() {    
                if($('canonical_url').value=='custom') {
                    $('canonical_url_custom').show();
                }
                else {
                    $('canonical_url_custom').hide();
                }
           }
           $('canonical_url').observe('change',listenCU);
                </script>";
        if ($eventElem) {
               $eventElem->setAfterElementHtml($html);
       }

    }
}