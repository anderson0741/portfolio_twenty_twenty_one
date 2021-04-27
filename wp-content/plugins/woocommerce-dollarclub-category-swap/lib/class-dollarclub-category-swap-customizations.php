<?php
/*
Copyright (C)  2020, Dollar Club
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'DollarClubCategorySwap' ) ):

class DollarClubCategorySwap {

  private static $instance;

  public static function instance() {

    if ( !isset( self::$instance ) || !( self:: $instance instanceof DollarClubCategorySwap ) ) {

      self::$instance = new DollarClubCategorySwap();

    }
    return self::$instance;

  }

  public function __construct() {

    add_action( 'woocommerce_no_stock', __CLASS__ . '::update_category_on_complete', 10, 1 );
    add_action( 'woocommerce_product_set_stock', __CLASS__ . '::update_category_on_complete', 10, 1 );


  }

  public static function update_category_on_complete( $product ) {
    $total_stock = $product->get_stock_quantity();
    $category_ids = $product->get_category_ids();

    if ( $total_stock <= 0 && !( count( $category_ids ) == 1 && $category_ids[0] == 23 ) ) {
      $product->set_category_ids( array( 23 ) );
      $product->save();
    }

  }

}

/**
 * The main function responsible for returning The DollarClubCategorySwap
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = DollarClubCategorySwap(); ?>
 *
 * @since 1.0
 * @return object The DollarClubCategorySwap Instance
 */
function DollarClubCategorySwap() {

    return DollarClubCategorySwap::instance();

}

DollarClubCategorySwap();

endif;
