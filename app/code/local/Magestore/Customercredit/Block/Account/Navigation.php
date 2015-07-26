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
 * Customercredit Navigation Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
	protected $_navigation_title = '';
	
	public function setNavigationTitle($title){
		$this->_navigation_title = $title;
		return $this;
	}
	
	public function getNavigationTitle(){
		return $this->_navigation_title;
	}
	
	public function addLink($name, $path, $label, $disabled = false, $order = 0, $urlParams=array())
    {
    	if (isset($this->_links[$order])) $order++;

    	$link = new Varien_Object(array(
            'name' 		=> $name,
            'path' 		=> $path,
            'label' 	=> $label,
            'disabled'	=> $disabled,
            'order'		=> $order,
            'url' 		=> $this->getUrl($path, $urlParams),
        ));

        Mage::dispatchEvent('customercredit_account_navigation_add_link',array(
        	'block'		=> $this,
        	'link'		=> $link,
        ));

        $this->_links[$order] = $link;
        return $this;
    }

	public function getLinks(){
		$links = new Varien_Object(array(
			'links'	=> $this->_links,
		));

		Mage::dispatchEvent('customercredit_account_navigation_get_links',array(
			'block'		=> $this,
			'links_obj'	=> $links,
		));
//
		$this->_links = $links->getLinks();

		ksort($this->_links);

		return $this->_links;
	}

	public function isActive($link){
		if (parent::isActive($link)) return true;
    		return false;
	}
}