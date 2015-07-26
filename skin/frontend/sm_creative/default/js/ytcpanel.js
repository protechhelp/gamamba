// JavaScript Document

jQuery(document).ready(function($){
	/* Begin: Show o hide cpanel */  
	$('#cpanel_btn').click(function(){
		if($('#cpanel_btn i').attr('class') == 'fa fa-hand-o-left'){
			$('#cpanel_wrapper').animate({
				'right':'-302px'  
			}, 200, function(){
				$('#cpanel_wrapper').show().animate({
					'right':'0px'
				});
			});
			$('#cpanel_btn i').attr('class', 'fa fa-hand-o-right');
		}else if($('#cpanel_btn i').attr('class')=='fa fa-hand-o-right'){
			$('#cpanel_wrapper').animate({
				'right':'0px'  
			}, 200, function(){
				$('#cpanel_wrapper').show().animate({
					'right':'-302px'
				});
			});
			$('#cpanel_btn i').attr('class', 'fa fa-hand-o-left');
		}
	});
	/* End: Show o hide cpanel */	
});

function onCPResetDefault(_cookie){
	for (i=0;i<_cookie.length;i++) { 
		if(getCookie(TMPL_NAME+'_'+_cookie[i])!=undefined){
			createCookie (TMPL_NAME+'_'+_cookie[i], '', -1);
		}
	}

	if (window.location.href.indexOf ('?')>-1) window.location.href = window.location.href.substr(0,window.location.href.indexOf ('?'));
	else window.location.reload();
}

function onCPApply () {
	var elems = document.getElementById('cpanel_wrapper').getElementsByTagName ('*');
	var usersetting = {};
	for (i=0;i<elems.length;i++) {
		var el = elems[i]; 
	    if (el.name && (match=el.name.match(/^ytcpanel_(.*)$/))) {
	        var name = match[1];	        
	        var value = '';
	        if (el.tagName.toLowerCase() == 'input' && (el.type.toLowerCase()=='radio' || el.type.toLowerCase()=='checkbox')) {
	        	if (el.checked) value = el.value;
	        } else {
	        	value = el.value;
	        }
			if(trim(value)){
				if (usersetting[name]) {
					if (value) usersetting[name] = value + ',' + usersetting[name];
				} else {
					usersetting[name] = value;
				}
			}
	    }
	}
	
	for (var k in usersetting) {
		name = TMPL_NAME + '_' + k; //alert(name);
		value = usersetting[k];
		createCookie(name, value, 365);
	}
	
	if (window.location.href.indexOf ('?')>-1) window.location.href = window.location.href.substr(0,window.location.href.indexOf ('?'));
	else window.location.reload();
}