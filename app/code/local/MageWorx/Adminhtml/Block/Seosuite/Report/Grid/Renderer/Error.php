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

class MageWorx_Adminhtml_Block_Seosuite_Report_Grid_Renderer_Error extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    
    public function render(Varien_Object $row) {
        
        $index = $this->getColumn()->getIndex();
        $values = Mage::registry('error_types');
        $errors = array();
        
        switch ($index) {
            case 'meta_title_error':                
                if ($row->getData('prepared_meta_title')=='' && isset($values['missing'])) $errors[] = $this->htmlEscape($values['missing']);
                if ($row->getData('meta_title_len')>70  && isset($values['long'])) $errors[] = $this->htmlEscape($values['long'].' ('.$row->getData('meta_title_len').')');
                if ($row->getData('meta_title_dupl')>1  && isset($values['duplicate'])) $errors[] = $this->htmlEscape($values['duplicate']).' (<a href="'.$this->getUrl('*/*/duplicateView/', array('prepared_meta_title'=>$row->getData('prepared_meta_title'), 'store'=>$row->getData('store_id'))).'" title="'.$this->__('View Duplicates').'">'.$row->getData('meta_title_dupl').'</a>)';
                    
                break;
            case 'name_error':
                if ($row->getData('name_dupl')>1 && isset($values['duplicate'])) $errors[] = $this->htmlEscape($values['duplicate']).' (<a href="'.$this->getUrl('*/*/duplicateView/', array('prepared_name'=>$row->getData('prepared_name'), 'store'=>$row->getData('store_id'))).'" title="'.$this->__('View Duplicates').'">'.$row->getData('name_dupl').'</a>)';
                break;
            case 'meta_descr_error':
                if ($row->getData('meta_descr_len')==0 && isset($values['missing'])) $errors[] = $this->htmlEscape($values['missing']);
                if ($row->getData('meta_descr_len')>150  && isset($values['long'])) $errors[] = $this->htmlEscape($values['long'].' ('.$row->getData('meta_descr_len').')');
                break;                
            case 'heading_error':
                if ($row->getData('prepared_heading')=='' && isset($values['missing'])) $errors[] = $this->htmlEscape($values['missing']);
                if ($row->getData('heading_dupl')>1  && isset($values['duplicate'])) $errors[] = $this->htmlEscape($values['duplicate']).' (<a href="'.$this->getUrl('*/*/duplicateView/', array('prepared_heading'=>$row->getData('prepared_heading'), 'store'=>$row->getData('store_id'))).'" title="'.$this->__('View Duplicates').'">'.$row->getData('heading_dupl').'</a>)';
                break;
        }
        
        return implode('<br/>', $errors);
    }
}
