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

class WebsiteAlive_Connector_Block_Connector extends Mage_Core_Block_Template {
	
	protected function _toHtml() {
		$html = "";
		$server = Mage::getStoreConfig('waconnector/general/server');
		$websiteId = Mage::getStoreConfig('waconnector/general/website_id');
		$groupId = Mage::getStoreConfig('waconnector/general/group_id');
		if(Mage::getStoreConfig('waconnector/general/is_enabled')
				&& $groupId
				&& $server
		){
			$html .= <<<HTML_CONTENT
<!-- Start WebsiteAlive Embedded Icon/Tracking Code -->
<script type="text/javascript">
function wsa_include_js(){
var wsa_host = (("https:" == document.location.protocol) ? "https://" : "http://");
var js = document.createElement('script');
js.setAttribute('language', 'javascript');
js.setAttribute('type', 'text/javascript');
js.setAttribute('src',wsa_host + 'tracking.websitealive.com/vTracker_v2.asp?objectref=$server&websiteid=$websiteId&groupid=$groupId');
document.getElementsByTagName('head').item(0).appendChild(js);
}

if (window.attachEvent) {window.attachEvent('onload', wsa_include_js);}
else if (window.addEventListener) {window.addEventListener('load', wsa_include_js, false);}
else {document.addEventListener('load', wsa_include_js, false);}
</script>
<!-- End WebsiteAlive Embedded Icon/Tracking Code --> 	
HTML_CONTENT;
		}
		
        return $html;
    }
    
}