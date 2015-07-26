<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.harapartners.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@harapartners.com so we can send you a copy immediately.
 */

class WebsiteAlive_Connector_Model_Source_Website {

    public function toOptionArray() {
//    	$resultArary = array(array('value' => "", 'label'=>"-- Please Select --"));
		$resultArary = array();
    	$availableSites = self::_getAvailableWebsites();
        foreach($availableSites as $availableSite){
        	if(isset($availableSite['websiteid']) && isset($availableSite['title'])){
        		$resultArary[] = array('value' => $availableSite['websiteid'], 'label' => $availableSite['title']);
        	}
        }
        return $resultArary;
    }

    public function toArray(){
//    	$resultArary = array("" => "-- Please Select --");
    	$resultArary = array();
    	$availableSites = self::_getAvailableWebsites();
        foreach($availableSites as $availableSite){
        	if(isset($availableSite['websiteid']) && isset($availableSite['title'])){
        		$resultArary[$availableSite['websiteid']] = $availableSite['title'];
        	}
        }
        return $resultArary;
    }
    
    public static function generateHtml($websiteResultJson = null){
//    	$htmlContent = "<option selected=\"selected\" value=\"\">-- Please Select --</option>";
    	$htmlContent = "";
    	$availableSites = self::_getAvailableWebsites($websiteResultJson);
        foreach($availableSites as $availableSite){
        	if(isset($availableSite['websiteid']) && isset($availableSite['title'])){
        		$htmlContent .= "<option value=\"{$availableSite['websiteid']}\">{$availableSite['title']}</option>";
        	}
        }
        return $htmlContent;
    }
    
    protected static function _getAvailableWebsites($websiteResultJson = null){
    	if(!!$websiteResultJson){
    		$availableSites = json_decode($websiteResultJson, 1);
    	}else{
    		$availableSites = json_decode(Mage::getStoreConfig('waconnector/general/available_website_json'), 1);
    	}
    	if(!$availableSites){
    		$availableSites = array();
    	}
    	return $availableSites;
    }

}
