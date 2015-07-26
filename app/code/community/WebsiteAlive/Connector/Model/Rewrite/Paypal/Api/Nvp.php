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
class WebsiteAlive_Connector_Model_Rewrite_Paypal_Api_Nvp extends Mage_Paypal_Model_Api_Nvp {
	
    public function getButtonSourceEc(){
    	return $this->getBuildNotationCode();
    }

    public function getButtonSourceDp(){
    	return $this->getBuildNotationCode();
    }
    
    //Important build code update, for Mage_Paypal 1.4 -
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