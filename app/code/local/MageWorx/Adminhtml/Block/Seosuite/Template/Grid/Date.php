<?php

class MageWorx_Adminhtml_Block_Seosuite_Template_Grid_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
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
        $value = '---';
        $list = array();
        if(Mage::registry('seo_grid_date'))
        {
            $list = Mage::registry('seo_grid_date');
        } 
        else {
            $collection = Mage::getResourceModel('seosuite/template_store_collection');
            foreach ($collection as $item) {
                if(!isset($list[$item->getStoreId()])) {
                    $list[$item->getStoreId()] = array();
                }
                $list[$item->getStoreId()][$item->getTemplateId()] = $item->getLastUpdate();
            }
            
            Mage::register('seo_grid_date',$list,true);
        }
        if(isset($list[$this->getStoreId()][$row->getTemplateId()])) {
            $value = $list[$this->getStoreId()][$row->getTemplateId()];
        }
        
        return $value;
    }
}