<?php
/**
 * Fix deprecated jQuery functions.
 *
 * @package millersoils
 */

/**
 * Replace jQuery scripts.
 */
function mo_replace_jquery_scripts() {
	wp_deregister_script( 'quasar-jquery' );
	wp_enqueue_script(
		'quasar-jquery',
		get_stylesheet_directory_uri() . '/js/quasar.jquery.js',
		[ 'jquery' ],
		MO_THEME_VERSION,
		false
	);
	$ajax_call = [
		'ajaxurl'          => admin_url( 'admin-ajax.php' ),
		'ajax_nonce'       => wp_create_nonce( 'rockthemes_security_nonce' ),
		'f_way'            => F_WAY,
		'frontend_options' => [
			'activate_smooth_scroll'     => xr_get_option( 'activate_smooth_scroll', false ),
			'disable_top_links_for_ipad' => xr_get_option( 'disable_top_links_for_ipad', true ),
		],
	];
	wp_localize_script( 'quasar-jquery', 'rockthemes', $ajax_call );

	wp_deregister_script( 'rockthemes-parallax' );
	wp_enqueue_script(
		'rockthemes-parallax',
		get_stylesheet_directory_uri() . '/js/rockthemes-parallax.js',
		[ 'jquery' ],
		MO_THEME_VERSION,
		false
	);

	wp_deregister_script( 'modernizr-js' );
	wp_enqueue_script(
		'modernizr-js',
		get_stylesheet_directory_uri() . '/js/modernizr-custom.js',
		[ 'jquery' ],
		MO_THEME_VERSION,
		false
	);
}

add_action( 'wp_enqueue_scripts', 'mo_replace_jquery_scripts', 20 );

/**
 * Replace jQuery scripts.
 */
function mo_replace_admin_jquery_scripts() {
	wp_deregister_script( 'rock-builder-js' );
	wp_enqueue_script(
		'rock-builder-js',
		get_stylesheet_directory_uri() . '/js/rock-builder.js',
		[ 'jquery' ],
		MO_THEME_VERSION,
		false
	);
}

add_action( 'admin_enqueue_scripts', 'mo_replace_admin_jquery_scripts', 20 );

/**
 * Replacement for rockthemes_pb_frontend_js().
 */
function mo_rockthemes_pb_frontend_js() {
	?>
	<script type="text/javascript">
		jQuery( document ).ready( function() {
			jQuery.fn.rockthemes_animate_columns = function( obj, diff ) {
				if ( ! Modernizr.cssanimations ) return;

				jQuery( window ).scroll( function() {
					jQuery.fn.rockthemes_animate_columns_action( obj, diff );
				} );
			};

			jQuery.fn.rockthemes_animate_columns_action = function( obj, diff ) {
				var current_obj = obj.div;

				var imagePos = current_obj.offset().top;


				var topOfWindow = jQuery( window ).scrollTop() + jQuery( window ).height() - diff;

				if ( imagePos < topOfWindow && ! obj.div.hasClass( obj.animation_class ) ) {
					setTimeout( function() {
						current_obj.addClass( obj.animation_class + ' animated' );
						if ( current_obj.find( '.ajax-body' ).length ) {
							jQuery.fn.rockthemes_animate_ajax_showcase( current_obj );
						}

						if ( current_obj.find( '.rock-skill' ).length ) {
							jQuery.fn.rockthemes_animate_skill( current_obj );
						}

						if ( current_obj.find( '.rockthemes-list' ).length ) {
							jQuery.fn.rockthemes_animate_list( current_obj, obj.animation_class );
						}
					}, obj.delay_time );
				}
			};

			jQuery.fn.rockthemes_animate_ajax_showcase = function( ajax_obj ) {
				var latest_i = 0;
				ajax_obj.find( '.ajax-body ul > li' ).each( function( i ) {
					var that = jQuery( this );
					setTimeout( function() {
						that.addClass( 'animated fadeIn' ).css( { 'opacity': '1' } );
						;
					}, 100 * i );
					//jQuery(this).delay(100*i).animate({"opacity":"1"},150);
					latest_i = i;
				} );

				setTimeout( function() {
					ajax_obj.removeClass( 'rockthemes-animate' );
				}, latest_i * 150 );
			};

			jQuery.fn.rockthemes_animate_skill = function( ajax_obj ) {
				if ( ! Modernizr.cssanimations ) return;
				ajax_obj.find( '.rock-skill' ).each( function( i ) {
					for ( var i = 0; i < jQuery.rockthemes_skills.length; i++ ) {
						if ( jQuery( this ).attr( 'id' ) == jQuery.rockthemes_skills[ i ].id ) {
							var obj = jQuery.rockthemes_skills[ i ].obj;
							var value = jQuery.rockthemes_skills[ i ].value;

							setTimeout( function() {
								obj.refresh( value );
							}, ( ( i + 1 ) * 600 ) );
						}
					}
				} );
			};

			jQuery.fn.rockthemes_animate_list = function( list_element, animation ) {
				list_element.find( 'li' ).css( 'opacity', '0' ).addClass( 'animated' );

				var latest_i = 0;
				list_element.find( ' ul > li' ).each( function( i ) {
					var that = jQuery( this );
					setTimeout( function() {
						that.addClass( animation );
					}, 300 * i );
					latest_i = i;
				} );

			};

			//Set Skill Default Value to 0
			jQuery( '.rockthemes-animate .rock-skill' ).each( function() {
				if ( ! Modernizr.cssanimations ) return;
				for ( var i = 0; i < jQuery.rockthemes_skills.length; i++ ) {
					if ( jQuery( this ).attr( 'id' ) == jQuery.rockthemes_skills[ i ].id ) {
						jQuery.rockthemes_skills[ i ].obj.refresh( 0 );
					}
				}
			} );

			jQuery( '.rockthemes-animate' ).each( function() {
				if ( ! Modernizr.cssanimations ) return;
				var obj = new Object();
				obj.div = jQuery( this );
				obj.animation_class = jQuery( this ).attr( 'animation-class' );
				obj.delay_time = jQuery( this ).attr( 'animation-delay-time' );

				jQuery.fn.rockthemes_animate_columns( obj, 10 );
			} );

			if ( ! Modernizr.cssanimations ) {
				jQuery( '.rockthemes-animate' ).removeClass( 'rockthemes-animate' );
			}


		} );

		jQuery( window ).on( 'load', function() {
			if ( ! Modernizr.cssanimations ) return;

			setTimeout( function() {
				jQuery( '.rockthemes-animate' ).each( function() {
					var obj = new Object();
					obj.div = jQuery( this );
					obj.animation_class = jQuery( this ).attr( 'animation-class' );
					obj.delay_time = jQuery( this ).attr( 'animation-delay-time' );

					jQuery.fn.rockthemes_animate_columns_action( obj, 0 );
				} );
			}, 150 );

		} );
	</script>
	<?php
}

