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

class WebsiteAlive_Connector_Model_Observer {
	
	public function addBlockBeforeRenderLayout(){
		$layout = Mage::getSingleton('core/layout');
		$targetBlock = $layout->getBlock('before_body_end');
		//Special Ajax calls may not have 'before_body_end' block, i.e. onepage checkout progress block
		if(!!$targetBlock){
			$targetBlock->append($layout->createBlock('waconnector/connector'));
		}
	}
	
}