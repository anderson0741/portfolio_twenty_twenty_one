<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package DollarClub
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)-in-3.0.0
 *
 * @return void
 */
function dollarclub_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'wc-product-gallery-lightbox');
}
add_action( 'after_setup_theme', 'dollarclub_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function dollarclub_woocommerce_scripts() {
	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'dollarclub-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'dollarclub_woocommerce_scripts', 20 );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function dollarclub_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'dollarclub_woocommerce_active_body_class' );

/**
 * Products per page.
 *
 * @return integer number of products.
 */
function dollarclub_woocommerce_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'dollarclub_woocommerce_products_per_page' );

/**
 * Product gallery thumnbail columns.
 *
 * @return integer number of columns.
 */
function dollarclub_woocommerce_thumbnail_columns() {
	return 4;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'dollarclub_woocommerce_thumbnail_columns' );

/**
 * Default loop columns on product archives.
 *
 * @return integer products per row.
 */
function dollarclub_woocommerce_loop_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'dollarclub_woocommerce_loop_columns' );

/**
 * Remove Built in WooCommerce Structured Data
 */
add_filter( 'woocommerce_structured_data_product', 'dollarclub_woocommerce_remove_structured_data', 50, 1);
function dollarclub_woocommerce_remove_structured_data($data) {
	return array();
}

/**
 * Prevent title from showing, and run shortcode for the description
 */
add_filter( 'woocommerce_show_page_title', '__return_false' );
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
if ( ! function_exists( 'dollarclub_woocommerce_taxonomy_archive_description' ) ) {
	/**
	 * Product options and rating wrapper.
	 *
	 * @return  void
	 */
	function dollarclub_woocommerce_taxonomy_archive_description() {
		if ( is_product_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
			$term = get_queried_object();

			if ( $term && ! empty( $term->description ) ) {
				echo do_shortcode($term->description); // WPCS: XSS ok.
			}
		}
	}
}
add_action( 'woocommerce_archive_description', 'dollarclub_woocommerce_taxonomy_archive_description', 10 );


/**
 * Move/Remove Breadcrumb, Result count, add filter, add order by name
 */
 remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
 add_action( 'woocommerce_after_main_content', 'woocommerce_breadcrumb', 20 );

 add_filter( 'woocommerce_get_catalog_ordering_args', 'dollar_club_woocommerce_get_catalog_ordering_args' );

if ( ! function_exists( 'dollar_club_woocommerce_get_catalog_ordering_args' ) ) {
    /**
     * Adds orderby name to woocommerce internal ordering
     * @param array $args array of args for the woocommerce query
     *
     * @return array modified args to have orderby as title if it is ordered by name
     */
    function dollar_club_woocommerce_get_catalog_ordering_args( $args ) {
        $orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

        if ( 'title-desc' == $orderby_value ) {
            $args['orderby'] = 'title';
            $args['order'] = 'DESC';
        }
		if ($args['orderby'] == 'relevance') {
			$args['order'] = 'ASC';
		}

        return $args;
    }
}

add_filter( 'woocommerce_default_catalog_orderby_options', 'dollar_club_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'dollar_club_woocommerce_catalog_orderby' );
if ( ! function_exists( 'dollar_club_woocommerce_catalog_orderby' ) ) {
    /**
     * Adds orderby name options to front end
     * @param array $sortby array of options to order by
     *
     * @return array modified array to have orderby name az and za
     */
    function dollar_club_woocommerce_catalog_orderby( $sortby ) {
        $sortby['title'] = __( 'Sort by name: a-z' );
        $sortby['title-desc'] = __( 'Sort by name: z-a' );
        return $sortby;
    }
}

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

add_action('woocommerce_after_shop_loop', 'dollarclub_woocommerce_add_load_more_products', 10);
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);

if ( ! function_exists( 'dollarclub_woocommerce_add_load_more_products' ) ) {
	/**
	* Adds load more products to page.
	*
	* @return  void
	*/
	function dollarclub_woocommerce_add_load_more_products() {
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return;
		}
		global $dollar_club_product_count;
		$count = $dollar_club_product_count ? $dollar_club_product_count : wc_get_loop_prop( 'total' );
		$total_offscreen = $count - 12;
		$load_number = 12;
		if ($total_offscreen > 0 && $total_offscreen < 12) {
			$load_number = $total_offscreen;
		}
		?>
		<div class="dollar-club-load-more">
			<a href="#" id="load-more"><span class="load-more-bold">Load <span class="load-more-count"><?php echo $load_number; ?></span> more products (showing <span class="product-showing-count">12</span> of <span class="product-count"><?php echo $count; ?></span>)...</span></a>
		</div>
		<?php
	}
}

/**
 * Reorder product display order
 * Move image below title and price
 * Move rating to be in the same container as options
 * Remove price
 */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

