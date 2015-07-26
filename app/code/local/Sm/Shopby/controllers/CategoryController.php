<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

include_once 'Mage/Catalog/controllers/CategoryController.php';
class Sm_Shopby_CategoryController extends Mage_Catalog_CategoryController{

    public function viewAction(){
        $this->loadLayout();
        if (($category = $this->_initCatagory())) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());

            if ($this->getRequest()->isAjax()) {
                $update->addHandle('catalog_category_layered_ajax_layer');
            }
            $this->loadLayoutUpdates();

            if (($layoutUpdates = $settings->getLayoutUpdates())) {
                if (is_array($layoutUpdates)) {
                    foreach ($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();

            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if (($root = $this->getLayout()->getBlock('root'))) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $params = $this->getRequest()->getParams();
            if ($this->getRequest()->isAjax() && !isset($params['p'])) {
                $listing = $this->getLayout()->getBlock('product_list')->toHtml();
                $layer = $this->getLayout()->getBlock('catalog.leftnav')->toHtml();

                $urlModel = Mage::getSingleton('core/url');
                $listing = $urlModel->sessionUrlVar($listing);
                $layer = $urlModel->sessionUrlVar($layer);

                $response = array(
                    'listing' => $listing,
                    'layer' => $layer
                );

                $this->getResponse()->setHeader('Content-Type', 'application/json', true);
                $this->getResponse()->setBody(json_encode($response));
            } else {
                $this->renderLayout();
            }
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }

}