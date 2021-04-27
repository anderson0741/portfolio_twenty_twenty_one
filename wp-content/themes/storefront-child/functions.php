<?php
/**
 * DollarClubs functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package DollarClub
 */

 if ( ! function_exists( 'dollarclub_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function dollarclub_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on DollarClub, use a find and replace
		 * to change 'dollarclub' to the name of your theme in all the template files.
		 */
		// load_theme_textdomain( 'storefront-child', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		// add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'dollarclub' ),
		) );
		register_nav_menus( array(
			'menu-2' => esc_html__( 'Mobile', 'dollarclub' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
endif;
add_action( 'after_setup_theme', 'dollarclub_setup' );

/**
 * Redirect at checkout if user isnt logged in
 */

add_action( 'template_redirect', 'redirect_to_login' );

function redirect_to_login() {

  if ( is_page(8) && ! is_user_logged_in() ) {

    wp_redirect( '/login/', 301 );
    exit;
  }
  if ( is_page(9) && ! is_user_logged_in() ) {

    wp_redirect( '/login/', 301 );
    exit;
  }
  if ( is_page(174) && ! is_user_logged_in() ) {

    wp_redirect( '/login/', 301 );
    exit;
  }
}

// PLP archive

  add_filter( 'woocommerce_product_add_to_cart_text', 'plp_add_to_cart_button' );
  function plp_add_to_cart_button() {
    return __( 'Donate', 'donate-slug' );
  }

  remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

  // add_filter('posts_clauses', 'order_by_stock_status');
  //
  // function order_by_stock_status($posts_clauses) {
  //   global $wpdb;
  //   // only change query on WooCommerce loops
  //   if (is_woocommerce() && (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy())) {
  //       $posts_clauses['join'] .= " INNER JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id) ";
  //       $posts_clauses['orderby'] = " istockstatus.meta_value ASC, " . $posts_clauses['orderby'];
  //       $posts_clauses['where'] = " AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '' " . $posts_clauses['where'];
  //   }
  //   return $posts_clauses;
  // }

// PLP END

// PDP

  // remove_theme_support( 'wc-product-gallery-zoom' ); Not Currently working
  // remove_theme_support( 'wc-product-gallery-lightbox' ); Not Currently working
  remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

  add_action( 'woocommerce_before_add_to_cart_quantity', 'dollar_sign_front_add_cart' );

  function dollar_sign_front_add_cart() {
   echo '<div class="product-dollar-sign">$</div>';
  }

  add_filter( 'woocommerce_product_single_add_to_cart_text', 'pdp_add_to_cart_text' );
  function pdp_add_to_cart_text() {
    return __( 'Donate!', 'donation-slug' );
  }

  // Remove in stock message

    function my_wc_hide_in_stock_message( $html, $text, $product ) {

    	$availability = $product->get_availability();

    	if ( isset( $availability['class'] ) && 'in-stock' === $availability['class'] ) {
    		return '';
    	}

    	return $html;

    }

    // add_filter( 'woocommerce_stock_html', 'my_wc_hide_in_stock_message', 10, 3 );

  // End

  // PDP Change out of stock message

    function availability_filter_func($availability) {

      $availability['availability'] = str_ireplace('Out of stock', 'Campaign Complete!', $availability['availability']);
      $availability['availability'] = str_ireplace('in stock', 'Until Funded!', '$' .$availability['availability']);

      return $availability;

    }

    add_filter('woocommerce_get_availability', 'availability_filter_func');

  // End

// PDP End

add_action('init','delay_remove');
function delay_remove(){
	remove_action('woocommerce_after_shop_loop','woocommerce_catalog_ordering', 10);
	remove_action('woocommerce_before_shop_loop','woocommerce_catalog_ordering', 10);
	remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
	remove_action( 'storefront_header', 'storefront_primary_navigation', 50 );
	add_action( 'storefront_header', 'storefront_primary_navigation', 30 );
	if ( function_exists( 'storefront_header_cart' ) ) {
		remove_action( 'storefront_header', 'storefront_header_cart', 60 );
		if ( function_exists( 'Storefront_WooCommerce_Customiser' ) ) {
			$header_cart = get_theme_mod( 'swc_header_cart' );
			if ( false == $header_cart ) {
				// don't add back the cart
			} else {
				add_action( 'storefront_header', 'storefront_header_cart', 35 );
			}
		} else {
			add_action( 'storefront_header', 'storefront_header_cart', 35 );
		}
	}
}
add_filter( 'storefront_menu_toggle_text', 'jk_storefront_menu_toggle_text' );
function jk_storefront_menu_toggle_text( $text ) {
	$text = __( '' );
	return $text;
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once dirname( __FILE__ ) . '/inc/woocommerce.php';
}

function dollarclub_scripts() {
  wp_enqueue_style( 'dollarclub-style', get_stylesheet_directory_uri() . '/dist/css/app.css' );
  wp_enqueue_script( 'dollarclub-bundle', get_stylesheet_directory_uri() . '/dist/app.js', array(), '20190923', true );
}

function dollarclub_login_scripts() {
	wp_enqueue_style( 'dollarclub-style', get_stylesheet_directory_uri() . '/dist/css/login.css' );
}

add_action( 'login_enqueue_scripts', 'dollarclub_login_scripts' );

add_action( 'wp_enqueue_scripts', 'dollarclub_scripts', 20 );

function remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'wp', 'remove_image_zoom_support', 100 );

// Blog Section

/**
 * Custom template tags for this theme.
 */
require dirname( __FILE__ ) .  '/inc/template-tags.php';

function dollarclub_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Widget Area 1', 'dollarclub' ),
		'id'            => 'blog-widget-area-1',
		'description'   => esc_html__( 'Add widgets here.', 'dollarclub' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<span class="widget-title">',
		'after_title'   => '</span>',
	) );
  /*
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Widget Area 2', 'dollarclub' ),
		'id'            => 'blog-widget-area-2',
		'description'   => esc_html__( 'Add widgets here.', 'dollarclub' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<span class="widget-title">',
		'after_title'   => '</span>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Widget Area 3', 'dollarclub' ),
		'id'            => 'blog-widget-area-3',
		'description'   => esc_html__( 'Add widgets here.', 'dollarclub' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<span class="widget-title">',
		'after_title'   => '</span>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Widget Area 4', 'dollarclub' ),
		'id'            => 'blog-widget-area-4',
		'description'   => esc_html__( 'Add widgets here.', 'dollarclub' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<span class="widget-title">',
		'after_title'   => '</span>',
	) );
  */
  register_sidebar( array(
    'name'          => esc_html__( 'Blog Widget Featured Products', 'dollarclub' ),
    'id'            => 'blog-widget-featured-products',
    'description'   => esc_html__( 'Add product widgets here.', 'dollarclub' ),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<span class="widget-title">',
    'after_title'   => '</span>',
  ) );
  register_sidebar( array(
    'name'          => esc_html__( 'Footer Copyright', 'dollarclub' ),
    'id'            => 'sidebar-footer-copyright',
    'description'   => esc_html__( 'Add widgets here.', 'dollarclub' ),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<div class="widget-title">',
    'after_title'   => '</div>',
  ) );
}

