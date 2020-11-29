<?php
/**
 * Theme functions.
 *
 * @package millersoils
 */

define( 'MO_THEME_VERSION', '2.0' );

/**
 * Sets up the content width value based on the theme's design.
 *
 * @see quasar_content_width() for template-specific adjustments.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 604;
}

/**
 * Filters MO file path for loading translations for 'quasar' text domain.
 *
 * @param string $mofile Path to the MO file.
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 *
 * @return string
 */
function msc_load_textdomain_mofile( string $mofile, string $domain ) {
	if ( 'quasar' === $domain ) {
		return get_stylesheet_directory() . '/languages/quasar-ru_RU.mo';
	}

	return $mofile;
}

add_filter( 'load_textdomain_mofile', 'msc_load_textdomain_mofile', 10, 2 );

/**
 * Replace buttons.css.
 */
function msc_wp_enqueue_scripts() {
	// Buttons.
	wp_deregister_style( 'quasar-buttons' );

	wp_enqueue_style( 'quasar-buttons', get_stylesheet_directory_uri() . '/css/buttons.css', [], MO_THEME_VERSION );
}

add_action( 'wp_enqueue_scripts', 'msc_wp_enqueue_scripts', 20 );

if ( ! function_exists( 'quasar_content_width' ) ) :
	/**
	 * Adjusts content_width value for video post formats and attachment templates.
	 *
	 * @return void
	 * @since Quasar 1.0
	 */
	function quasar_content_width() {
		global $content_width;

		if ( is_attachment() ) {
			$content_width = 724;
		} elseif ( has_post_format( 'audio' ) ) {
			$content_width = 800;
		}
	}
endif;

/**
 * Enqueue main theme style.
 */
function quasar_child_enqueue() {
	wp_deregister_style( 'quasar-style' );

	// Enqueue parent styles.
	wp_enqueue_style( 'quasar-main', get_template_directory_uri() . '/style.css', [], MO_THEME_VERSION );
	wp_enqueue_style( 'quasar-style', get_stylesheet_uri(), [ 'quasar-main' ], MO_THEME_VERSION );
}

add_action( 'wp_enqueue_scripts', 'quasar_child_enqueue', 20 );

/**
 * Filter to modify favicon in admin area.
 *
 * @param string $url     URL of favicon.
 * @param int    $size    Size of favicon.
 * @param int    $blog_id Blog id.
 *
 * @return false|string
 */
function get_site_icon_url_filter( string $url, int $size, int $blog_id ) {
	if ( is_admin() ) {
		$switched_blog = false;

		if ( is_multisite() && ! empty( $blog_id ) && get_current_blog_id() !== (int) $blog_id ) {
			switch_to_blog( $blog_id );
			$switched_blog = true;
		}

		switch ( $blog_id ) {
			case 0:
				$site_icon_id = 3340; // Here we set admin-favicon.png id.
				break;
			default:
				$site_icon_id = get_option( 'site_icon' );
		}

		if ( $site_icon_id ) {
			if ( $size >= 512 ) {
				$size_data = 'full';
			} else {
				$size_data = [ $size, $size ];
			}
			$url = wp_get_attachment_image_url( $site_icon_id, $size_data );
		}

		if ( $switched_blog ) {
			restore_current_blog();
		}
	}

	return $url;
}

add_filter( 'get_site_icon_url', 'get_site_icon_url_filter', 10, 3 );

/**
 * Filter to add svg favicon.
 * It is not clear why do we need it...
 *
 * @param array $meta_tags Meta tags for favicon.
 *
 * @return array
 */
function site_icon_meta_tags_filter( array $meta_tags ) {
	$favicon_svg = get_stylesheet_directory_uri() . '/favicon.svg';
	$meta_tags[] = sprintf( '<link rel="mask-icon" href="%s" />', esc_url( $favicon_svg ) );
	$meta_tags[] = sprintf( '<link rel="icon" type="image/svg+xml" href="%s" />', esc_url( $favicon_svg ) );

	return $meta_tags;
}

//add_filter( 'site_icon_meta_tags', 'site_icon_meta_tags_filter' );

/**
 * Register taxonomies for Dealer custom post type.
 */
function register_taxonomies() {
	$args = [
		'labels'            => [
			'name'              => 'Locations',
			'singular_name'     => 'Location',
			'search_items'      => 'Search Locations',
			'all_items'         => 'All Locations',
			'parent_item'       => 'Parent Location',
			'parent_item_colon' => 'Parent Location:',
			'edit_item'         => 'Edit Location',
			'update_item'       => 'Update Location',
			'add_new_item'      => 'Add New Location',
			'new_item_name'     => 'New Location',
			'menu_name'         => 'Locations',
		],
		'description'       => 'Dealer Location',
		'public'            => true,
		'show_ui'           => true,
		'hierarchical'      => true,
		'meta_box_cb'       => null,
		'show_admin_column' => false,
	];
	register_taxonomy( 'location', [ 'dealer' ], $args );

	$args = [
		'labels'            => [
			'name'              => 'Types',
			'singular_name'     => 'Type',
			'search_items'      => 'Search Types',
			'all_items'         => 'All Types',
			'parent_item'       => 'Parent Type',
			'parent_item_colon' => 'Parent Type:',
			'edit_item'         => 'Edit Type',
			'update_item'       => 'Update Type',
			'add_new_item'      => 'Add New Type',
			'new_item_name'     => 'New Type',
			'menu_name'         => 'Types',
		],
		'description'       => 'Dealer Type: Dealer or Point of Sale',
		'public'            => true,
		'show_ui'           => true,
		'hierarchical'      => false,
		'meta_box_cb'       => null,
		'show_admin_column' => false,
	];
	register_taxonomy( 'dealer_type', [ 'dealer' ], $args );

	$args = [
		'labels'            => [
			'name'              => 'Brands',
			'singular_name'     => 'Brand',
			'search_items'      => 'Search Brands',
			'all_items'         => 'All Brands',
			'parent_item'       => 'Parent Brand',
			'parent_item_colon' => 'Parent Brand:',
			'edit_item'         => 'Edit Brand',
			'update_item'       => 'Update Brand',
			'add_new_item'      => 'Add New Brand',
			'new_item_name'     => 'New Brand',
			'menu_name'         => 'Brands',
		],
		'description'       => 'Dealer Brands',
		'public'            => true,
		'show_ui'           => true,
		'hierarchical'      => false,
		'meta_box_cb'       => null,
		'show_admin_column' => false,
	];
	register_taxonomy( 'brand', [ 'dealer' ], $args );
}

