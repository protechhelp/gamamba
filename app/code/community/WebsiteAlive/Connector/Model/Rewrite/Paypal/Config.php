<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.harapartners.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@harapartners.com so we can send you a copy immediately.
 * 
 *
 */
class WebsiteAlive_Connector_Model_Rewrite_Paypal_Config extends Mage_Paypal_Model_Config {
	
	protected $_isStagingMode = null;
	
	/* Compatibility for Staging server SSL verification */
	public function __get($key){
		if(strcmp($key, 'verifyPeer') == 0 || strcmp($key, 'verify_peer') == 0){
			if($this->_isStagingMode === null){
				$this->_isStagingMode = 0;
				try{
					$curlResource = curl_init("https://www.paypal.com/");
					curl_setopt($curlResource, CURLOPT_TIMEOUT, 3);
					curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, false);
					curl_setopt($curlResource, CURLOPT_SSL_VERIFYPEER, true);
					curl_exec($curlResource);
					$curlError = curl_error($curlResource);
					curl_close($curlResource);
					if(!!$curlError){
						$this->_isStagingMode = 1;
					}
				}catch (Exception $ex){
					$this->_isStagingMode = 1;
				}
			}
			if($this->_isStagingMode == 1){
				return 0;
			}
		}
		return parent::__get($key);
    }
	
	public function getBuildNotationCode($countryCode = null){
		if($this->_isModuleActive('Enterprise_Enterprise')){
			return 'Hara_SI_MagentoEE_PPA';
		}else{
			return 'Hara_SI_MagentoCE_PPA';
		}
	}
	private function _isModuleActive($code) {
        $module = Mage::getConfig()->getNode("modules/$code");
        $model = Mage::getConfig()->getNode("global/models/$code");
        return $module && $module->is('active') || $model;
    }
    
}