add_action( 'woocommerce_after_shop_loop_item_title', 'dollar_club_dynamic_title_height', 9);
if ( !function_exists( 'dollar_club_dynamic_title_height' ) ) {
	/**
	 * Add empty div with flex-grow to make title size a bit more dynamically
	 *
	 * @return  void
	 */
	function dollar_club_dynamic_title_height() {
		?>
		<div class="dollar-club-title-grow"></div>
		<?php
	}
}
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 15);
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 17);

if ( ! function_exists( 'dollarclub_woocommerce_product_option_wrapper' ) ) {
	/**
	 * Product options and rating wrapper.
	 *
	 * @return  void
	 */
	function dollarclub_woocommerce_product_option_wrapper() {
		echo '<div class="dollar-club-product-options-rating">';
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'dollarclub_woocommerce_product_option_wrapper', 6);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

function dollarclub_woocommerce_add_to_cart() {

	global $product;

	if ( ( is_page( 'cart' ) || is_cart() ) && !( $product->is_type( 'variable' ) ) && $product->is_in_stock()) {

		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

	} else {

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

	}

}
add_action( 'woocommerce_after_shop_loop_item', 'dollarclub_woocommerce_add_to_cart', 1 );

if ( ! function_exists( 'dollarclub_woocommerce_product_option_wrapper_close' ) ) {
	/**
	 * Product options and rating wrapper.
	 *
	 * @return  void
	 */
	function dollarclub_woocommerce_product_option_wrapper_close() {
		echo '</div>';
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'dollarclub_woocommerce_product_option_wrapper_close', 40);


/**
 * variable price html.
 *
 * @param string $price current price string.
 * @return array $price edited price string.
 */
// function dollarclub_variable_price_html( $price, $product ) {
// 	$prefix = sprintf('%s: ', __('Starting at', 'iconic'));
//
//     $min_price_regular = $product->get_variation_regular_price( 'min', true );
//     $min_price_sale    = $product->get_variation_sale_price( 'min', true );
//     $max_price = $product->get_variation_price( 'max', true );
//     $min_price = $product->get_variation_price( 'min', true );
//
//     $price = ( $min_price_sale == $min_price_regular ) ? wc_price( $min_price_regular ) : '<del>' . wc_price( $min_price_regular ) . '</del>' . '<ins>' . wc_price( $min_price_sale ) . '</ins>';
// 		$price = str_replace('.00', '', $price);
// 		if ($min_price < $max_price) {
// 			$price = $prefix . $price;
// 		}
//     return $price;
//
// }
// add_filter( 'woocommerce_variable_sale_price_html', 'dollarclub_variable_price_html', 10, 2 );
// add_filter( 'woocommerce_variable_price_html', 'dollarclub_variable_price_html', 10, 2 );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function dollarclub_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 3,
		'columns'        => 3,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'dollarclub_woocommerce_related_products_args' );

if ( ! function_exists( 'dollarclub_woocommerce_product_columns_wrapper' ) ) {
	/**
	 * Product columns wrapper.
	 *
	 * @return  void
	 */
	function dollarclub_woocommerce_product_columns_wrapper() {
		$columns = dollarclub_woocommerce_loop_columns();
		echo '<div class="columns-' . absint( $columns ) . '">';
	}
}
add_action( 'woocommerce_before_shop_loop', 'dollarclub_woocommerce_product_columns_wrapper', 40 );

if ( ! function_exists( 'dollarclub_woocommerce_product_columns_wrapper_close' ) ) {
	/**
	 * Product columns wrapper close.
	 *
	 * @return  void
	 */
	function dollarclub_woocommerce_product_columns_wrapper_close() {
		echo '</div>';
	}
}
add_action( 'woocommerce_after_shop_loop', 'dollarclub_woocommerce_product_columns_wrapper_close', 40 );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'dollarclub_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function dollarclub_woocommerce_wrapper_before() {
		?>
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
			<?php
	}
}
add_action( 'woocommerce_before_main_content', 'dollarclub_woocommerce_wrapper_before' );

if ( ! function_exists( 'dollarclub_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function dollarclub_woocommerce_wrapper_after() {
			?>
			</main><!-- #main -->
		</div><!-- #primary -->
		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'dollarclub_woocommerce_wrapper_after' );

// Move title
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 4 );

// Move Price
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_price', 6 );


if ( ! function_exists( 'dollar_club_woocommerce_remove_single_product_image_link' ) ) {
	/**
	 * Make images on single product page non click able (prevent user for viewing the image page)
	 *
	 * @return string modified html with no links
	 */
	function dollar_club_woocommerce_remove_single_product_image_link( $html, $post_thumbnail_id ) {
        $html = preg_replace('<a href=\".*?\">', '', $html);
        $html = str_replace('<>', '', $html);
        $html = str_replace('</a>', '', $html);
		return $html;
	}
}

