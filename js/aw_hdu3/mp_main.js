jQuery(document).ready(function() {


	jQuery('.m-tc-right').click(function(event) {
		if ((parseInt(jQuery('.m-tips-mover').css("left")) % 425) == 0) {
		var max = (((jQuery('.m-tip').size() - 1) * 425) * -1) + "px";
		if (jQuery('.m-tips-mover').css("left") == max) {
			jQuery('.m-tips-mover').css("left", "0px");
		} else {
			var ileTeraz = (parseInt(jQuery('.m-tips-mover').css("left")) - 425) + "px";
			jQuery('.m-tips-mover').css("left", ileTeraz);
		}
		};
	});


	jQuery('.m-tc-left').click(function(event) {
		if ((parseInt(jQuery('.m-tips-mover').css("left")) % 425) == 0) {
		var max = (((jQuery('.m-tip').size() - 1) * 425) * -1) + "px";
		if (jQuery('.m-tips-mover').css("left") == "0px") {
			jQuery('.m-tips-mover').css("left", max);
		} else {
			var ileTeraz = (parseInt(jQuery('.m-tips-mover').css("left")) + 425) + "px";
			jQuery('.m-tips-mover').css("left", ileTeraz);
		}
		}
	});
	
	
	jQuery('.m-select').click(function(event) {
		if(jQuery(this).hasClass('active')) {		
			jQuery(this).removeClass('active');			 
			jQuery(this).find('ul').hide();
                      
		} else {
			jQuery(this).addClass('active');
			jQuery(this).find('ul').show();			
		}
	});
	
	jQuery('.m-select').find('.u_select').mouseleave(function() {
		jQuery(this).find('ul').hide();
		jQuery(this).parent().removeClass('active');
	});
	

	jQuery('.m-select ul li').click(function(event) {            
		var thisText = jQuery(this).html();
		var thisValue = jQuery(this).val();
		var stringThisValue = jQuery(this).attr('data-val');
		var thisDataValue = jQuery(this).data('value');
		jQuery(this).parent().parent().parent().find('span').html(thisText);
		jQuery(this).parent().parent().parent().find('span').attr('data-value', thisDataValue);
		jQuery(this).parent().parent().parent().find('input').val(thisValue);

		if(stringThisValue) {
			jQuery(this).parent().parent().parent().find('input').val(stringThisValue);
		}
		var m_select = jQuery(this).parent().parent().parent();
		jQuery(m_select).find('ul').slideUp('600', function(){
			  jQuery(m_select).removeClass('active');
		});

        if(stringThisValue == 'retail'){
            jQuery('.wholesale-price, .mp-add-price-range').hide();
        } else {
            jQuery('.wholesale-price, .mp-add-price-range').show();
        }

        if(stringThisValue == 'wholesale'){
            jQuery('.retail-price').hide();
            jQuery('.request-selected').hide();

        } else {
            jQuery('.retail-price').show();
            jQuery('.request-selected').show();
        }

	});
	
	jQuery('.m-tips-mover').css("width", jQuery('.m-tip').size() * 480 );

	jQuery('.mp-acc-balance-drop ').click(function(event) {
		if(jQuery(this).hasClass('active')) {
			jQuery(this).removeClass('active');
			jQuery(this).find('ul').hide();
		} else {
			jQuery(this).addClass('active');
			jQuery(this).find('ul').show();
		}
	});
	
	jQuery('.mp-acc-balance-drop').find('.u_select2').mouseleave(function() {	
		jQuery(this).find('ul').hide();
		jQuery(this).parent().removeClass('active');
	});

	jQuery('.mp-acc-balance-drop ul').click(function(event) {
		jQuery(this).slideUp();
	});


	




	jQuery('.mp-front-menu li').click(function(event) {
		if (jQuery(event.target).hasClass('mp-fm-close')) {
			return false;
		} else {
			if(jQuery(this).hasClass('pushed')) {
				jQuery(this).removeClass('pushed');
				jQuery(this).find('ul').hide();

			} else {
				jQuery(this).addClass('pushed');
				jQuery(this).find('ul').show();
			}
		}
	});

	jQuery('.mp-front-menu > li ul li').click(function(event) {
		jQuery(this).addClass('active');   
		var value = jQuery(this).text();
		var numval = jQuery(this).attr('data-rel');
		
		jQuery(this).parent().parent().parent().find('span span').text(value);
		
		if(numval == 0)
			jQuery(this).parent().parent().parent().removeClass('active');
		else
			jQuery(this).parent().parent().parent().addClass('active');
	});

  jQuery('.mp-front-menu > li').find('.mp-front-menu-drop-down').mouseleave(function() {	
		jQuery(this).find('ul').hide();
		jQuery(this).parent().removeClass('pushed');
	});

	jQuery('.mp-front-menu li span img').click(function(event) {
		var value = jQuery(this).parent().parent().data('default');
		jQuery(this).parent().parent().find('span span').text(value);
		jQuery(this).parent().parent().removeClass('active');
	});

	jQuery('.mp-acc-menu > ul > li.active').each(function(index, el) {
		var src = jQuery(this).find('.mp-menu-ico img').attr("src");
		src =  src.replace('.png', '-h.png');
		jQuery(this).find('.mp-menu-ico img').attr("src", src);
	});

	jQuery('.mp-offerts li').click(function(event) {
		if (jQuery(this).find('.mp-o-ratio').hasClass('active')) {
			jQuery(this).find('.mp-o-ratio').removeClass('active');
		} else {
			jQuery('.mp-o-ratio').removeClass('active');
			jQuery(this).find('.mp-o-ratio').addClass('active');
		}
	});

	jQuery('.mp-acc-front > li').hover(function() {
		var src = jQuery(this).find('.mp-acc-f-head img').attr("src");
		src = src.replace('.png', '-h.png');
		jQuery(this).find('.mp-acc-f-head img').attr("src", src);
	}, function() {
		var src = jQuery(this).find('.mp-acc-f-head img').attr("src");
		src = src.replace('-h.png', '.png');
		jQuery(this).find('.mp-acc-f-head img').attr("src", src);
	});	
		
	jQuery('.my-account .notify_status span').click(function(e){
		e.stopPropagation();
		var id = jQuery(this).parent().attr('id').split('_')[1];
		if (id > 0) {
			delNotifyDrop(id);
		}
	});


    jQuery('.request-selected-btn img').hover(function() {
        jQuery('.request-selected-btn .request-selected-tooltip').show();
    }, function() {
        jQuery('.request-selected-btn .request-selected-tooltip').hide();
    });

    jQuery('.feeTooltip').hover(function() {
        jQuery('.feeTooltip-content').show();
    }, function() {
        jQuery('.feeTooltip-content').hide();
    });

    jQuery('.request-selected-input input').keyup();

});

