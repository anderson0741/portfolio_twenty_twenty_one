<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Dollar_Club_WooCommerce_Title_Image_Customizations' ) ):

class Dollar_Club_WooCommerce_Title_Image_Customizations {

    private static $instance;

    public static function instance() {

        if ( !isset( self::$instance ) || !( self:: $instance instanceof Dollar_Club_WooCommerce_Title_Image_Customizations ) ) {

            self::$instance = new Dollar_Club_WooCommerce_Title_Image_Customizations();

        }
        return self::$instance;

    }

    public function __construct() {

        // add_action( 'admin_enqueue_scripts', __CLASS__ . '::dollarclub_admin_enqueue_scripts' );

        add_action( 'woocommerce_shop_loop_item_title', __CLASS__ . '::dollarclub_woocommerce_title_wrapper', 9);

        add_action( 'woocommerce_shop_loop_item_title', __CLASS__ . '::dollarclub_woocommerce_title_image', 11);

        add_action( 'woocommerce_shop_loop_item_title', __CLASS__ . '::dollarclub_woocommerce_title_closing_wrapper', 12);

        add_action( 'woocommerce_before_single_product_summary', __CLASS__ . '::dollarclub_woocommerce_title_image', 5);

        add_action( 'woocommerce_process_product_meta', __CLASS__ . '::dollarclub_save_product_title_image' );

        add_action( 'woocommerce_before_single_product_summary', __CLASS__ . '::dollarclub_woocommerce_title_wrapper', 3);

        add_action( 'woocommerce_before_single_product_summary', __CLASS__ . '::dollarclub_woocommerce_title_closing_wrapper', 5);

        add_filter( 'woocommerce_rest_prepare_product_object', __CLASS__ . '::dollarclub_add_product_title_image_to_api_response', 11, 3 );


    }

    public static function dollarclub_woocommerce_title_wrapper() {
    	echo '<div class="product-title">';
    }
    public static function dollarclub_woocommerce_title_closing_wrapper() {
    	echo '</div>';
    }


    public static function dollarclub_admin_enqueue_scripts() {
        wp_enqueue_script( 'product-title-image-admin-media', plugin_dir_url( dirname( __FILE__ ) ) . '/assets/js/product-title-image-admin-media.js' );
    }

    public static function dollarclub_woocommerce_title_image() {
    	global $product;
    	if ( $product->meta_exists( 'dollarclub_product_title_image_attachment_id' ) ) {
    		$image = wp_get_attachment_url( get_post_meta( $product->get_id(), 'dollarclub_product_title_image_attachment_id', true ) );
    		if ( $image ) {

    			echo '<img class="dollarclub-title-image" src="' . $image . '"/>';

    		}

    	}
    }

    public static function dollarclub_save_product_title_image( $post_id ) {

        $title_image_id = isset( $_POST['dollarclub_product_title_image_attachment_id'] ) ? $_POST['dollarclub_product_title_image_attachment_id'] : '';
        update_post_meta( $post_id, 'dollarclub_product_title_image_attachment_id', $title_image_id );

    }

    public static function dollarclub_add_product_title_image_to_api_response($response, $post, $request) {
    	global $product;
    	$product = $post;
    	ob_start();
    	self::dollarclub_woocommerce_title_image();
    	$response->data['title_image'] = ob_get_clean();

  	    return $response;
    }

}

/**
 * The main function responsible for returning The Dollar_Club_WooCommerce_Title_Image_Customizations
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = Dollar_Club_WooCommerce_Title_Image_Customizations(); ?>
 *
 * @since 1.0
 * @return object The Dollar_Club_WooCommerce_Title_Image_Customizations Instance
 */
function Dollar_Club_WooCommerce_Title_Image_Customizations() {

    return Dollar_Club_WooCommerce_Title_Image_Customizations::instance();

}

Dollar_Club_WooCommerce_Title_Image_Customizations();

endif;