// Change 'Clear' to 'Reset' for the reset variation button
add_filter( 'woocommerce_reset_variations_link', 'dollarclub_woocommerce_reset_variation_text');

if ( ! function_exists( 'dollarclub_woocommerce_reset_variation_text' ) ) {
	/**
	 * Change 'Clear' to 'Reset' for the reset variation button
	 *
	 * @return string modified reset button
	 */
	function dollarclub_woocommerce_reset_variation_text( $reset ) {
		return str_replace('Clear', 'Reset', $reset);
	}
}

// Reorganize Product Summary
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 25 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 5 );

function dollar_club_add_descriptions_to_product_options($args) {
	$product = $args['product'];
	$attribute = $args['attribute'];
	$data = array();
	if ( $product && taxonomy_exists( $attribute ) ) {
		// Get terms if this is a taxonomy - ordered. We need the names too.
		$terms = wc_get_product_terms( $product->get_id(), $attribute, array('fields' => 'all') );
		foreach ( $terms as $term ) {
			if ($term->description) {
				$data[$term->slug] = $term->description;
			}
		}
		if (!empty($data)) {
			echo "<div class=\"attribute-description-info\" data-arrtibute_name=\"" . $attribute . "\" data-attribute_descriptions=\"" . esc_html(json_encode($data)) . "\"></div>";
		}
	}


	$args['class'] = $args['attribute'];
	return $args;
}
add_filter( 'woocommerce_radio_variation_attribute_options_args', 'dollar_club_add_descriptions_to_product_options' );

function dollarclub_woocommerce_single_product_summary_start() {

	?><div class="dollarclub-inner-summary"><?php
}
add_action( 'woocommerce_single_product_summary', 'dollarclub_woocommerce_single_product_summary_start', 10 );

function dollarclub_woocommerce_single_product_summary_end() {

	?></div><?php

}
add_action( 'woocommerce_single_product_summary', 'dollarclub_woocommerce_single_product_summary_end', 55 );

function dollarclub_single_product_carousel_options( $options ) {
	$options['directionNav'] = true;
  $options['prevText'] = '<i><svg viewBox="0 0 512 512"><path d="M368 505.6c-8 0-16-3.2-22.4-8l-240-225.6c-6.4-6.4-9.6-14.4-9.6-24 0-8 3.2-16 9.6-22.4l240-224c12.8-11.2 33.6-11.2 44.8 1.6 12.8 12.8 11.2 32-1.6 44.8l-214.4 201.6 216 203.2c12.8 11.2 12.8 32 0 44.8-4.8 4.8-14.4 8-22.4 8z"/></svg></i>';
  $options['nextText'] = '<i><svg viewBox="0 0 512 512"><path d="M144 505.6c8 0 16-3.2 22.4-8l240-225.6c6.4-6.4 9.6-14.4 9.6-22.4s-3.2-16-9.6-22.4l-240-224c-12.8-12.8-32-12.8-44.8 0s-11.2 32 1.6 44.8l214.4 201.6-216 203.2c-12.8 11.2-12.8 32 0 44.8 6.4 4.8 14.4 8 22.4 8z"/></svg></i>';
	$options['controlNav'] = 'thumbnails';
  return $options;
}
add_filter( 'woocommerce_single_product_carousel_options', 'dollarclub_single_product_carousel_options' );

function dollarclub_gallery_image_size() { return 'full'; }
add_filter( 'woocommerce_gallery_image_size', 'dollarclub_gallery_image_size' );

function dollarclub_gallery_thumbnail_size( $size ) { return 'thumbnail'; }
add_filter( 'woocommerce_gallery_thumbnail_size', 'dollarclub_gallery_thumbnail_size', 10, 1);

add_filter( 'woocommerce_product_description_heading', '__return_null' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

function dollarclub_product_tabs( $tabs ) {
	unset( $tabs['additional_information'] );

  return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'dollarclub_product_tabs', 20 );

//Move reviews to the bottom of the page
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 25);

// Remove upsells on PDP, Added back in inside of woocommerce-dollar-club-customizations
/*
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );


function dollarclub_wc_product_addon_start( $addon ) {

	?>
	<div class="dollarclub-addon-type-wrapper dollarclub-addon-type-<?php echo sanitize_title( $addon['display'] ); ?>">
	<?php

}
add_action( 'wc_product_addon_start', 'dollarclub_wc_product_addon_start' );

function dollarclub_wc_product_addon_end() {

	?>
	</div>
	<?php

}
add_action( 'wc_product_addon_end', 'dollarclub_wc_product_addon_end' );
// End Product Add-Ons Stuff
*/

// Cart page Customizations

