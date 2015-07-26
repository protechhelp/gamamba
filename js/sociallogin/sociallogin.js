/**
* Apptha
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.apptha.com/LICENSE.txt
*
* ==============================================================
*                 MAGENTO EDITION USAGE NOTICE
* ==============================================================
* This package designed for Magento COMMUNITY edition
* Apptha does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* Apptha does not provide extension support in case of
* incorrect edition usage.
* ==============================================================
*
* @category    Apptha
* @package     Apptha_Sociallogin
* @version     0.1.8
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
* 
* */

/**
 * global window
 */

document.observe("dom:loaded", function() { 
    var i; 
    try {
    	
    	/**
		 * Get all href links
		 */
        var links = document.links;

        for (i = 0; i < links.length; i++) {
            
        	/**
    		 * login links
    		 */
        	if (links[i].href.search('/customer/account/login/') != -1 && links[i].href.indexOf("#") == -1 ) {
                links[i].href = 'javascript:apptha_sociallogin();';
            }
           
            /**
    		 * wishlist link 
    		 */
           
            
            /**
    		 * my account link 
    		 */
           
            
            /**
    		 * background fade element.
    		 */
            if ($('bg_fade') == null) {
                var screen = new Element('div', {'id': 'bg_fade'});
                document.body.appendChild(screen);
            }
        }

        if( window.location.href.search('/customer/account/login/') != -1 ||  window.location.href.search('/customer/account/create/') != -1 )
        	{
        	apptha_sociallogin();
        	}
        /**
		 * bind in checkout field.
		 */
        if (document.getElementById("checkout-step-login"))
        {
            $$('.col-2 .buttons-set').each(function(e) {
                e.insert({bottom: '<div id="multilogin"> <button type="button" class="button" style="" onclick="javascript:apptha_sociallogin();" title="Social Login" name="headerboxLink1" id="headerboxLink1"><span><span>Social Login</span></span></button></div>'});
            });
        }
        Event.observe('bg_fade', 'click', function() {

            apptha_socialloginclose();

        });

    }

    catch (exception)
    {
        alert(exception);
    }

});



/**
 * Apptha sociallogin login operation ajax part
 */
function doSociallogin(form_id, postUrl, formSuccess, progress_image) {
    new Ajax.Updater(
            {success: formSuccess}, postUrl, {
        method: 'post',
        asynchronous: true,
        evalScripts: false,
        onComplete: function(transport, json) {

            Element.hide(formSuccess);
            var pattern_url = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
            var url_value = pattern_url.test(transport.responseText);
            if (url_value == true)
            {
                Form.reset(form_id);
                window.location.href = transport.responseText;
            } else {
                Element.show(formSuccess);
            }

            Element.hide(progress_image);
        },
        onLoading: function(request, json) {
            Element.show(progress_image);
        },
        parameters: $(form_id).serialize(true),
    }
    );
}



/**
 * Apptha social login pop-up function
 */
function apptha_sociallogin()
{
    
	/**
	 * Background fade is visible
	 */
    $('bg_fade').setStyle({visibility: 'visible', opacity: 0});
    new Effect.Opacity('bg_fade', {duration: .2, from: 0, to: 0.4});

   
    /**
	 * Setting the window width and height
	 */
    if (typeof window.innerHeight != 'undefined') {
        document.getElementById('header_logo_Div').style.top = Math.round(document.body.offsetTop + ((window.innerHeight - document.getElementById('header_logo_Div').getHeight())) / 2) + 'px';
        document.getElementById('header_logo_Div').style.left = Math.round(document.body.offsetLeft + ((window.innerWidth - document.getElementById('header_logo_Div').getWidth())) / 2) + 'px';
    } else {
        document.getElementById('header_logo_Div').style.top = Math.round(document.body.offsetTop + ((document.documentElement.offsetHeight - document.getElementById('header_logo_Div').getHeight())) / 2) + 'px';
        document.getElementById('header_logo_Div').style.left = Math.round(document.body.offsetLeft + ((document.documentElement.offsetWidth - document.getElementById('header_logo_Div').getWidth())) / 2) + 'px';
    }
    $('header_logo_Div').setStyle({display: 'block'});

}

