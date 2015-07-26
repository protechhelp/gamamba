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

class WebsiteAlive_Connector_Block_Adminhtml_System_Config_Login extends Mage_Adminhtml_Block_System_Config_Form_Field {
	
    protected function _toHtml() {
    	$htmlId = $this->getHtmlId();
    	$ajaxUrl = $this->getAjaxUrl();
    	$buttonLabel = $this->escapeHtml($this->getButtonLabel());
    	
		$htmlContent = <<< HTML_CONTENT
<script type="text/javascript">
    function ajaxLogin() {
        var elem = $('$htmlId');
        
        params = {
            username: $('waconnector_general_username').value,
            password: $('waconnector_general_password').value
        };

        new Ajax.Request('$ajaxUrl', {
            parameters: params,
            onSuccess: function(response) {
                result = 'Login failed!';
                try {
                    response = JSON.parse(response.responseText);
                    if (response.status == 1) {
                        result = 'Successful! Please select website...';
                        $('waconnector_general_website_id').update(response.website_option_html);
                        elem.removeClassName('fail').addClassName('success');
                    } else {
                        elem.removeClassName('success').addClassName('fail');
                    }
                } catch (e) {
                    elem.removeClassName('success').addClassName('fail');
                }
                $('ajax_login_result').update(result);
            }
        });
    }
</script>
<button onclick="javascript:ajaxLogin(); return false;" class="scalable" type="button" id="$htmlId">
    <span><span><span id="ajax_login_result">$buttonLabel</span></span></span>
</button>
HTML_CONTENT;
    	
    	return $htmlContent;
    }

    public function render(Varien_Data_Form_Element_Abstract $element){
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('waconnector')->__($originalData['button_label']),
            'html_id' => $element->getHtmlId(),
            'ajax_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/system_config_ajax/login')
        ));

        return $this->_toHtml();
    }
}