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
class Magestore_Customercredit_Block_Adminhtml_Customercredit_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('customercreditGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Customercredit_Block_Adminhtml_Customercredit_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToSelect('email')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('group_id')
                ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
                ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
                ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
                ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
                ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
                ->joinAttribute('credit_value', 'customer/credit_value', 'entity_id', null, 'left');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'number',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150',
            'index' => 'email',
            'renderer' => 'customercredit/adminhtml_customer_renderer_customer'
        ));

        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $this->addColumn('credit_value', array(
            'header' => Mage::helper('customer')->__('Credit Balance'),
            'width' => '100',
            'align' => 'right',
            'currency_code' => $currency,
            'index' => 'credit_value',
            'type' => 'price',
            'renderer' => 'customercredit/adminhtml_customer_renderer_customerprice',
        ));
        $groups = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionHash();

        $this->addColumn('group', array(
            'header' => Mage::helper('customer')->__('Group'),
            'width' => '100',
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
        ));

        $this->addColumn('Telephone', array(
            'header' => Mage::helper('customer')->__('Telephone'),
            'width' => '100',
            'index' => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header' => Mage::helper('customer')->__('ZIP'),
            'width' => '90',
            'index' => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header' => Mage::helper('customer')->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header' => Mage::helper('customer')->__('State/Province'),
            'width' => '100',
            'index' => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header' => Mage::helper('customer')->__('Customer Since'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('customer')->__('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_id',
            ));
        }

        $this->addColumn('action', array(
            'header' => Mage::helper('customer')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('customer')->__('Edit'),
                    'url' => array('base' => 'adminhtml/customer/edit/back/edit/tab/customer_info_tabs_credit_balance'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/index', array('_current' => true));
    }

    public function getRowUrl($row) {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/customer/edit/back/edit/tab/customer_info_tabs_credit_balance', array('id' => $row->getId()));
    }

    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $data = array();
		$data[] = '"'.Mage::helper('customercredit')->__('ID').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Name').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Email').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Credit Balance').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Group').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Telephone').'"';		 
		$data[] = '"'.Mage::helper('customercredit')->__('ZIP').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Country').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('State/Province').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Customer Since').'"';
		$data[] = '"'.Mage::helper('customercredit')->__('Website').'"';
        $csv.= implode(',', $data)."\n";

        foreach ($this->getCollection() as $item) {           
			$data =  Mage::helper('customercredit')->getValueToCsv($item);
            $csv.= $data ."\n";			
        }					
        return $csv;
    }
}