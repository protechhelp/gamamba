<?php
/*------------------------------------------------------------------------
 # SM Shop By - Version 1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

include_once 'Mage/CatalogSearch/controllers/ResultController.php';
class Sm_Shopby_ResultController extends Mage_CatalogSearch_ResultController{

    public function indexAction(){
        $query = Mage::helper('catalogsearch')->getQuery();

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity() + 1);
                } else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()) {
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                } else {
                    $query->prepare();
                }
            }

            Mage::helper('catalogsearch')->checkNotes();

            $this->loadLayout();
            if ($this->getRequest()->isAjax()) {
                $update = $this->getLayout()->getUpdate();
                $update->addHandle('catalog_category_layered_ajax_layer');
            }
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');

            if ($this->getRequest()->isAjax()) {
                $listing = $this->getLayout()->getBlock('search_result_list')->toHtml();
                $layer = $this->getLayout()->getBlock('catalogsearch.leftnav')->toHtml();

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

            if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        } else {
            $this->_redirectReferer();
        }
    }

}