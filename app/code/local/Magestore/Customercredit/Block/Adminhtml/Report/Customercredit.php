<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customercredit Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Adminhtml_Report_Customercredit extends Magestore_Customercredit_Block_Adminhtml_Report_Graph
{
    public function __construct(){
        $currency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$this->_google_chart_params = array(
			'cht'  => 'lc',
			'chf'  => 'bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0',
			'chdl' => $this->__('Spend credit').'|'.$this->__('Received credit'),
			'chco' => '2424ff,db4814',
            'chxt' => 'x,y,y',
            'chxlexpend' => '|2:||'.$this->__('# Credit('.$currency.')')
		);
		
		$this->setHtmlId('customer-credit');
        parent::__construct();
    }
    
    protected function _prepareData(){
    	$this->setDataHelperName('customercredit/report_customercredit');
    	$this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
    	$data = $this->setDataRows(array('spent_credit','received_credit'));
    	$this->_axisMaps = array(
    		'x'	=> 'range',
                'y' => 'received_credit'
    	);
    	parent::_prepareData();
    }
    
    public function getCommentContent(){
        return $this->__('This report shows the <b>spend Credit </b> and <b>reveived Credit</b> of Customer');
    }
}