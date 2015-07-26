(function($){
	
	$.fn.viewport = function(){	
	    var e = window, a = 'inner';
	    if (!('innerWidth' in window )) {
	        a = 'client';
	        e = document.documentElement || document.body;
	    }
	    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	}	
	
	$.fn.getScrollbarWidth=	function(){
		document.body.style.overflow = 'hidden'; 
		var width = document.body.clientWidth;
		document.body.style.overflow = 'scroll'; 
		width -= document.body.clientWidth; 
		if(!width) width = document.body.offsetWidth - document.body.clientWidth;
		document.body.style.overflow = ''; 	
		return width;
	}
	
	$.fn.detectDevice = function(){
		var width = $.fn.viewport().width;
		var scrollWidth = $().getScrollbarWidth();
		//alert(width);
 		if( width > 1200 ){
			return 'wide';
  		}else if( width >= 992 ){
			return 'normal';
  		}else if(  width >= 768 ){
			return 'tablet';
  		}else if(  width > 0 ){
			return 'mobile';
  		}
	}

	$.fn.updateDataElementClass = function(){
		var $this = $(this);
		
		currentdevice = (currentdevice)?currentdevice:$.fn.detectDevice();
		
		// Build data
		if ($this.data('default')) return; 		
		// With attr data-*
		else $this.data();
		// Make the source better view in inspector
    	$this.removeAttr ('data-default data-wide data-normal data-tablet data-stablet data-mobile');
		// For element no attr data-default
		if (!$this.data('default')) 
			$this.data('default', $this.attr('class'));
		// Default
		if ( !$this.data('default') ||  !currentdevice || !$this.data(currentdevice))
			return;	

		// Add new
		else{
				// Remove current
				$this.removeClass ($this.data('default'));			
				$this.addClass ($this.data(currentdevice));
		}
	}
	
})(jQuery)
var currentdevice = '';
jQuery(document).ready(function($){
	
	var bootstrap_elements = $('[class*="span"]');
	// Build data
	bootstrap_elements.each ( function(){
		var $this = $(this);
		// With attr data-*
		$this.data();
		// Make the source better view in inspector
    	 $this.removeAttr ('data-default data-wide data-normal data-tablet data-stablet data-mobile');
		// For element no attr data-default
		if (!$this.data('default')) 
			$this.data('default', $this.attr('class'));
	
	});
	function updateBootstrapElementClass(newdevice){
  		if (newdevice == currentdevice) return ;
		bootstrap_elements.each(function(){
			var $this = $(this);
			// Default
			if ( !$this.data('default') || (!$this.data(newdevice) && (!currentdevice || !$this.data(currentdevice))) )
				return;
			// Remove current
			if ($this.data(currentdevice)) $this.removeClass($this.data(currentdevice));
			else $this.removeClass ($this.data('default'));
			// Add new
			if ($this.data(newdevice)) $this.addClass ($this.data(newdevice));
			else $this.addClass ($this.data('default'));
		});
    	currentdevice = newdevice;
	};
	function getScrollbarWidth(){
		document.body.style.overflow = 'hidden'; 
		var width = document.body.clientWidth;
		document.body.style.overflow = 'scroll'; 
		width -= document.body.clientWidth; 
		if(!width) width = document.body.offsetWidth - document.body.clientWidth;
		document.body.style.overflow = ''; 	
		return width;
	}
	function viewport() {
	    var e = window, a = 'inner';
	    if (!('innerWidth' in window )) {
	        a = 'client';
	        e = document.documentElement || document.body;
	    }
	    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	}
	
	function idforPriceOnMenu(){
		if($('.sm_megamenu_content .price-box .special-price span.special-price').length > 0){
			$('.sm_megamenu_content .price-box .special-price span.special-price').each(function(){
				$(this).attr('id', $(this).attr('id')+'_onmenu');
			});
		}
		if($('.sm_megamenu_content .price-box .regular-price').length > 0){
			$('.sm_megamenu_content .price-box .regular-price').each(function(){
				$(this).attr('id', $(this).attr('id')+'_onmenu');
			});
		}
	}
	idforPriceOnMenu();	
	
	function detectDevice () {
		var width = viewport().width; 
	
 		if( width > 1200 ){
			return 'wide';
  		}else if( width >= 992 ){
			return 'normal';
  		}else if(  width >= 768 ){
			return 'tablet';
  		}else if(  width > 0 ){
			return 'mobile';
  		}
	}

  	updateBootstrapElementClass (detectDevice());
  
  	// With window resize
  	$(window).resize(function(){
    	if ($.data(window, 'detect-device-time'))
      		clearTimeout($.data(window, 'detect-device-time'));
    	$.data(window, 'detect-device-time',
      		setTimeout(function(){
        		updateBootstrapElementClass (detectDevice());
      		}, 200)
    	)
  	});

});


jQuery(window).load(function(){	
  	function viewport() {
	    var e = window, a = 'inner';
	    if (!('innerWidth' in window )) {
	        a = 'client';
	        e = document.documentElement || document.body;
	    }
	    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	}
});