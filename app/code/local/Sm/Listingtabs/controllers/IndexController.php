<?php

/*------------------------------------------------------------------------
 # SM Listing Tabs- Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_ListingTabs_IndexController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxAction()
    {
        $helper = Mage::helper('listingtabs/data');
        $isAjax = Mage::app()->getRequest()->isAjax();

        if ($isAjax) {
            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('listingtabs_index_ajax'); //load the layout you defined in layout xml file
            $layout->generateXml();
            $layout->generateBlocks();
            $output = $layout->getOutput();
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('items_markup' => $output)));
            //$this->getResponse()->setHeader('Content-type', 'text/html');
            //$this->getResponse()->setBody($output);
        }
    }


}