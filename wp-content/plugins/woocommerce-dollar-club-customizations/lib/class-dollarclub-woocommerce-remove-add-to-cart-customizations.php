<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations' ) ):

class Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations {

    private static $instance;

    public static function instance() {

        if ( !isset( self::$instance ) || !( self:: $instance instanceof Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations ) ) {

            self::$instance = new Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations();

        }
        return self::$instance;

    }

    public function __construct() {

      add_action('woocommerce_is_purchasable', __CLASS__ . '::disable_purchasable_on_product_category_archives', 10, 2);

    }

    public static function disable_purchasable_on_product_category_archives( $purchasable, $product ) {
      $category = $product->get_category_ids();

      foreach ($category as $cat_id) {

        if( $cat_id == 23 ) {

          $purchasable = false;
          break;

        }

      }

      return $purchasable;

    }

}

/**
 * The main function responsible for returning The Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations(); ?>
 *
 * @since 1.0
 * @return object The Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations Instance
 */
function Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations() {

    return Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations::instance();

}

Dollar_Club_WooCommerce_Remove_Add_To_Cart_Customizations();

endif;
