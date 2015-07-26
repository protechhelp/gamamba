jQuery(document).ready(function(){

	jQuery('#row_creative_cfg_general_body_background_image td .inline img').click(function(){
		$this = jQuery(this);
		jQuery('#row_creative_cfg_general_body_background_image td .inline').removeClass('active');
		jQuery('#row_creative_cfg_general_body_background_image td .inline img').removeClass('active');
		$this.addClass('active');
	});
	
	jQuery('#row_creative_cfg_general_body_background_image td input').each(function(){
		$this = jQuery(this);
		attr_id = $this.attr('id');
		attr_active = $this.attr('checked');
		if( attr_active == 'checked' ){		
			jQuery('#row_creative_cfg_general_body_background_image td .inline').each(function(){
				$this = jQuery(this);
				attr_for = $this.attr('for');				
				if( attr_for == attr_id ){
					$this.addClass('active');
				}
			});	
		}
	});
	
});

