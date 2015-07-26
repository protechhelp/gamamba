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

class WebsiteAlive_Connector_Adminhtml_System_Config_AjaxController extends Mage_Adminhtml_Controller_Action {

    public function loginAction() {
    	     
		try{
			$username = $this->getRequest()->getParam('username');
    		$password = $this->getRequest()->getParam('password');
    		//Check encrypted password
			if (preg_match('/^\*+$/', $password)) {
	            $password = Mage::helper('core')->decrypt(Mage::getStoreConfig('waconnector/general/password'));
	        }
    		$loginRequestUrl = "https://api-v1.websitealive.com/auth/?action=login_validate&user_name=$username&password=$password";
			$loginRequestCurl = curl_init();
			curl_setopt($loginRequestCurl, CURLOPT_URL, $loginRequestUrl);
			curl_setopt($loginRequestCurl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($loginRequestCurl, CURLOPT_RETURNTRANSFER, 1);
			$loginResultData = json_decode(curl_exec($loginRequestCurl), 1);
			curl_close($loginRequestCurl);
			
			if(empty($loginResultData[0]['objectref']) || empty($loginResultData[0]['groupid'])){
				throw new Exception('Invalid login.');
			}
			
			$coreConfigData = Mage::getModel('core/config_data')->load('waconnector/general/server', 'path');
			$coreConfigData->setPath('waconnector/general/server'); //In case of new save
			$coreConfigData->setValue($loginResultData[0]['objectref']);
			$coreConfigData->save();
			
			$coreConfigData = Mage::getModel('core/config_data')->load('waconnector/general/group_id', 'path');
			$coreConfigData->setPath('waconnector/general/group_id'); //In case of new save
			$coreConfigData->setValue($loginResultData[0]['groupid']);
			$coreConfigData->save();
			
			$websiteRequestUrl = "https://api-v1.websitealive.com/org/?action=getwebsites&objectref={$loginResultData[0]['objectref']}&groupid={$loginResultData[0]['groupid']}";
			$websiteRequestCurl = curl_init();
			curl_setopt($websiteRequestCurl, CURLOPT_URL, $websiteRequestUrl);
			curl_setopt($websiteRequestCurl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($websiteRequestCurl, CURLOPT_RETURNTRANSFER, 1);
			$websiteResultJson = curl_exec($websiteRequestCurl);
			curl_close($websiteRequestCurl);
			
			$coreConfigData = Mage::getModel('core/config_data')->load('waconnector/general/available_website_json', 'path');
			$coreConfigData->setPath('waconnector/general/available_website_json'); //In case of new save
			$coreConfigData->setValue($websiteResultJson);
			$coreConfigData->save();
			
		}catch(Exception $e){
			echo json_encode(array('status' => 0)); //Json error
			exit;
		}
		//Need to send $websiteResultJson, the store config is still the cache value, not our new value 
		echo json_encode(array(
				'status' => 1, 
				'website_option_html' => WebsiteAlive_Connector_Model_Source_Website::generateHtml($websiteResultJson)
		)); //Json success
		exit;
    }
    
}