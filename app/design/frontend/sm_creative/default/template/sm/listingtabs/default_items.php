<?php 
	$limit_product = Mage::getStoreConfig('listingtabs_cfg/source_options/product_limitation');

	$catidPost = $this->getRequest()->getPost('categoryid');
?>		
<div class="slider_<?php echo $catidPost;?>">
	<div class="slider-t">
	<?php
	/*------------------------------------------------------------------------
		# SM Listing Tabs - Version 2.0.0
		# Copyright (c) 2014 YouTech Company. All Rights Reserved.
		# @license - Copyrighted Commercial Software
		# Author: YouTech Company
		# Websites: http://www.magentech.com
	   -------------------------------------------------------------------------*/

	$helper = Mage::helper('listingtabs/data');
	if ($this->_isAjax()) {
		$type_filter = $this->_getConfig('filter_type');
		switch ($type_filter) {
			case 'categories':
				$catid = $this->getRequest()->getPost('categoryid');
				$catid = $this->getRequest()->getPost('categoryid');
				$child_items = $this->_getProductInfor($catid);
				break;
			case 'fieldproducts':
				$field_order = $this->getRequest()->getPost('categoryid');
				$catid = $this->_getCatIds();
				$child_items = $this->_getProductInfor($catid, $field_order);
				break;
		}
		
		
	}

	if (!empty($child_items)) {
		$k = $this->getRequest()->getPost('ajax_listingtags_start', 0);
		$count = 0;
		$nb_rows = 6;
		foreach ($child_items as $_product) {  $count++;
			$now = date("Y-m-d");
			$newsFrom = substr($_product->getData('news_from_date'), 0, 10);
			$newsTo = substr($_product->getData('news_to_date'), 0, 10);
			$specialprice = $_product->getData('special_price');
		?>
			<?php if ($count % $nb_rows == 1 || $nb_rows == 1) { ?>
			<div class="item">
			<?php } ?>
				<div class="item-wrapper">
					<div class="item-inner">
						<?php
						if ($_product->_image) {
							?>
							<div class="product-image">

									<a href="<?php echo $_product->link ?>" title="<?php echo $_product->title;?>">
										<?php if($_product->getThumbnail() != $_product->getSmallImage()) { ?> 
										   <img class="small-image" src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize(370,370); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" />
										   <img class="thumnail-image image-hover" src="<?php echo $this->helper('catalog/image')->init($_product, 'thumbnail')->resize(370,370) ?>" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'thumbnail'), null, true) ?>" />
										<?php } else { ?>
											<img class="small-image" src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize(370,370); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" />
										<?php } ?>									 
									</a>
									
									<?php if ($newsFrom !="" && $now>=$newsFrom && ($now<=$newsTo || $newsTo=="")){?>
										<div class="new-item">
											<span><?php echo $this->__('New!'); ?></span>
										</div>
									<?php }?>
									
									<?php if ( $specialprice ){ ?>
										<div class="sale-item">
											<span><?php echo $this->__('Sale'); ?></span>
										</div>
									<?php }?>	

								
								<?php if ($this->_getConfig('product_description_display', 1) == 1 && $helper->_trimEncode($_product->description) != '') { ?>
									<div class="short-dsc">

										<?php if ($this->_getConfig('product_date_display', 1) == 1) { ?>
											<div class="created-date ">
												<?php echo $_product->created_at; ?>
											</div>
										<?php } ?>
											
										<div class="des-content">
											<?php echo $_product->_description; ?>
										</div>
										
										<?php if ($this->_getConfig('product_hits_display')) { ?>
											<div class="hits"><?php echo 'Read'; ?> 
												<?php 
												if ($_product->num_view_counts > 1) {
													echo $_product->num_view_counts . ' times';
												} else {
													echo $_product->num_view_counts . ' time';
												}?>
											</div>
										<?php } ?>
												
										<?php if ((int)$this->_getConfig('product_readmore_display', 1)) { ?>
											<div class="item-readmore">
												<a href="<?php echo $_product->link; ?>"
													title="<?php echo $_product->title ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')); ?> >
													<?php echo $this->_getConfig('product_readmore_text', 'Detail'); ?>
												</a>
											</div>
										<?php } ?>


									</div>
								<?php } ?>
								
								
							</div>
						<?php } ?>
						<div class="product-info">
							<?php if ($this->_getConfig('product_title_display', 1) == 1) { ?>
							<div class="product-name">
								<a href="<?php echo $_product->link ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')) ?>
								   title="<?php echo $_product->title ?>">
									<?php echo $helper->truncate($_product->title, $this->_getConfig('product_title_maxlength', 25)); ?>
								</a>
							</div>
							<?php } ?>
							
							<?php if ((int)$this->_getConfig('product_reviews_count', 1)) { ?>
								<?php echo $this->getReviewsSummaryHtml($_product, "short", true); ?>
							<?php } ?>
							
							<?php if ((int)$this->_getConfig('product_price_display', 1)) { ?>
								<div class="sale-price">
									<?php echo $this->getPriceHtml($_product, true); ?>
								</div>
							<?php } ?>
							
							<div class="product-addto-wrap">
								<div class="product-addto-wrap-inner">
									<?php if ((int)$this->_getConfig('product_addwishlist_display', 1)) { ?>
										<?php if ($this->helper('wishlist')->isAllow()) { ?>
											<a title="<?php echo $this->__('Add to Wishlist') ?>" href="<?php echo $this->helper('wishlist')->getAddUrl($_product) ?>"
													class="link-wishlist"><span><?php echo $this->__('Add to Wishlist') ?></span>
											</a>
										<?php } ?>
									<?php } ?>
									
									<?php if ((int)$this->_getConfig('product_addcart_display', 1)) { ?>
										<?php if ($_product->isSaleable()) { ?>
										<div class="product-addcart">
											<button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')">
													<span>
														<span>
															<?php echo $this->__('Add to Cart') ?>
														</span>
													</span>
											</button>
										</div>
										<?php } else { ?>
											<p class="availability out-of-stock">
												<span>
												<?php echo $this->__('Out of stock') ?>
												</span>
											</p>
											<?php } ?>
									<?php } ?>
									
									<?php if ((int)$this->_getConfig('product_addcompare_display', 1)) { ?>
										<?php if ($_compareUrl = $this->getAddToCompareUrl($_product)) { ?>
											<a title="<?php echo $this->__('Add to Compare') ?>" href="<?php echo $_compareUrl ?>"
											   class="link-compare"><span><?php echo $this->__('Add to Compare') ?></span>
											</a>
										<?php } ?>
									<?php } ?>
										
								</div>
							</div>
							
							<div class="action-button">
								<div class="btn-top">
									
								</div>
								
								<div class="btn-bottom">
									<a style="display:none;" href="<?php echo $_product->link ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')) ?>
									   title="<?php echo $_product->title ?>">
										<?php echo $helper->truncate($_product->title, $this->_getConfig('product_title_maxlength', 25)); ?>
									</a>
									<?php if ((int)$this->_getConfig('product_addwishlist_display', 1) || (int)$this->_getConfig('product_addcompare_display', 1)) { ?>
										
										
										
									<?php } ?>
								</div>
							</div>
						</div>

						
					</div>
				</div>
			<?php if ($count % $nb_rows == 0 || $count == $limit_product) { ?>
			</div>
			<?php } ?>
			
		<?php
		}
	}?>

	</div>	
	<?php if ($this->_isAjax()) {?>		
			<script>
				jQuery.noConflict()
				var owl = jQuery(".slider_<?php echo $catidPost;?> .slider-t");
				owl.owlCarousel({
					responsive:{
						0:{
							items:1
						},
						480:{
							items:1
						},
						768:{
							items:1
						},
						992:{
							items:1
						},
						1200:{
							items:1
						}
					},
					margin: 30,
					navigation : true, // Show next and prev buttons
					//autoPlay:true,
					pagination:false,
					stopOnHover:true
				});
			</script>
	<?php } ?>	
</div>


		