add_action( 'init', 'register_taxonomies' );

/**
 * Rewrite rules.
 */
function rewrite_rules() {
	// Теги.
	add_rewrite_tag( '%location%', '([^&]+)', 'location=' );
	add_rewrite_tag( '%dealer_type%', '([^&]+)', 'dealer_type=' );
	add_rewrite_tag( '%brand%', '([^&]+)', 'brand=' );

	// Правило перезаписи.
	add_rewrite_rule( '^dealers/([^/]*)/?([^/]*)?/?([^/]*)?/?', 'index.php?page_id=305&location=$matches[1]&dealer_type=$matches[2]&brand=$matches[3]', 'top' );

	// Скажем WP, что есть новые параметры запроса.
	add_filter(
		'query_vars',
		function ( $vars ) {
			$vars[] = 'location';
			$vars[] = 'dealer_type';
			$vars[] = 'brand';

			return $vars;
		}
	);
}

add_action( 'init', 'rewrite_rules' );

/**
 * Register Dealer custom post type.
 */
function register_cpt_dealer() {
	$labels = [
		'name'               => __( 'Dealers', 'dealer' ),
		'singular_name'      => __( 'Dealer', 'dealer' ),
		'add_new'            => __( 'Add New', 'dealer' ),
		'add_new_item'       => __( 'Add New Dealer', 'dealer' ),
		'edit_item'          => __( 'Edit Dealer', 'dealer' ),
		'new_item'           => __( 'New Dealer', 'dealer' ),
		'view_item'          => __( 'View Dealer', 'dealer' ),
		'search_items'       => __( 'Search Dealers', 'dealer' ),
		'not_found'          => __( 'Not Found', 'dealer' ),
		'not_found_in_trash' => __( 'Not Found In Trash', 'dealer' ),
		'parent_item'        => __( 'Parent', 'dealer' ),
		'parent_item_colon'  => __( 'Parent:', 'dealer' ),
		'menu_name'          => __( 'Millers Oils Dealers', 'dealer' ),
	];

	$args = [
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => 'Millers Oils Dealers',
		'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes' ],
		'taxonomies'          => [ 'dealer_type', 'location', 'brand' ],
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => get_stylesheet_directory_uri() . '/admin-menu-icon.svg',
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'dealer',
			'with_front' => false,
		],
		'capability_type'     => 'page',
	];

	register_post_type( 'dealer', $args );
}

add_action( 'init', 'register_cpt_dealer' );

/**
 * Shortcode to output content basing on current HTTP_HOST value
 *
 * @param array|string $atts    Arguments.
 * @param string|null  $content Content of the shortcode.
 * @param string       $tag     Tag of the shortcode.
 *
 * @return string
 */
function domain_shortcode( $atts, ?string $content, string $tag ) {
	if ( $content ) {
		$atts = shortcode_atts(
			[ 'host' => '' ],
			$atts
		);

		$content = do_shortcode( $content );
		$content = esc_attr( $content );
		$trans   = [
			'[' => '&#91;',
			']' => '&#93;',
		];
		$content = strtr( $content, $trans );
		// Shortcode is called as [domain][/domain]
		// Call itself in [domain ...] form
		// This is for a case when nested shortcodes are inside of this shortcode.
		return do_shortcode( '[domain host="' . $atts['host'] . '" content="' . $content . '"]' );
	}

	$atts = shortcode_atts(
		[
			'host'    => '',
			'content' => '',
		],
		$atts
	);

	$content = $atts['content'];

	if ( '' !== $atts['host'] ) {
		$host = isset( $_SERVER['HTTP_HOST'] ) ?
			filter_var( wp_unslash( $_SERVER['HTTP_HOST'] ), FILTER_SANITIZE_STRING ) :
			'';
		if ( false === mb_strpos( $host, $atts['host'] ) ) {
			$content = '';
		}
	}

	$trans   = [
		'&#91;' => '[',
		'&#93;' => ']',
	];
	$content = strtr( $content, $trans );
	$content = html_entity_decode( $content, ENT_QUOTES );

	return $content;
}

add_shortcode( 'domain', 'domain_shortcode' );

/**
 * Filters domain shortcode and makes it work as .ru domain on .test site.
 *
 * Returning a non-false value from filter will short-circuit the
 * shortcode generation process, returning that value instead.
 *
 * @param false|string $return      Short-circuit return value. Either false or the value to replace the shortcode with.
 * @param string       $tag         Shortcode name.
 * @param array|string $attr        Shortcode attributes array or empty string.
 * @param array        $m           Regular expression match array.
 *
 * @return false|string
 */
function mo_pre_do_shortcode_tag( $return, $tag, $attr, $m ) {
	$host = isset( $_SERVER['HTTP_HOST'] ) ?
		filter_var( wp_unslash( $_SERVER['HTTP_HOST'] ), FILTER_SANITIZE_STRING ) :
		'';
	if ( 'domain' === $tag && false !== mb_strpos( $host, '.test' ) && '.ru' === $attr['host'] ) {
		$attr['host'] = '.test';
		$content      = isset( $m[5] ) ? $m[5] : null;
		$return       = domain_shortcode( $attr, $content, $tag );
	}

	return $return;
}

add_filter( 'pre_do_shortcode_tag', 'mo_pre_do_shortcode_tag', 10, 4 );

/**
 * Output Google map
 *
 * @param array|string $atts    Arguments.
 * @param string|null  $content Content of the shortcode.
 * @param string       $tag     Tag of the shortcode.
 *
 * @return string HTML code of Google map
 */
