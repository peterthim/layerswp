<?php // Get the API up and running for the plugin listing
$api = new Layers_API();
$layers_migrator = new Layers_Widget_Migrator();

$valid_types = array( 'stylekits' , 'extensions' , 'themes' );

$type = isset( $_GET[ 'type' ] ) ? $_GET[ 'type' ] : 'themes';

if( !in_array( $type, $valid_types ) ) return; ?>

<?php switch( $type ){
	case 'stylekits' :
		$excerpt = __( 'Style Kits' , 'layerswp' );
		$products = $api->get_stylekit_list();
		$site_key = 'cc';
		$fallback_url = 'http://bit.ly/layers-stylekits';
		break;
	case 'extensions' :
		$excerpt = __( 'Extensions' , 'layerswp' );
		$products = $api->get_extension_list();
		$site_key = 'cc';
		$fallback_url = 'http://bit.ly/layers-extensions';
		break;
	default :
		$excerpt = __( 'Themes' , 'layerswp' );
		$products = $api->get_theme_list();
		$site_key = 'tf';
		$fallback_url = 'http://bit.ly/layers-themes';
};

$all_authors = array(); ?>

<section id="layers-marketplace" class="l_admin-area-wrapper">

	<?php $this->marketplace_header( 'Marketplace' ); ?>

	<div class="l_admin-row l_admin-well l_admin-content">
		<div class="l_admin-browser">
			<?php if( is_wp_error( $products ) ) { ?>
				<div class="l_admin-section-title l_admin-large l_admin-content-large l_admin-t-center">
					<h3 class="l_admin-heading"><?php _e( 'Oh No!' , 'layerswp'); ?></h3>
					<div class="l_admin-media-body l_admin-push-bottom">
						<p class="l_admin-excerpt"><?php _e( sprintf( 'We had some trouble getting the list of %s, luckily though you can just browse the catalogue on Envato.', strtolower( $excerpt ) ) , 'layerswp'); ?></p>
					</div>
					<a href="<?php echo $fallback_url; ?>" class="l_admin-button btn-primary btn-large"><?php _e( 'Browse on Envato', 'layerswp' ); ?></a>
				</div>
			<?php } else { ?>
				<div class="l_admin-marketplace-loading l_admin-section-title l_admin-large l_admin-content-large l_admin-t-center">
					<h3 class="l_admin-heading"><?php _e( 'Loading...' , 'layerswp'); ?></h3>
					<div class="l_admin-media-body l_admin-push-bottom">
						<p class="l_admin-excerpt"><?php _e( sprintf( 'We\'re busy gathering and sorting the list of %s, hang tight.', strtolower( $excerpt ) ) , 'layerswp'); ?></p>
					</div>
				</div>
			<?php } ?>

			<?php if( !is_wp_error( $products ) ) { ?>
				<div class="l_admin-marketplace-intro l_admin-section-title l_admin-small l_admin-content-small l_admin-hide">
					<p class="l_admin-excerpt"> <?php _e( 'You are viewing', 'layerswp' ); ?> <span id="intro-product-type"> <?php echo strtolower( $excerpt ); ?>,</span><span id="intro-author"></span> <?php _e( 'ordered', 'layerswp' ); ?> <span id="intro-sort"><?php _e( 'by last updated', 'layerswp' ); ?></span>.</p>
				</div>
				<div class="l_admin-products l_admin-hide">

					<?php foreach( $products->matches as $key => $details ) {

						if( FALSE === in_array(  ucfirst( strtolower( $details->author_username ) ), $all_authors ) ){
							$all_authors[] = ucfirst( strtolower( $details->author_username ) );
						}

						$envato_url = 'http://www.layerswp.com/go-envato/?id=' . esc_attr( $details->id ) . '&item=' . esc_attr( $details->name ) . '&site=' . $site_key; ?>
						<div
							id="product-details-<?php echo $details->id; ?>" class="l_admin-product l_admin-animate" tabindex="0"
							data-id="<?php echo $details->id; ?>"
							data-url="<?php echo esc_attr( $envato_url ); ?>"
							data-slug="<?php echo sanitize_title( $details->name ); ?>"
							data-updated="<?php echo strtotime( $details->updated_at ); ?>"
							data-name="<?php echo esc_attr( $details->name ); ?>"
							data-sales="<?php echo esc_attr( $details->number_of_sales ); ?>"
							data-rating="<?php echo ( $details->rating->count > 0 ? ceil( $details->rating->rating ) : '' ) ; ?>"
							data-author="<?php echo $details->author_username; ?>"
							data-price="<?php echo (float) ($details->price_cents/100); ?>"
							data-trending="<?php echo ( isset( $details->trending ) && '1' == $details->trending ? 1 : 0 ); ?>">
							<label for="layers-preset-layout-<?php echo esc_attr( $details->id ); ?>-radio">

								<?php /**
								* Get images and/or video
								**/
								$previews = $details->previews;

								if ( isset( $previews->icon_with_landscape_preview->landscape_url ) && strpos( $previews->icon_with_landscape_preview->landscape_url, '//' ) ) {
									$is_img = 1;
									$image_src = $previews->icon_with_landscape_preview->landscape_url ;
								} else if ( isset( $previews->icon_with_video_preview->landscape_url ) && strpos( $previews->icon_with_video_preview->landscape_url, '//' ) ) {
									$is_img = 1;
                           			$image_src = $previews->icon_with_video_preview->landscape_url ;
								} else if ( isset( $previews->icon_with_video_preview->video_url ) && strpos( $previews->icon_with_video_preview->video_url, '//' ) ) {
									$is_img = 0;
									$video_src = $previews->icon_with_video_preview->video_url ;
								} ?>

								<?php if( isset( $image_src ) ) { ?>
									<div class="l_admin-product-screenshot" data-view-item="product-details-<?php echo $details->id; ?>">
										<?php if( 1 == $is_img ) { ?>
											<img src="<?php echo esc_url( $image_src ); ?>" />
										<?php } else { ?>
											<?php layers_show_html5_video( esc_url( $video_src ) ); ?>
										<?php } ?>
									</div>
								<?php } ?>

								<h3 class="l_admin-product-name" id="<?php echo esc_attr( $details->id ); ?>">
									<?php echo esc_html( $details->name ); ?>
								</h3>

								<div class="l_admin-marketplace-actions">
									<?php if( isset( $details->rating ) && 3 <= $details->rating->count && 2<= $details->rating->rating ) { ?>
										<div class="l_admin-pull-right theme-rating star-rating l_admin-push-left-small">
											<?php for( $i = 1; $i < 6; $i++ ){ ?>
												<?php if( ceil( $details->rating->rating ) >= $i ) { ?>
													<span class="star star-full"></span>
												<?php } else { ?>
													<span class="star star-empty"></span>
												<?php } ?>
											<?php } ?>
										</div>
									<?php } ?>
									<a class="l_admin-pull-left l_admin-button btn-subtle l_admin-push-right-small" data-item="<?php echo esc_attr( $details->name ); ?>" data-view-item="product-details-<?php echo $details->id; ?>" href="<?php echo $envato_url; ?>" target="_blank">
										<?php _e( 'Details' , 'layerswp' ); ?>
									</a>
									<a class="l_admin-pull-left l_admin-button btn-primary l_admin-push-right-small" href="<?php echo $envato_url; ?>&type=purchase" target="_blank" data-item="<?php echo esc_attr( $details->name ); ?>" data-price="$ <?php echo (float) ($details->price_cents/100); ?>">
										<?php _e( 'Buy for ' , 'layerswp' ); ?>
										$<?php echo (float) ($details->price_cents/100); ?>
									</a>
									<span class="l_admin-pull-left l_admin-sales-count">
										<?php echo esc_attr( $details->number_of_sales ); ?> sales
									</span>
								</div>
							</label>
							<input  class="l_admin-product-json" type="hidden" value='<?php echo htmlspecialchars( json_encode( $details ) ); ?>' />
						</div>
					<?php } // Get Preset Layouts ?>
					<script>
						// Fill the author select box
						var layers_market_authors = jQuery.parseJSON( '<?php echo json_encode( $all_authors ); ?>' );
						layers_market_authors.sort();

						jQuery.each( layers_market_authors, function( key, username ){
							jQuery( '#layers-marketplace-authors' ).append( jQuery( '<option value="'+ username.toString().toLowerCase() + '">' + username + '</option>') );
						});

					</script>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="theme-overlay l_admin-hide">
		 <div class="theme-backdrop"></div>
		 <div class="theme-wrap">
			  <div class="theme-header">
					<button class="left dashicons dashicons-no"><span class="screen-reader-text">Show previous</span></button>
					<button class="right dashicons dashicons-no"><span class="screen-reader-text">Show next</span></button>
					<button class="close dashicons dashicons-no"><span class="screen-reader-text">Close details dialog</span></button>
			  </div>
			  <div class="theme-about">
					<div class="theme-screenshots"><img /></div>
					<div class="theme-info">
						<h3 class="theme-name"></h3>
						<p class="l_admin-row">
							<span class="l_admin-pull-left l_admin-push-right-small">
								<img class="theme-author-img" />
							</span>
							<span class="theme-meta">
								<a class="theme-author" target="_blank"></a>
								<span class="theme-sales"></span>
							</span>
						</p>
						<p class="theme-rating star-rating"></p>
						<p class="theme-description"></p>
						<p class="theme-tags"></p>
					</div>
			  </div>

			  <div class="theme-actions">
					<div class="inactive-theme">
						<a href="" class="button button-secondary theme-details-link" target="_blank">
							<?php _e( 'View More Info' , 'layerswp' ); ?>
						</a>
						<a href="" class="button button-secondary theme-demo-link" target="_blank">
							<?php _e( 'View Demo' , 'layerswp' ); ?>
						</a>
						<a href="" class="button button-primary theme-buy-link" target="_blank">
							<?php _e( 'Buy for $', 'layerswp' ); ?> <span class="theme-price"></span>
						</a>
					</div>
			  </div>
		 </div>
	</div>

</section>
<?php $this->footer(); ?>