add_action( 'widgets_init', 'dollarclub_widgets_init' );

// End Blog Section

// Checkout Section

add_filter( 'woocommerce_checkout_login_message', 'bbloomer_return_customer_message' );

function bbloomer_return_customer_message() {
  return 'Please Login before checkout.';
}

// End Checkout Section

// My Account Section

  add_filter( 'woocommerce_account_menu_items', 'custom_remove_downloads_my_account', 999 );

  function custom_remove_downloads_my_account( $items ) {
    unset($items['downloads']);
    return $items;
  }

  add_filter('validate_username' , 'custom_validate_username', 10, 2);

  function custom_validate_username($valid, $username ) {
  		if (preg_match("/\\s/", $username)) {
     			// there are spaces
        wc_add_notice( "Username cannot contain a space.", "error" );
  			return $valid=false;
  		}
  	return $valid;
  }


  add_filter ( 'woocommerce_account_menu_items', 'misha_one_more_link' );
  function misha_one_more_link( $menu_links ){

  	$new = array( 'user' => 'Profile' );
  	$menu_links = array_slice( $menu_links, 0, 1, true )
  	+ $new
  	+ array_slice( $menu_links, 1, NULL, true );


  	return $menu_links;


  }

  add_filter( 'woocommerce_get_endpoint_url', 'misha_hook_endpoint', 10, 4 );
  function misha_hook_endpoint( $url, $endpoint, $value, $permalink ){

  	if( $endpoint === 'user' ) {

  		$url = site_url() . '/user/';

  	}

  	return $url;

  }

// End My Account Section

// Adding number over cart icon
add_filter('wp_nav_menu_items','wc_menu_cart_number', 10, 2);
function wc_menu_cart_number($menu, $args) {
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'primary' !== $args->theme_location )
		return $menu;
	ob_start();
		global $woocommerce;
		$cart_contents_count = $woocommerce->cart->cart_contents_count;
    $menu_item = '';

		if ( $cart_contents_count > 0 ) {
			if ($cart_contents_count == 0) {
				$menu_item = '';
			}
			$menu_item .= '<li class="cart-number"><a class="cart-number-link" href="/cart/">';
			$menu_item .= $cart_contents_count;
			$menu_item .= '</a></li>';
		}
		echo $menu_item;
	$social = ob_get_clean();
	return $menu . $social;
}

// Ultimate Member spam catch
add_action('um_submit_form_errors_hook_','um_custom_validate_username', 999, 1);
function um_custom_validate_username( $args ) {
	if ( isset( $args['first_name'] ) && strstr( $args['first_name'], $args['last_name'] ) ) {
		UM()->form()->add_error( 'first_name', 'Your first name cant be the same as your last name.' );
	}
	if ( isset( $args['last_name'] ) && strstr( $args['last_name'], $args['first_name'] ) ) {
		UM()->form()->add_error( 'last_name', 'Your last name cant be the same as your first name.' );
	}
}