function map_shortcode( $atts, ?string $content, string $tag ) {
	if ( $content ) {
		$content = do_shortcode( $content );
		// Shortcode is called as [map][/map]
		// Call itself in [map ...] form
		// This is for a case when nested shortcodes are inside of this shortcode.
		return do_shortcode( '[map ' . $content . ']' );
	}

	$objects = [];
	foreach ( $atts as $key => $value ) {
		if ( 'object' === substr( $key, 0, 6 ) ) {
			$objects[] = $value;
		}
	}

	$atts = shortcode_atts(
		[
			'center'            => 'auto',
			'zoom'              => 'auto',
			'width'             => '100%',
			'height'            => '400px',
			'marker'            => '',
			'marker_size'       => '',
			'marker_hover'      => '',
			'marker_hover_size' => '',
			'marker_click'      => '',
			'marker_click_size' => '',
			'title'             => '',
			'key'               => '',
		],
		$atts
	);

	if ( '' === $atts['center'] ) {
		$atts['center'] = '0, 0';
	}
	if ( 'auto' !== $atts['center'] ) {
		$atts['center'] = 'center: new google.maps.LatLng(' . $atts['center'] . "),\n";
	}
	if ( 'auto' !== $atts['zoom'] ) {
		$atts['zoom'] = 'zoom: ' . $atts['zoom'] . ",\n";
	}
	$atts['width']  = 'width: ' . $atts['width'] . ';';
	$atts['height'] = 'height: ' . $atts['height'] . ';';
	if ( ! $atts['marker_hover'] ) {
		$atts['marker_hover'] = $atts['marker'];
	}
	if ( ! $atts['marker_hover_size'] ) {
		$atts['marker_hover_size'] = $atts['marker_size'];
	}
	if ( ! $atts['marker_click'] ) {
		$atts['marker_click'] = $atts['marker'];
	}
	if ( ! $atts['marker_click_size'] ) {
		$atts['marker_click_size'] = $atts['marker_size'];
	}
	if ( $atts['key'] ) {
		$atts['key'] = '?key=' . $atts['key'];
	}

	ob_start();
	?>
	<link rel='dns-prefetch' href='//maps.googleapis.com'>
	<script type='text/javascript'
			src='https://maps.googleapis.com/maps/api/js<?php echo esc_html( $atts['key'] ); ?>'></script>
	<script type="text/javascript">
		// KAGG style for map
		var KAGG =
			[
				{
					'elementType': 'geometry',
					'stylers': [
						{
							'color': '#244059'
						}
					]
				},
				{
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#8ec3b9'
						}
					]
				},
				{
					'elementType': 'labels.text.stroke',
					'stylers': [
						{
							'color': '#1a3646'
						}
					]
				},
				{
					'featureType': 'administrative',
					'stylers': [
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'administrative.country',
					'elementType': 'geometry.stroke',
					'stylers': [
						{
							'color': '#4b6878'
						},
						{
							'visibility': 'on'
						}
					]
				},
				{
					'featureType': 'administrative.land_parcel',
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#64779e'
						}
					]
				},
				{
					'featureType': 'administrative.locality',
					'stylers': [
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'administrative.province',
					'elementType': 'geometry.stroke',
					'stylers': [
						{
							'color': '#4b6878'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'landscape',
					'elementType': 'labels',
					'stylers': [
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'landscape.man_made',
					'elementType': 'geometry.stroke',
					'stylers': [
						{
							'color': '#334e87'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'landscape.natural',
					'elementType': 'geometry',
					'stylers': [
						{
							'color': '#023e58'
						}
					]
				},
				{
					'featureType': 'poi',
					'stylers': [
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'poi',
					'elementType': 'geometry',
					'stylers': [
						{
							'color': '#283d6a'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'poi',
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#6f9ba5'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'poi',
					'elementType': 'labels.text.stroke',
					'stylers': [
						{
							'color': '#1d2c4d'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'poi.park',
					'elementType': 'geometry.fill',
					'stylers': [
						{
							'color': '#023e58'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'poi.park',
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#3c7680'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'road',
					'stylers': [
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'road',
					'elementType': 'geometry',
					'stylers': [
						{
							'color': '#304a7d'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'road',
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#98a5be'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'road',
					'elementType': 'labels.text.stroke',
					'stylers': [
						{
							'color': '#1d2c4d'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'road.highway',
					'elementType': 'geometry',
					'stylers': [
						{
							'color': '#2c6675'
						}
					]
				},
				{
					'featureType': 'road.highway',
					'elementType': 'geometry.stroke',
					'stylers': [
						{
							'color': '#255763'
						}
					]
				},
				{
					'featureType': 'road.highway',
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#b0d5ce'
						}
					]
				},
				{
					'featureType': 'road.highway',
					'elementType': 'labels.text.stroke',
					'stylers': [
						{
							'color': '#023e58'
						}
					]
				},
				{
					'featureType': 'transit',
					'stylers': [
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'transit',
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#98a5be'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'transit',
					'elementType': 'labels.text.stroke',
					'stylers': [
						{
							'color': '#1d2c4d'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'transit.line',
					'elementType': 'geometry.fill',
					'stylers': [
						{
							'color': '#283d6a'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'transit.station',
					'elementType': 'geometry',
					'stylers': [
						{
							'color': '#3a4762'
						},
						{
							'visibility': 'off'
						}
					]
				},
				{
					'featureType': 'water',
					'elementType': 'geometry',
					'stylers': [
						{
							'color': '#0e1626'
						}
					]
				},
				{
					'featureType': 'water',
					'elementType': 'labels.text.fill',
					'stylers': [
						{
							'color': '#4e6d70'
						},
						{
							'visibility': 'off'
						}
					]
				}
			];
		var Markers = [
			<?php
			foreach ( $objects as $object ) {
				echo '[' . $object . "],\n			";
			}
			?>
		];
		var infoWindows = [];
		var geoCoder;
		var map;

		function getLat( latLngString ) {
			latLngString = latLngString.replace( '(', '' );
			latLngString = latLngString.replace( ')', '' );
			latLngString = latLngString.trim();
			if ( latLngString === '' ) {
				return '';
			}
			return latLngString.split( ',' )[ 0 ].trim();
		}

		function getLng( latLngString ) {
			latLngString = latLngString.replace( '(', '' );
			latLngString = latLngString.replace( ')', '' );
			latLngString = latLngString.trim();
			if ( latLngString === '' ) {
				return '';
			}
			return latLngString.split( ',' )[ 1 ].trim();
		}

		function putMarker( name, address, latLngString, open_marker ) {
			var contentString = '<div class="infoWindow"><strong>' + name + '<\/strong>' +
				'<br>' + address + '<\/div>';
			var infowindow = new google.maps.InfoWindow( {
				content: contentString
			} );
			var lat = getLat( latLngString );
			var lng = getLng( latLngString );
			if ( ( lat === '' ) || ( lng === '' ) ) {
				return;
			}
			var latLng = new google.maps.LatLng( lat, lng );
			var marker = new google.maps.Marker( {
				map: map,
				position: latLng,
				size: new google.maps.Size(<?php echo esc_html( $atts['marker_size'] ); ?>),
				scaledSize: new google.maps.Size(<?php echo esc_html( $atts['marker_size'] ); ?>),
				title: name,
				optimized: false, /* without it, IE11 doesn't show marker */
				icon: marker_icon1
			} );

			if ( open_marker )
				infowindow.open( map, marker );

			marker.addListener( 'click', function() {
				for ( var i = 0; i < infoWindows.length; i++ ) {
					infoWindows[ i ].close( map, this );
				}
				marker.setIcon( marker_icon3 );
				infowindow.open( map, marker );
			} );

			google.maps.event.addListener( marker, 'mouseover', function() {
				marker.setIcon( marker_icon2 );
			} );
			google.maps.event.addListener( marker, 'mouseout', function() {
				marker.setIcon( marker_icon1 );
			} );
			infoWindows.push( infowindow );
		}

		function Title( controlDiv, map ) {
			var controlText = document.createElement( 'div' );
			controlDiv.className = 'map-header';
			controlText.innerHTML = "<?php echo esc_html( $atts['title'] ); ?>";
			controlDiv.appendChild( controlText );
		}

		function initMap() {
//            geoCoder = new google.maps.Geocoder();

			// Declare new style
			var KAGGStyledMap = new google.maps.StyledMapType( KAGG, { name: 'KAGG' } );

			<?php if ( 'auto' === $atts['center'] ) { ?>
			// Calculate auto center
			var bounds = new google.maps.LatLngBounds();

			for ( i = 0; i < Markers.length; i++ ) {
				var lat = getLat( Markers[ i ][ 2 ] );
				var lng = getLng( Markers[ i ][ 2 ] );
				var latLng = new google.maps.LatLng( lat, lng );
				bounds.extend( latLng );
			}
			<?php } ?>

			// Declare Map options
			var mapOptions = {
				<?php
				if ( 'auto' === $atts['center'] ) {
				?>
				center: bounds.getCenter(),
				<?php
				} else {
					echo esc_html( $atts['center'] );
				}
				?>

				<?php
				if ( 'auto' !== $atts['zoom'] ) {
					echo esc_html( $atts['zoom'] );
				}
				?>
				scrollwheel: false,
				mapTypeControl: false,
				streetViewControl: false,
				panControl: false,
				rotateControl: false,
				zoomControl: true,
				zoomControlOptions: {
					/*style: google.maps.ZoomControlStyle.SMALL,*/
					position: google.maps.ControlPosition.LEFT_CENTER
				}
			};

			// Create map
			map = new google.maps.Map( document.getElementById( 'map-canvas' ), mapOptions );
			<?php
			if ( 'auto' === $atts['zoom'] ) {
			?>
			map.fitBounds( bounds );
			<?php
			}
			?>

			// Setup skin for the map
//            map.mapTypes.set('KAGG_style', KAGGStyledMap);
//            map.setMapTypeId('KAGG_style');

			//add marker icons
			marker_icon1 = {
				url: "<?php echo esc_url( $atts['marker'] ); ?>",
				<?php
				// To use .svg, it must contain width and height like in example below.
				/*
				 url: 'data:image/svg+xml;utf-8, \
				 <svg width="46" height="55" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" enable-background="new 0 0 60 60"> \
				 <path opacity="0.2" d="M52.9,52c0,3-9.9,5.5-22,5.5c-12.2,0-22-2.5-22-5.5s9.9-5.5,22-5.5C43.1,46.5,52.9,48.9,52.9,52z"/> \
				 <polygon fill="#5190FC" points="9.5,10.9 12.5,9.3 14.5,8.4 16.7,7.5 18.9,6.8 21.1,6.2 23.4,5.7 25.7,5.4 28.1,5.2 30.4,5.1 \
				 32.7,5.2 35,5.4 37.3,5.7 39.6,6.2 41.8,6.8 44.1,7.5 46.2,8.4 48.4,9.4 51.3,10.9 50.1,13.1 49.3,14.4 48.5,15.8 47.6,17.3 \
				 46.7,18.9 45.8,20.6 44.7,22.3 43.7,24.2 41.4,28.1 40.2,30.2 39,32.3 37.8,34.4 35.2,38.8 32.7,43.3 30.5,47.1 "/> \
				 <path fill="#FFFFFF" d="M30.4,6.1l2.3,0.1l2.3,0.2l2.2,0.3l2.2,0.5l2.2,0.6l2.1,0.7l2.1,0.8l2.1,1l2,1.1l-0.7,1.2l-0.8,1.3l-0.8,1.4 \
				 l-0.9,1.5l-0.9,1.6l-1,1.7l-1,1.8l-1.1,1.9l-1.1,1.9l-1.1,2l-1.2,2l-1.2,2.1l-1.2,2.1l-1.3,2.2l-1.3,2.2l-1.3,2.2l-1.3,2.2l-1.3,2.3 \
				 L10.9,11.3l2-1.1l2-0.9l2.1-0.8l2.2-0.7l2.2-0.6l2.2-0.4l2.2-0.3l2.3-0.2L30.4,6.1 M30.4,4.1L30.4,4.1l-2.3,0.1l-0.1,0l-0.1,0 \
				 l-2.3,0.2l0,0l0,0l-2.2,0.3l-0.1,0l-0.1,0L21,5.2l-0.1,0l-0.1,0l-2.2,0.6l-0.1,0l0,0l-2.2,0.7l-0.1,0l-0.1,0l-2.1,0.8l-0.1,0l-0.1,0 \
				 l-2,0.9l0,0l0,0l-2,1.1l-1.8,1l1,1.8l19.6,33.8l1.7,3l1.7-3l1.3-2.3l1.3-2.3l1.3-2.2l1.3-2.2l1.3-2.2l1.2-2.1l1.2-2.2l1.2-2l1.2-2 \
				 l1.1-1.9l1.1-1.9l1-1.7l1-1.7l0.9-1.6l0.9-1.4l0.8-1.4l0.8-1.3l0.7-1.2l1-1.8l-1.8-1l-2-1.1l0,0l0,0l-2.1-1l-0.1,0l-0.1,0l-2.1-0.8 \
				 l0,0l0,0l-2.1-0.7l-0.1,0l-0.1,0l-2.2-0.6l-0.1,0l-0.1,0l-2.2-0.5l0,0l-0.1,0l-2.2-0.3l-0.1,0l-0.1,0l-2.3-0.2l-0.1,0l-0.1,0 \
				 L30.4,4.1L30.4,4.1L30.4,4.1z"/> \
				 </svg>',
				 */
				?>
				size: new google.maps.Size(<?php echo esc_html( $atts['marker_size'] ); ?>),
				scaledSize: new google.maps.Size(<?php echo esc_html( $atts['marker_size'] ); ?>)
			};
			marker_icon2 = {    // on hover
				url: "<?php echo esc_url( $atts['marker_hover'] ); ?>",
				size: new google.maps.Size(<?php echo esc_html( $atts['marker_hover_size'] ); ?>),
				scaledSize: new google.maps.Size(<?php echo esc_html( $atts['marker_hover_size'] ); ?>)
			};
			marker_icon3 = {    // on click
				url: "<?php echo esc_url( $atts['marker'] ); ?>",
				size: new google.maps.Size(<?php echo esc_html( $atts['marker_click_size'] ); ?>),
				scaledSize: new google.maps.Size(<?php echo esc_html( $atts['marker_click_size'] ); ?>)
			};
			var open_marker = false;

			for ( var i = 0; i < Markers.length; i++ ) {
				open_marker = false;
				putMarker( Markers[ i ][ 0 ], Markers[ i ][ 1 ], Markers[ i ][ 2 ], open_marker );
			}

			<?php
			if ( $atts['title'] ) {
			?>

			var titleDiv = document.createElement( 'div' );
			var title = new Title( titleDiv, map );

			titleDiv.index = 1;
			map.controls[ google.maps.ControlPosition.TOP_CENTER ].push( titleDiv );

			<?php
			}
			?>
		}

		google.maps.event.addDomListener( window, 'load', initMap );

	</script>
	<div class="ourLocation">
		<div class="map" id="map-canvas"
			 style="<?php echo esc_attr( $atts['width'] ); ?><?php echo esc_attr( $atts['height'] ); ?>"></div>
	</div>
	<?php

	return ob_get_clean();
}

add_shortcode( 'map', 'map_shortcode' );

/**
 * Compare terms by id
 *
 * @param object $a Term.
 * @param object $b Term.
 *
 * @return bool
 */
function compare_terms_by_id( object $a, object $b ) {
	return ( $a->term_id > $b->term_id );
}

/**
 * Get dealer data from post fields.
 *
 * @param array|string $atts    Arguments.
 * @param string|null  $content Content of the shortcode.
 * @param string       $tag     Tag of the shortcode.
 *
 * @return bool|mixed|string
 */
function get_dealer_data( $atts, ?string $content, string $tag ) {
	global $post;

	if ( isset( $post->original_id ) ) {
		$original_id = $post->original_id;
		$original    = get_post( $original_id );
	} else {
		return '';
	}

	switch ( $tag ) {
		case 'dealer_title':
			return esc_html( $original->post_title );
		case 'dealer_address':
			return esc_html( get_post_meta( $original_id, 'mo_address', true ) );
		case 'dealer_coordinates':
			return esc_html( get_post_meta( $original_id, 'mo_coordinates', true ) );
		case 'dealer_phone':
			return esc_html( get_post_meta( $original_id, 'mo_phone', true ) );
		case 'dealer_website':
			return esc_url( get_post_meta( $original_id, 'mo_website', true ) );
		case 'dealer_website_name':
			$data = esc_url( get_post_meta( $original_id, 'mo_website', true ) );
			// Remove 'http(s)://'.
			$pos = strpos( $data, '//' );
			if ( $pos ) {
				$data = substr( $data, $pos + 2 );
			}
			// Remove last slash.
			$pos = strrpos( $data, '/' );
			if ( ( strlen( $data ) - 1 ) === $pos ) {
				$data = substr( $data, 0, strlen( $data ) - 1 );
			}

			return $data;
		case 'dealer_email':
			return esc_html( get_post_meta( $original_id, 'mo_email', true ) );
		case 'dealer_brands':
			$data  = '';
			$terms = get_the_terms( $original, 'brand' );
			if ( $terms ) {
				usort( $terms, 'compare_terms_by_id' );
				foreach ( $terms as $term ) {
					$image_id = get_term_meta( $term->term_id, 'term_image_id', true );

					$data .= '<br />' . wp_get_attachment_image( $image_id, 'full' );
				}
			}

			return $data;
		case 'dealer_type':
			$data  = '';
			$terms = get_the_terms( $original, 'dealer_type' );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					if ( $data ) {
						$data .= ' и ' . mb_strtolower( $term->name );
					} else {
						$data .= mb_strtolower( $term->name );
					}
				}
			}

			return $data;
		default:
	}

	return '';
}

add_shortcode( 'dealer_title', 'get_dealer_data' );
add_shortcode( 'dealer_address', 'get_dealer_data' );
add_shortcode( 'dealer_coordinates', 'get_dealer_data' );
add_shortcode( 'dealer_phone', 'get_dealer_data' );
add_shortcode( 'dealer_website', 'get_dealer_data' );
add_shortcode( 'dealer_website_name', 'get_dealer_data' );
add_shortcode( 'dealer_email', 'get_dealer_data' );
add_shortcode( 'dealer_brands', 'get_dealer_data' );
add_shortcode( 'dealer_type', 'get_dealer_data' );

/**
 * Shortcode to output list of dealers.
 *
 * @param array|string $atts Shortcode attributes.
 *
 * @return mixed
 */
function dealer_list_shortcode( $atts ) {
	global $post;

	$atts = shortcode_atts(
		[
			'dealer_type' => get_query_var( 'dealer_type', '' ),
			'location'    => get_query_var( 'location', '' ),
			'brand'       => get_query_var( 'brand', '' ),
			'number'      => - 1,
			'order'       => 'ASC',
			'orderby'     => 'date',
		],
		$atts
	);

	$args = [
		'post_type'      => 'dealer',
		'post_status'    => 'publish',
		'dealer_type'    => $atts['dealer_type'],
		'location'       => $atts['location'],
		'brand'          => $atts['brand'],
		'posts_per_page' => $atts['number'],
		'order'          => strtoupper( $atts['order'] ),
		'orderby'        => $atts['orderby'],
	];

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$counter       = 1;
		$return_string = '';
		while ( $query->have_posts() ) {
			$query->the_post();
			$return_string .= '<p>';

			$website = get_post_meta( $post->ID, 'mo_website', true );
			if ( $website ) {
				$title = $counter . '. <a href="' . esc_html( $website );

				$title .= '" target="_blank" rel="noopener">' . $post->post_title . '</a><br>';
			} else {
				$title = $counter . '. ' . $post->post_title . '<br>';
			}
			$return_string .= $title;
			$return_string .= esc_html( get_post_meta( $post->ID, 'mo_address', true ) ) . '<br>';
			$return_string .= 'Тел.: ' . esc_html( get_post_meta( $post->ID, 'mo_phone', true ) ) . '<br>';
			$return_string .= '<a href="' . get_permalink() . '">Подробная информация</a>';
			$return_string .= '</p>';
			$counter ++;
		}
	} else {
		$return_string = 'Ничего не найдено';
	}

	wp_reset_postdata();

	return $return_string;
}

add_shortcode( 'dealer_list', 'dealer_list_shortcode' );

/**
 * Shortcode to output list of dealers.
 *
 * @param array|string $atts Shortcode attributes.
 *
 * @return mixed
 */
function dealer_map_objects_shortcode( $atts ) {
	global $post;

	$atts = shortcode_atts(
		[
			'dealer_type' => get_query_var( 'dealer_type', '' ),
			'location'    => get_query_var( 'location', '' ),
			'brand'       => get_query_var( 'brand', '' ),
			'number'      => - 1,
			'order'       => 'ASC',
			'orderby'     => 'date',
		],
		$atts
	);

	$args = [
		'post_type'      => 'dealer',
		'post_status'    => 'publish',
		'dealer_type'    => $atts['dealer_type'],
		'location'       => $atts['location'],
		'brand'          => $atts['brand'],
		'posts_per_page' => $atts['number'],
		'order'          => strtoupper( $atts['order'] ),
		'orderby'        => $atts['orderby'],
	];

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$counter       = 1;
		$return_string = '';
		while ( $query->have_posts() ) {
			$query->the_post();
			$return_string .= 'object' . $counter . '="';
			$return_string .= '\'' . esc_html( $post->post_title ) . '\', ';
			$return_string .= '\'' . esc_html( get_post_meta( $post->ID, 'mo_address', true ) ) . '\', ';
			$return_string .= '\'(' . esc_html( get_post_meta( $post->ID, 'mo_coordinates', true ) ) . ')\'';
			$return_string .= '" ';
			$counter ++;
		}
	} else {
		$return_string = '';
	}

	wp_reset_postdata();

	return $return_string;
}

add_shortcode( 'dealer_map_objects', 'dealer_map_objects_shortcode' );

/**
 * Dealers shortcode.
 *
 * @return false|string
 */
function dealers_shortcode() {
	ob_start();
	?>
	<div id="molabel">
		<div id="molabelheader">
			<strong>Где купить</strong>
		</div>
		<div id="molabelitems">
			<table style="border: none;">
				<tbody>
				<tr style="background: white;">
					<td><a title="Глобально" href="http://www.millersoils.co.uk/distributorsnonuk.asp"><span
									style="font-size: 16px; font-weight: bold;">Глобально</span></a></td>
					<td><img
								class="alignnone size-full wp-image-306"
								src="/wp-content/uploads/2015/03/Globe.gif" alt="Globe" width="75"
								height="75"/></td>
				</tr>
				<tr style="background: white;">
					<td><span style="font-size: 16px; font-weight: bold; line-height: 1.5;">В России<br/><a
									href="/dealers/?location=russia&dealer_type=dealer">Дилеры</a><br/><a
									href="/dealers/?location=russia&dealer_type=pos">Точки продаж</a></span></td>
					<td><img
								class="alignnone wp-image-2701 size-full"
								src="/wp-content/uploads/2016/05/russian-flag.png" width="75"
								height="75"/></td>
				</tr>
				<tr style="background: white;">
					<td><span style="font-size: 16px; font-weight: bold; line-height: 1.5;">В Киргизии<br/><a
									href="/dealers/?location=kyrgyzstan&dealer_type=dealer">Дилеры</a></span></td>
					<td><img
								class="alignnone wp-image-2700 size-full"
								src="/wp-content/uploads/2017/04/kyrgyz-flag-site.png" width="75"
								height="75"/></td>
				</tr>
				<tr style="background: white;">
					<td><span style="font-size: 16px; font-weight: bold; line-height: 1.5;">В Латвии<br/><a
									href="/dealers/?location=latvia&dealer_type=dealer">Дилеры</a></span></td>
					<td><img
								class="alignnone wp-image-2700 size-full"
								src="/wp-content/uploads/2016/05/latvian-flag.png" width="75"
								height="75"/></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div id="molabelfooter">
		</div>
	</div>
	<div style="max-width: 243px;">
		<p><img class="size-full wp-image-288 aligncenter"
				src="/wp-content/uploads/2015/03/tech-helpdesk.jpg" alt="tech-helpdesk" width="200"
				height="164"/></p>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'dealers', 'dealers_shortcode' );

/**
 * Добавляет возможность загружать изображения для элементов указанных таксономий - категории, метки.
 *
 * Получить ID картинки термина: $image_id = get_term_meta( $term_id, 'term_image_id', 1 );
 * Затем получить URL картинки: $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
 *
 * @Author: Kama
 *
 * @ver   : 1.1
 */
if ( ! class_exists( 'Term_Meta_Image' ) ) {
	/**
	 * Class Term_Meta_Image
	 */
	class Term_Meta_Image {

		/**
		 * Для каких таксономий должен работать код.
		 *
		 * @var string[]
		 */
		public $for_taxes = [ 'brand' ];

		/**
		 * Term_Meta_Image constructor.
		 */
		public function __construct() {

			foreach ( $this->for_taxes as $taxname ) {
				add_action( "{$taxname}_add_form_fields", [ & $this, 'add_term_image' ], 10, 2 );
				add_action( "{$taxname}_edit_form_fields", [ & $this, 'update_term_image' ], 10, 2 );
				add_action( "created_{$taxname}", [ & $this, 'save_term_image' ], 10, 2 );
				add_action( "edited_{$taxname}", [ & $this, 'updated_term_image' ], 10, 2 );

				add_filter( "manage_edit-{$taxname}_columns", [ & $this, 'add_image_column' ] );
				add_filter( "manage_{$taxname}_custom_column", [ & $this, 'fill_image_column' ], 10, 3 );
			}
		}

		/**
		 * Add a form field in the new category page.
		 *
		 * @param string $taxonomy The taxonomy slug.
		 */
		public function add_term_image( string $taxonomy ) {
			wp_enqueue_media(); // Подключим стили медиа, если их нет.

			add_action( 'admin_print_footer_scripts', [ & $this, 'add_script' ], 99 );
			?>
			<div class="form-field term-group">
				<label for="term_image_id">
					<?php esc_html_e( 'Image', 'text_domain' ); ?>
				</label>
				<input type="hidden" id="term_image_id" name="term_image_id" class="custom_media_url" value="">
				<div id="term__image__wrapper"></div>
				<p>
					<input
							type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button"
							name="ct_tax_media_button"
							value="<?php esc_html_e( 'Add Image', 'text_domain' ); ?>"/>
					<input
							type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove"
							name="ct_tax_media_remove"
							value="<?php esc_html_e( 'Remove Image', 'text_domain' ); ?>"/>
				</p>
			</div>
			<?php
		}

		/**
		 * Edit the form field.
		 *
		 * @param WP_Term $term     Current taxonomy term object.
		 * @param string  $taxonomy Current taxonomy slug.
		 */
		public function update_term_image( WP_Term $term, string $taxonomy ) {
			wp_enqueue_media(); // Подключим стили медиа, если их нет.

			add_action( 'admin_print_footer_scripts', [ & $this, 'add_script' ], 99 );

			$image_id = get_term_meta( $term->term_id, 'term_image_id', true );
			?>
			<tr class="form-field term-group-wrap">
				<th scope="row">
					<label for="term_image_id"><?php esc_html_e( 'Image', 'text_domain' ); ?></label>
				</th>
				<td>
					<input
							type="hidden" id="term_image_id" name="term_image_id"
							value="<?php echo esc_html( $image_id ); ?>">
					<div id="term__image__wrapper">
						<?php
						if ( $image_id ) {
							echo wp_get_attachment_image( $image_id, 'thumbnail' );
						}
						?>
					</div>
					<p>
						<input
								type="button" class="button button-secondary ct_tax_media_button"
								id="ct_tax_media_button" name="ct_tax_media_button"
								value="<?php esc_html_e( 'Add Image', 'text_domain' ); ?>"/>
						<input
								type="button" class="button button-secondary ct_tax_media_remove"
								id="ct_tax_media_remove" name="ct_tax_media_remove"
								value="<?php esc_html_e( 'Remove Image', 'text_domain' ); ?>"/>
					</p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Save the form field.
		 *
		 * @param int $term_id Term ID.
		 * @param int $tt_id   Term taxonomy ID.
		 */
		public function save_term_image( int $term_id, int $tt_id ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['term_image_id'] ) && '' !== $_POST['term_image_id'] ) {
				$image = sanitize_text_field( wp_unslash( $_POST['term_image_id'] ) );
				add_term_meta( $term_id, 'term_image_id', $image, true );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * Update the form field value.
		 *
		 * @param int $term_id Term ID.
		 * @param int $tt_id   Term taxonomy ID.
		 */
		public function updated_term_image( int $term_id, int $tt_id ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['term_image_id'] ) && '' !== $_POST['term_image_id'] ) {
				$image = sanitize_text_field( wp_unslash( $_POST['term_image_id'] ) );
				update_term_meta( $term_id, 'term_image_id', $image );
			} else {
				update_term_meta( $term_id, 'term_image_id', '' );
			}
			// phpcs:disable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * Add script.
		 */
		public function add_script() {
			?>
			<script>
				jQuery( document ).ready( function( $ ) {
					function ct_media_upload( button_class ) {
						var _custom_media = true,
							_orig_send_attachment = wp.media.editor.send.attachment;

						$( 'body' ).on( 'click', button_class, function( e ) {
							var button_id = '#' + $( this ).attr( 'id' );
							var send_attachment_bkp = wp.media.editor.send.attachment;
							var button = $( button_id );

							_custom_media = true;

							wp.media.editor.send.attachment = function( props, attachment ) {
								if ( _custom_media ) {
									$( '#term_image_id' ).val( attachment.id );
									$( '#term__image__wrapper' ).html( '<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />' );
									$( '#term__image__wrapper .custom_media_image' ).attr( 'src', attachment.sizes.thumbnail.url ).css( 'display', 'block' );
								} else {
									return _orig_send_attachment.apply( button_id, [ props, attachment ] );
								}
							};
							wp.media.editor.open( button );
							return false;
						} );
					}

					ct_media_upload( '.ct_tax_media_button.button' );

					$( 'body' ).on( 'click', '.ct_tax_media_remove', function() {
						$( '#term_image_id' ).val( '' );
						$( '#term__image__wrapper' ).html( '<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />' );
					} );

					// Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
					$( document ).ajaxComplete( function( event, xhr, settings ) {
						var queryStringArr = settings.data.split( '&' );

						if ( $.inArray( 'action=add-tag', queryStringArr ) !== -1 ) {
							var xml = xhr.responseXML;
							$response = $( xml ).find( 'term_id' ).text();

							if ( $response !== '' ) {
								$( '#term__image__wrapper' ).html( '' ); // Clear the thumb image
							}
						}
					} );
				} );
			</script>
			<?php
		}

		/**
		 * Добавляет колонку картинки в таблицу терминов.
		 *
		 * @param array $columns Columns.
		 *
		 * @return array|string[]
		 */
		public function add_image_column( array $columns ) {
			// Подправим ширину колонки через css.
			add_action(
				'admin_notices',
				function () {
					echo '<style>.column-image{ width:60px; text-align:center; }</style>';
				}
			);

			$num = 1; // После какой по счету колонки вставлять новые.

			$new_columns = [ 'image' => 'Картинка' ];

			return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
		}

		/**
		 * Fill image column.
		 *
		 * @param string $string      Blank string.
		 * @param string $column_name Name of the column.
		 * @param int    $term_id     Term ID.
		 *
		 * @return string
		 */
		public function fill_image_column( string $string, string $column_name, int $term_id ) {
			// Если есть картинка.
			if ( 'image' === $column_name ) {
				$image_id = get_term_meta( $term_id, 'term_image_id', 1 );
				if ( $image_id ) {
					$string = '<img src="' . wp_get_attachment_image_url( $image_id, 'thumbnail' ) . '" width="100%" height="auto" style="vertical-align: middle;" alt="" />';
				}
			}

			return $string;
		}
	}

	new Term_Meta_Image(); // Init.
}

/* Development section */

/**
 * Wraps multibyte string by words.
 *
 * @param string $str   Text containing several lines.
 * @param int    $width Maximum number of chars in output line.
 * @param string $break End of line symbol.
 * @param bool   $cut   Cut word if it is longer than $width.
 *
 * @return string
 */
function mb_wordwrap( $str, $width = 75, $break = "\n", $cut = false ) {
	$lines = explode( $break, $str );
	foreach ( $lines as &$line ) {
		$line = rtrim( $line );
		if ( mb_strlen( $line ) <= $width ) {
			continue;
		}
		$words  = explode( ' ', $line );
		$line   = '';
		$actual = '';
		foreach ( $words as $word ) {
			if ( mb_strlen( $actual . $word ) <= $width ) {
				$actual .= $word . ' ';
			} else {
				if ( '' !== $actual ) {
					$line .= rtrim( $actual ) . $break;
				}
				$actual = $word;
				if ( $cut ) {
					while ( mb_strlen( $actual ) > $width ) {
						$line .= mb_substr( $actual, 0, $width ) . $break;

						$actual = mb_substr( $actual, $width );
					}
				}
				$actual .= ' ';
			}
		}
		$line .= trim( $actual );
	}

	return implode( $break, $lines );
}

/**
 * Update SEO.
 */
function update_seo() {
	$current_user = wp_get_current_user();
	if ( 'mscomponents' !== $current_user->user_login ) {
		return;
	}

	$args           = [
		'numberposts'      => - 1,
		'post_type'        => 'product',
		'suppress_filters' => false,
	];
	$post_count     = 0;
	$post_processed = 0;
	$posts          = get_posts( $args );
	foreach ( $posts as $post ) {
		$post_count ++;
		$id = $post->ID;

		$content = $post->post_content;
		$from    = '<h4>Описание</h4>';
		$to      = '<h4>';
		$pos1    = mb_strpos( $content, $from );
		$pos1    = $pos1 + mb_strlen( $from );
		$pos2    = mb_strpos( $content, $to, $pos1 );
		$desc    = '';
		if ( ( $pos1 >= 0 ) && ( $pos2 >= 0 ) ) {
			$desc = mb_substr( $content, $pos1, $pos2 - $pos1 );
			$desc = esc_html( $desc );
			$desc = trim( $desc );
		}

		if ( mb_strlen( $desc ) > 156 ) {
			$desc = mb_substr( $desc, 0, 156 );
			$desc = mb_wordwrap( $desc, 155 );
			$i    = mb_strpos( $desc, "\n" );
			if ( $i ) {
				$desc = mb_substr( $desc, 0, $i );
			}
			$desc = $desc . '…';
		}

		$focus = $post->post_title;

		update_post_meta( $id, '_yoast_wpseo_focuskw', $focus );
		update_post_meta( $id, '_yoast_wpseo_focuskw_text_input', $focus );
		update_post_meta( $id, '_yoast_wpseo_metadesc', $desc );

		$post_processed ++;
	}
	// write_log( '$post_processed: ' . $post_processed );
}

//add_action ('init', 'update_seo');	// for development purposes only

/* End of development section */
