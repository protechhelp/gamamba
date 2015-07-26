<?php
class AW_Helpdesk3_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Help Desk'));
        $this->renderLayout();
    }
}
