/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2012 Skrill Holdings Ltd. (http://www.skrill.com)
 */

Event.observe(window, 'load', function(){
	if (typeof isMoneybookersCcApiEnabled != 'undefined' && isMoneybookersCcApiEnabled) {
        if (typeof payment != 'undefined' && typeof payment.save != 'undefined') {
            moneybookerspspPaymentWrap();
        }
    }
});

function enableElements(elements) {
	for (var i=0; i<elements.length; i++) {
    	elements[i].disabled = false;
	}
}


function moneybookerspspPaymentWrap() {
    payment.moneybookersPspFormId = 'form_placeholder_moneybookerspsp_cc';
	payment.save = payment.save.wrap(function(origSaveMethod){
        var moneybookersMethods = ['moneybookerspsp_cc', 'moneybookerspsp_elv'];
        var isChecked = false;
        for (var key in moneybookersMethods){
            var elementId = 'p_method_'+moneybookersMethods[key];
            if ($(elementId) && $(elementId).checked){
                isChecked = true;
                this.moneybookersPspFormId = 'form_placeholder_'+moneybookersMethods[key];
                break;
            }
        }
		if (isChecked) {
			if (checkout.loadWaiting!=false) return;
            if (this.isMoneybookersPspDataValid[this.currentMethod] == true){
                origSaveMethod();
            }else{
                checkout.setLoadWaiting('payment');
                var url = $(this.moneybookersPspFormId).src.split('#');
                $(this.moneybookersPspFormId).src = url[0]+'#save';
            }
		} else {
			origSaveMethod();
		}
	});
}