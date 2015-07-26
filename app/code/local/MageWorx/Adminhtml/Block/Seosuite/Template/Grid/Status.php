<?php

class MageWorx_Adminhtml_Block_Seosuite_Template_Grid_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
{
    
    public function getStoreId() {
        $store = $this->getRequest()->getParam('store');
        if(!$store) {
            $store = 0;
        }
        return $store;
    }
    
    public function render(Varien_Object $row)
    {
        $url = $this->getUrl("*/*/changeStatus",array('template_id'=>$row->getId()));
        $status = $this->__('Enable');
        if($row->getStatus())
        {
           $status = $this->__('Disable'); 
        }
        $value = '<a href="'.$url.'">'.$status."</a>";
        return $value;
    }
}