function apptha_socialloginclose() {

    $('header_logo_Div').setStyle({display: 'none'});

    $('register_block').setStyle({display: 'none'});
    $('login_block').setStyle({display: 'block'});
    $('forget_password_div').setStyle({display: 'none'});
    $('twitter_block').setStyle({display: 'none'});
         
    /**
	 * Background fade
	 */
    $('bg_fade').setStyle({visibility: 'hidden', opacity: 0});
    new Effect.Opacity('bg_fade', {duration: .2, from: 0.4, to: 0});
    apptha_clearall();


}

function apptha_clearall(){

	/**
	 * Clear the pop-error message and validation messages
	 */
    $$('.popup_error_msg').each(function(msg) {
        msg.innerHTML = '';
    });
    $('register_error').setStyle({display: 'none'});
    $$('#socialpopup_main_div input').each(function(c) {
        $(c).setValue('');
        $(c).removeClassName('validation-failed');
    });
    $('formSuccess').setStyle({display: 'none'});
    $('form_login').reset();

    $$('.validation-advice').each(function(msg) {
        msg.setStyle({display: 'none'});
    });

}

/**
 * Show / hide forms as user clicks
 */
function show_hide_socialforms(frmid) {

    if (frmid == "1") {          

    	/**
		 * login form
		 */
        $('register_block').hide();
        $('forget_password_div').hide();
        $('form_login').reset();
        $('login_block').show();
        $('twitter_block').hide();
        if (typeof window.innerHeight != 'undefined') {

            var newpostop = Math.round(document.body.offsetTop + ((window.innerHeight - $('header_logo_Div').getHeight())) / 2);
            var newposleft = Math.round(document.body.offsetLeft + ((window.innerWidth - $('header_logo_Div').getWidth())) / 2);
            $('header_logo_Div').setStyle({top: newpostop + 'px', left: newposleft + 'px'});

        } else {
            var newpostop = Math.round(document.body.offsetTop + ((document.documentElement.offsetHeight - $('header_logo_Div').getHeight())) / 2);
            var newposleft = Math.round(document.body.offsetLeft + ((document.documentElement.offsetWidth - $('header_logo_Div').getWidth())) / 2);
            $('header_logo_Div').setStyle({top: newpostop + 'px', left: newposleft + 'px'});

        }
        return false;

    } else if (frmid == "2") {         
       
    	/**
		 * Create Account form
		 */
        $('login_block').hide();
        $('forget_password_div').hide();
        $('register_block').show();
        $('twitter_block').hide();
        apptha_clearall();
        if (typeof window.innerHeight != 'undefined') {

            var newpostop = Math.round(document.body.offsetTop + ((window.innerHeight - $('header_logo_Div').getHeight())) / 2);
            var newposleft = Math.round(document.body.offsetLeft + ((window.innerWidth - $('header_logo_Div').getWidth())) / 2);
            $('header_logo_Div').setStyle({top: newpostop + 'px', left: newposleft + 'px'});

        } else {
            var newpostop = Math.round(document.body.offsetTop + ((document.documentElement.offsetHeight - $('header_logo_Div').getHeight())) / 2);
            var newposleft = Math.round(document.body.offsetLeft + ((document.documentElement.offsetWidth - $('header_logo_Div').getWidth())) / 2);
            $('header_logo_Div').setStyle({top: newpostop + 'px', left: newposleft + 'px'});

        }
        return false;

    } else if (frmid == "3") {                
       
    	/**
		 * Forget password form
		 */
        Effect.toggle('forget_password_div', 'slide', {delay: 0.5});
        $('register_block').hide();
        $('twitter_block').hide();
        $('forget_password').setValue('');
        return false;
    } else if (frmid == "4") {    
       
    	/**
		 * Twitter password form
		 */
        $('twitter_block').show();
        $('register_block').hide();
        $('login_block').hide();
        $('forget_password_div').hide();
        return false;
    }

}

