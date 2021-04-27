<?php
/*
Copyright (C)  2020, Dollar Club
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'DollarClubDonationMeter' ) ):

class DollarClubDonationMeter {

  private static $instance;

  public static function instance() {

    if ( !isset( self::$instance ) || !( self:: $instance instanceof DollarClubDonationMeter ) ) {

      self::$instance = new DollarClubDonationMeter();

    }
    return self::$instance;

  }

  public function __construct() {

    add_filter( 'woocommerce_before_shop_loop_item_title', __CLASS__ . '::meter', 10, 2 );
    add_filter( 'woocommerce_share', __CLASS__ . '::meter', 10, 2 );
    add_shortcode( 'my_purchased_products', __CLASS__ . '::create_order_shortcode', 10, 2 );
    add_filter( 'woocommerce_account_dashboard', __CLASS__ . '::add_to_page', 10, 2 );
    add_action( 'wp_enqueue_scripts', __CLASS__ . '::wp_enqueue_scripts' );

  }

  private static function meter_func() {
    global $woocommerce, $product;

    $manage_stock = $product->get_manage_stock();

    if ( $manage_stock ) {

      $rounded_top_right_corner      = 0;
      $rounded_bottom_right_corner   = 0;
      $percent_symbol                = '%';
      $user_percent_symbol           = '%';
      $user_order_qty                = self::get_product_sold_count( $product->get_id() );
      $total_sales                   = $product->get_total_sales();
      $in_stock                      = $product->get_stock_quantity();
      $total_original_stock          = $total_sales + $in_stock;
      $user_percentage               = ( $user_order_qty/$total_original_stock ) * 100;
      $fraction                      = ( $total_original_stock - $in_stock ) / $total_original_stock;
      $percentage                    = $fraction * 100;
      $rounded_percentage            = round($percentage, 2);
      $rounded_percentage_width      = $rounded_percentage;
      $rounded_user_percentage       = round($user_percentage, 2);
      $rounded_user_percentage_width = $rounded_user_percentage;
      $referral_order_arr            = self::get_referral_array();
      $goal                          = 'Goal: ';
      $your                          = 'Your';
      $server_uri                    = $_SERVER['REQUEST_URI'];

      if ( strpos( $server_uri, 'user') ) {
        $your = '';
      }

      if (get_current_user_id() == self::get_user_id_by_login()) {
        $your = 'Your';
      }

      if ( $referral_order_arr ) {
        foreach ($referral_order_arr as $single_referral_order ) {
          if ( $product->get_id() == $single_referral_order['id'] ) {
            $qty                           = $single_referral_order['qty'];
            $ref_qty_percentage            = ( $qty/$total_original_stock ) * 100;
            $rounded_user_percentage_width = $rounded_user_percentage_width + $ref_qty_percentage;
            $rounded_user_percentage       = $rounded_user_percentage + $ref_qty_percentage;
            $rounded_user_percentage       = round($rounded_user_percentage, 2);
          }
        }
      }

      if ($percentage >= 100) {
        $rounded_top_right_corner = '13';
      } else if ($percentage >= 99) {
        $rounded_top_right_corner = '9';
      } else if ($percentage >= 97) {
        $rounded_top_right_corner = '6';
      } else if ($percentage >= 96) {
        $rounded_top_right_corner = '3';
      }

      if ($rounded_percentage >= 100) {
        $goal = 'Funded: ';
      }

      if ( $rounded_percentage_width <= 10 ) {
        $rounded_percentage_width = 10;
      }

      if ( $rounded_user_percentage_width <= 10 ) {
        $rounded_user_percentage_width = 10;
      }

      if ( $rounded_percentage_width >= 100 ) {
        $rounded_user_percentage_width = 100;
      }

      if ($rounded_user_percentage_width >= 100) {
        $rounded_bottom_right_corner = '13';
      } else if ($rounded_user_percentage_width >= 99) {
        $rounded_bottom_right_corner = '9';
      } else if ($rounded_user_percentage_width >= 98) {
        $rounded_bottom_right_corner = '6';
      } else if ($rounded_user_percentage_width >= 97) {
        $rounded_bottom_right_corner = '3';
      }

      if ( is_user_logged_in() && $rounded_user_percentage != 0 ) {

        echo "
        <div class='donation-percentage-outside rounded-border' style='margin-bottom: 0;'>
          <div class='donation-percentage-inside rounded-border-top' style='width: " . $rounded_percentage_width . "%; margin-bottom: 0; border-top-right-radius: " . $rounded_top_right_corner . "px;max-width:100%;'>
            <p style='padding-left:10px; margin-bottom: 0;'>" . $goal . ' ' . $rounded_percentage . $percent_symbol . "</p>
          </div>
          <div class='donation-percentage-user rounded-border-bottom' style='width: " . $rounded_user_percentage_width . "%; margin-bottom: 0; border-bottom-right-radius: " . $rounded_bottom_right_corner . "px;max-width:100%;'>
            <p style='padding-left:10px; margin-bottom: 0;'>". $your ." Influence: " . $rounded_user_percentage . $user_percent_symbol . "</p>
          </div>
        </div>";

      } else {

        echo "
        <div class='donation-percentage-outside rounded-border'>
          <div class='donation-percentage-inside rounded-border' style='width: " . $rounded_percentage_width . "%;margin-bottom:0;max-width:100%;'>
            <p style='padding-left:10px;margin-bottom:0;'>" . $goal . $rounded_percentage . $percent_symbol . "</p>
          </div>
        </div>";

      }

    }

  }

  private static function get_customers_order_info() {

    $current_user = self::get_user_id_by_login();
    if ( 0 == $current_user ) { return false; }

    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $current_user,
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_is_paid_statuses() ),
    ) );

    if ( ! $customer_orders ) { return false; }

    return $customer_orders;

  }

  private static function get_referral_array() {
    global $wpdb;
    $current_user = self::get_user_id_by_login();

    $get_referral_order = $wpdb->get_results("SELECT post_id FROM wp_postmeta WHERE meta_key = '_dollar_club_referral_meta' AND meta_value = $current_user");

    $order_item_data      = array();
    $referral_data_arr    = array();
    $referral_product_ids = array();
    $referral_product_qty = array();

    foreach( $get_referral_order as $result ) {
      $single_referral_order = var_export( $result->post_id, true );
      $single_referral_order = trim($single_referral_order,"'");
      $order                 = wc_get_order($single_referral_order);
      $items                 = $order->get_items();
      foreach ( $items as $item_id => $items_value ) {
        $item_data = $items_value->get_data();
        $referral_product_ids[] = $item_data['product_id'];
        $referral_product_qty[] = $item_data['quantity'];

        $order_item_data[]      = $item_data;
      }

    }
    for($i = 0; $i < count($referral_product_ids); $i++ ) {
      $referral_data_arr[] = array('id'=>$referral_product_ids[$i], 'qty'=>$referral_product_qty[$i]);
    }
    return $referral_data_arr;
  }

  private static function get_customers_product_ids() {
    if ( self::get_customers_order_info() === false ) { return; }
    $customer_orders = self::get_customers_order_info();
    $product_ids     = array();

    foreach ( $customer_orders as $customer_order ) {
      $order = wc_get_order( $customer_order->ID );
      $items = $order->get_items();

      foreach ( $items as $item ) {
        $product_id    = $item->get_product_id();
        $product_ids[] = $product_id;
      }
    }
    $product_ids = array_unique( $product_ids );

    return $product_ids;
  }

  private static function get_product_sold_count( $product_id ) {
    global $wpdb;

    if ( !is_user_logged_in() ) return;
    $user_id = self::get_user_id_by_login();

    $units_bought = $wpdb->get_var( "
        SELECT SUM(woim2.meta_value)
        FROM {$wpdb->prefix}woocommerce_order_items AS woi
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim ON woi.order_item_id = woim.order_item_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim2 ON woi.order_item_id = woim2.order_item_id
        INNER JOIN {$wpdb->prefix}postmeta pm ON woi.order_id = pm.post_id
        INNER JOIN {$wpdb->prefix}posts AS p ON woi.order_id = p.ID
        WHERE woi.order_item_type LIKE 'line_item'
        AND p.post_type LIKE 'shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND pm.meta_key = '_customer_user'
        AND pm.meta_value = '$user_id'
        AND woim.meta_key = '_product_id'
        AND woim.meta_value = '$product_id'
        AND woim2.meta_key = '_qty'
    ");

    return $units_bought;

  }

  private static function get_user_id_by_login() {
    $server_uri = $_SERVER['REQUEST_URI'];
    if ( strpos( $server_uri, 'user') ) {
      $server_uri = str_replace('/user/', "", $server_uri);
      $username = substr($server_uri, 0, strrpos( $server_uri, '/'));
      $user_info = get_user_by('login', $username );
      $current_user = $user_info->ID;
    } else {
      $current_user = get_current_user_id();
    }

    return $current_user;
  }

  public static function create_order_shortcode() {
    if ( self::get_customers_product_ids() === false ) { return; }
    $product_ids  = self::get_customers_product_ids();
    $referral_ids = array();

    if ( self::get_referral_array() != null ) {
      $referral_array = self::get_referral_array();
      foreach ( $referral_array as $referral ) {
        $referral_ids[] = $referral['id'];
      }
    }

    if ($product_ids != null) {
      $combined_ids = array_unique(array_merge($product_ids, $referral_ids));

      $combined_ids = implode( ",", $combined_ids );

      return do_shortcode("[products ids='$combined_ids']");
    }

    return;

  }

  public static function add_to_page() {

    echo "<div class='my-account-plp'>" . self::create_order_shortcode() . "</div>";

  }

  public static function meter( ) {

    self::meter_func();

  }

  public static function wp_enqueue_scripts() {

    wp_register_style( 'contributions-styles', plugin_dir_url( __DIR__ . '..' ) . 'assets/css/meter-frontend.css' );
    wp_enqueue_style( 'contributions-styles' );

  }

}

/**
 * The main function responsible for returning The DollarClubDonationMeter
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = DollarClubDonationMeter(); ?>
 *
 * @since 1.0
 * @return object The DollarClubDonationMeter Instance
 */
function DollarClubDonationMeter() {

    return DollarClubDonationMeter::instance();

}

DollarClubDonationMeter();

endif;