// Remove item added to cart message
add_filter( 'wc_add_to_cart_message_html', '__return_false' );
// Remove cross-sells at cart
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

function dollarclub_woocommerce_cart_collaterals() {
	?>
	<form class="woocommerce-coupon-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <?php if ( wc_coupons_enabled() ) { ?>
      <div class="coupon-drop-down">
        <span>Do you have a Promo Code?</span>
      </div>
      <div class="coupon under-proceed active">
        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" style="width: 100%" />
        <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>" style="width: 100%"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
      </div>
    <?php } ?>
  </form>
	<?php
}
add_action( 'woocommerce_before_cart_totals', 'dollarclub_woocommerce_cart_collaterals' );

function disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99 );

add_filter( 'angelleye_proceed_to_checkout_button_separator', '__return_null' );

function dollarclub_woocommerce_cart_actions() {
	woocommerce_button_proceed_to_checkout();
}
add_action( 'woocommerce_cart_actions', 'dollarclub_woocommerce_cart_actions' );

// Remove continue shopping button from notification
function dollarclub_wc_add_to_cart_message_html( $html ) {

	$replaced = preg_replace( '/<a.*?<\/a>(.*)/i', '$1', $html );
	$html = trim( $replaced );
	return $html;

}
add_filter( 'wc_add_to_cart_message_html', 'dollarclub_wc_add_to_cart_message_html' );

// Checkout page Customizations
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

function dollarclub_woocommerce_billing_fields( $fields, $country ) {

	unset( $fields['billing_company'] );
	unset( $fields['billing_address_2']['label_class'] );
	return $fields;

}
add_filter( 'woocommerce_billing_fields', 'dollarclub_woocommerce_billing_fields', 10, 2 );

function dollarclub_woocommerce_before_checkout_form_13() {

?>
<h3 class="express-checkout-header">Express Checkout</h3>
<?php

}
add_action( 'woocommerce_before_checkout_form', 'dollarclub_woocommerce_before_checkout_form_13', 13 );
function dollarclub_woocommerce_before_checkout_form_16() {

?>
<div class="express-checkout-divider"> Or </div>
<?php

}
add_action( 'woocommerce_before_checkout_form', 'dollarclub_woocommerce_before_checkout_form_16', 16 );

function dollarclub_woocommerce_credit_card_form_start() {
	?> <div class="dollar-club-cc-field-wrapper"> <?php
}
add_action( 'woocommerce_credit_card_form_start', 'dollarclub_woocommerce_credit_card_form_start' );

function dollarclub_woocommerce_credit_card_form_end() {
	?> </div> <?php
}
add_action( 'woocommerce_credit_card_form_end', 'dollarclub_woocommerce_credit_card_form_end' );

/**
 * Add "Print receipt" link to Order Received page and View Order page
 */
if ( !function_exists( 'dollar_club_woocomerce_print_button' ) ) {

	function dollar_club_woocomerce_print_button() {

	    echo '<a href="javascript:window.print()" id="wc-print-button" class="vc_general vc_btn3 vc_btn3-size-lg vc_btn3-shape-rounded vc_btn3-style-custom">PRINT</a>';

	}

}
add_action( 'woocommerce_thankyou', 'dollar_club_woocomerce_print_button', 1);
add_action( 'woocommerce_view_order', 'dollar_club_woocomerce_print_button', 8 );

if ( ! function_exists( 'dollarclub_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function dollarclub_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		dollarclub_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'dollarclub_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'dollarclub_woocommerce_cart_link' ) ) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function dollarclub_woocommerce_cart_link() {
		?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'dollarclub' ); ?>">
			<?php
			$item_count_text = sprintf(
				/* translators: number of items in the mini cart. */
				_n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'dollarclub' ),
				WC()->cart->get_cart_contents_count()
			);
			?>
			<span class="amount"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span> <span class="count"><?php echo esc_html( $item_count_text ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'dollarclub_woocommerce_header_cart' ) ) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function dollarclub_woocommerce_header_cart() {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
		<ul id="site-header-cart" class="site-header-cart">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php dollarclub_woocommerce_cart_link(); ?>
			</li>
			<li>
				<?php
				$instance = array(
					'title' => '',
				);

				the_widget( 'WC_Widget_Cart', $instance );
				?>
			</li>
		</ul>
		<?php
	}
}

// Shopping Cart
add_filter( 'woocommerce_widget_cart_is_hidden', '__return_true' );

// PDP PLP Prices
add_filter( 'woocommerce_variable_sale_price_html', 'businessbloomer_remove_prices', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'businessbloomer_remove_prices', 10, 2 );
add_filter( 'woocommerce_get_price_html', 'businessbloomer_remove_prices', 10, 2 );

function businessbloomer_remove_prices( $price, $product ) {
	if ( ! is_admin() ) $price = '';
	return $price;
}