//------------------------ notify -----------------------
function moveToNotifyUrl(url){
	location.href = url;
	
	return;
}

function delNotifyDrop(id, type){
	if (type == 'basket-important') {
		var height = jQuery('#basket-important #dropnotify_'+id).height()+20;
		jQuery('#basket-important #dropnotify_'+id).css('height', height).html('<td colspan="3"><span class="loading"></span></td>');
	}
	
	jQuery('div.my-acc-basket #dropnotify_'+id+' var').remove();
	jQuery('div.my-acc-basket #dropnotify_'+id+' .mark').removeClass('mark');
	jQuery('div.my-acc-basket #dropnotify_'+id).removeClass('important');
	jQuery('#notifygrid #listnotify_'+id+' span').remove();
	jQuery('#notifygrid #listnotify_'+id).parent().removeClass('important');
	jQuery('#important-notifications #listnotifyimportant_'+id+' span').remove();
	jQuery('#important-notifications #listnotifyimportant_'+id).parent().removeClass('important');
	var uri = jQuery('div.my-acc-basket #mp-head-notify').attr('data-rel');
	var cnt = parseInt(jQuery('div.my-acc-basket .headerNotifyCount').text());
	cnt--;
	
	if(cnt<=0) {
		jQuery('div.headerNotifyCount').remove();
		if (jQuery("#important-nav").length > 0){
			jQuery('#important-nav var').remove();
		}
		if (jQuery("#basketNav_important").length > 0){
			jQuery('#basketNav_important var').remove();
		}
	} else {
		jQuery('div.headerNotifyCount').text(cnt);
		if (jQuery("#important-nav").length > 0){
			jQuery('#important-nav var').text(cnt);
		}
		if (jQuery("#basketNav_important").length > 0){
			jQuery('#basketNav_important var').text(cnt);
		}
	}
	
	jQuery.ajax({
		type: "POST",
		url: uri+'notifications',
		data: 'notify='+id,
		dataType: 'json',
		beforeSend: function( xhr ) {
			
		}
	}).done(function(data) {
		if (type == 'basket-important' || type == 'basket-all') {
			jQuery.ajax({
				url: uri+'notificationsload'
			}).done(function(data) {
				jQuery('#basket-important > table').html(data);
				
				jQuery('#basket-all .mark').click(function(e){

					e.stopPropagation();		
					var id = jQuery(this).parent().attr('id').split('_')[1];
					if (id > 0) {
						delNotifyDrop(id, 'basket-all');
					}
				});
				
				jQuery('#basket-important .mark').click(function(e){
					e.stopPropagation();
					var id = jQuery(this).parent().attr('id').split('_')[1];
					if (id > 0) {                
						delNotifyDrop(id, 'basket-important');
					}
				});
			});
		}
	});
}