/**
 * Replace rockthemes_pb_frontend_js().
 */
function mo_fix_rockthemes_pb_frontend_js() {
	remove_action( 'wp_footer', 'rockthemes_pb_frontend_js' );
	add_action( 'wp_footer', 'mo_rockthemes_pb_frontend_js' );
}

add_action( 'wp_footer', 'mo_fix_rockthemes_pb_frontend_js', 0 );

/**
 * Replacement for rockthemes_woocommerce_script().
 */
function mo_rockthemes_woocommerce_script() {
	?>
	<script type="text/javascript">
		jQuery( document ).ready( function() {

			//Remove any columns settings like "first" and "last" from columns
			/*
			 if(jQuery(".woocommerce ul.products li.product.last")){
			 jQuery(".woocommerce ul.products li.product.last").removeClass("last");
			 }

			 if(jQuery(".woocommerce-page ul.products li.product.last").length){
			 jQuery(".woocommerce-page ul.products li.product.last").removeClass("last");
			 }

			 if(jQuery(".woocommerce ul.products li.product.first")){
			 jQuery(".woocommerce ul.products li.product.first").removeClass("first");
			 }

			 if(jQuery(".woocommerce-page ul.products li.product.first").length){
			 jQuery(".woocommerce-page ul.products li.product.first").removeClass("first");
			 }
			 */

			jQuery( '.special-cart-container' ).append( jQuery( '.special-cart-overlay-box' ) );

			jQuery( document ).on( 'click', '.special-cart-icon .fa-shopping-cart', function( e ) {
				e.preventDefault();

				if ( jQuery( '.rockthemes-woocommerce-menu-cart' ).children().length < 1 ) return;

				var overlay_box = jQuery( this ).parents( '.special-cart-container' ).find( '.special-cart-overlay-box' );
				if ( overlay_box.hasClass( 'cart-overlay-open' ) ) {
					overlay_box.removeClass( 'cart-overlay-open' ).slideUp( 100 );
				} else {
					overlay_box.addClass( 'cart-overlay-open' ).slideDown( 300 );
				}

			} );

			jQuery( document ).on( 'mouseup', function( e ) {
				var container = jQuery( '.cart-overlay-open' );

				if ( ! container.is( e.target )
					&& container.has( e.target ).length === 0
					&& ! jQuery( '.fa.fa-shopping-cart' ).is( e.target )
					&& jQuery( '.fa.fa-shopping-cart' ).has( e.target ).length === 0 ) {
					if ( jQuery( '.special-cart-overlay-box' ).hasClass( 'cart-overlay-open' ) ) {
						jQuery( '.special-cart-overlay-box' ).removeClass( 'cart-overlay-open' ).slideUp( 100 );
					}
				}
			} );


			jQuery( document ).on( 'click', '.add_to_cart_button', function() {
				var product = jQuery( this ).parents( '.product:eq(0)' ).addClass( 'woocommerce-adding-to-cart-ajax' ).removeClass( 'woocommerce-added-to-cart' );
			} );

			jQuery( document ).on( 'added_to_cart', function() {
				var that = jQuery( '.woocommerce-adding-to-cart-ajax' );
				that.removeClass( 'woocommerce-adding-to-cart-ajax' ).addClass( 'woocommerce-added-to-cart' );
				that.find( ' .rockthemes-woocommerce-added-icon' ).css( 'opacity', '1' );
				that.find( ' .rockthemes-woocommerce-added-icon > img' ).css( 'opacity', '1' );
				setTimeout( function() {
					that.find( ' .rockthemes-woocommerce-added-icon > img' ).animate( { 'opacity': '0' }, 100 );
					that.find( ' .rockthemes-woocommerce-added-icon' ).animate( { 'opacity': '0' }, 180, function() {
						that.find( ' .rockthemes-woocommerce-added-icon' ).attr( 'style', '' );
						that.find( ' .rockthemes-woocommerce-added-icon > img' ).attr( 'style', '' );
					} );
				}, 1800 );
				rockthemes_woo_add_to_cart_motion( that );
			} );

			function rockthemes_woo_add_to_cart_motion( that ) {
				var old_img = that.find( '.rockthemes-woocommerce-thumbnail > img' );
				if ( old_img.length < 1 ) {
					//Add to cart used somewhere elser than shop with image
					update_special_cart_icon_count();
					return;
				}
				var effect_img = old_img.clone();
				var old_img_x = parseInt( parseInt( old_img.offset().left ) );
				var old_img_y = parseInt( parseInt( old_img.offset().top ) - jQuery( window ).scrollTop() );

				var cart = jQuery( '.special-cart-container .special-cart-icon' );
				var cart_x = parseInt( parseInt( cart.offset().left ) );
				var cart_y = parseInt( parseInt( cart.offset().top ) - jQuery( window ).scrollTop() );
				effect_img.css( {
					'width': old_img.width(),
					'height': old_img.height(),
					'position': 'fixed',
					'zIndex': 9999,
					'left': old_img_x,
					'top': old_img_y,
					'opacity': '1'
				} ).addClass( 'add_to_cart_motion_img_shadow add_to_cart_motion_img_transition' );
				jQuery( 'body' ).append( effect_img );

				var scale_diff_x = ( parseInt( old_img.width() * 1.1 ) - parseInt( old_img.width() ) ) / 2;
				var scale_diff_y = ( parseInt( old_img.height() * 1.1 ) - parseInt( old_img.height() ) ) / 2;


				effect_img.css( {
					'opacity': '1',
					'width': old_img.width() * 1.1,
					'height': old_img.height() * 1.1,
					'position': 'fixed',
					'zIndex': 9999,
					'left': old_img_x - scale_diff_x,
					'top': old_img_y - scale_diff_y
				} );
				if ( Modernizr.csstransitions ) {
					effect_img.on( 'transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function() {
						setTimeout( function() {
							effect_img.removeClass( 'add_to_cart_motion_img_transition' )
								.animate( {
									'left': cart_x, 'top': cart_y, 'width': effect_img.width() / 10,
									'height': effect_img.height() / 10, 'opacity': '0.14'
								}, 800, 'easeInOutCubic', function() {
									effect_img.remove();
									update_special_cart_icon_count();
								} );
						}, 100 );
					} );
				} else {
					setTimeout( function() {
						effect_img.removeClass( 'add_to_cart_motion_img_transition' )
							.animate( {
								'left': cart_x, 'top': cart_y, 'width': effect_img.width() / 10,
								'height': effect_img.height() / 10, 'opacity': '0.14'
							}, 800, 'easeInOutCubic', function() {
								effect_img.remove();
								update_special_cart_icon_count();
							} );
					}, 100 );
				}
			}

			function update_special_cart_icon_count() {
				var cart = jQuery( '.special-cart-counter .quasar_cart_count' );
				cart.find( '.display-cart-count' ).html( cart.find( '.new_value' ).html() );
				jQuery( '.special-cart-counter' ).addClass( 'animated shake' );
				setTimeout( function() {
					jQuery( '.special-cart-counter' ).removeClass( 'animated shake' );
				}, 450 );
			}


			//WooCommerce Review Tab - Open from the link above title
			jQuery( document ).on( 'click', '.woocommerce-review-link', function() {
				var tab_id = jQuery( this ).attr( 'href' );
				var content = jQuery( tab_id ).parents( '.tabs-motion-content' );
				if ( typeof content == 'undefined' ) return;

				content = content.attr( 'class' ).split( ' ' );

				var content_id = '';
				for ( var i = 0; i < content.length; i++ ) {
					if ( content[ i ].toString().indexOf( 'content-' ) > -1 ) {
						content_id = content[ i ].toString();
						break;
					}
				}

				if ( content_id == '' ) return;

				jQuery( '.rock-tab-header[content-ref=\'' + content_id + '\']' ).trigger( 'click' );
			} );

		} );

		jQuery( window ).on( 'load', function() {
			if ( typeof update_special_cart_icon_count == 'function' ) {
				update_special_cart_icon_count();
			}

		} );
	</script>
	<?php
}

/**
 * Replace rockthemes_woocommerce_script().
 */
function mo_fix_rockthemes_woocommerce_script() {
	remove_action( 'wp_footer', 'rockthemes_woocommerce_script' );
	add_action( 'wp_footer', 'mo_rockthemes_woocommerce_script' );
}

add_action( 'wp_footer', 'mo_fix_rockthemes_woocommerce_script', 0 );
