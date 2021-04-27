<?php
/*
Copyright (C)  2020, Tractus, Inc
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'UMGroupCustomizations' ) ):

class UMGroupCustomizations {

  private static $instance;

  public static function instance() {

    if ( !isset( self::$instance ) || !( self:: $instance instanceof UMGroupCustomizations ) ) {

      self::$instance = new UMGroupCustomizations();

    }
    return self::$instance;

  }

  public function __construct() {

    add_action( 'woocommerce_product_options_pricing', __CLASS__ . '::add_group_link' );
    add_action( 'woocommerce_process_product_meta', __CLASS__ . '::save_custom_group_id' );
    add_action( 'woocommerce_thankyou', __CLASS__ . '::um_group_add_user', 10, 1 );

  }

  public static function add_group_link() {
    $args = array(
      'label' => __( 'Linked Group', 'woocommerce' ),
      'placeholder' => __( 'Enter Group ID', 'woocommerce' ),
      'id' => 'group_link_id',
      'desc_tip' => true,
      'description' => __( 'This is the Group ID you want linked to this product.', 'woocommerce' ),
    );

    woocommerce_wp_text_input( $args );

  }

  public static function save_custom_group_id( $post_id ) {

    $custom_group_id = isset( $_POST[ 'group_link_id' ] ) ? sanitize_text_field( $_POST[ 'group_link_id' ] ) : '';

    $product = wc_get_product( $post_id );

    $product->update_meta_data( 'group_link_id', $custom_group_id );

    $product->save();

  }

  public static function um_group_add_user( $order ) {
    global $wpdb;

    $order_info = wc_get_order( $order );
    $items      = $order_info->get_items();
    $billing_email = $order_info->get_billing_email();

    foreach( $items as $item ) {
      $item_data = $item->get_data();
      $user_id = get_current_user_id();
      if ($user_id == 0) return;
      $group_id = get_post_meta( $item_data['product_id'], 'group_link_id', true );
      UM()->Groups()->api()->join_group( $user_id, $user_id, $group_id );
      $table_name = UM()->Groups()->setup()->db_groups_table;

      $success = $wpdb->update( $table_name, array( 'status' => 'approved' ), array( 'user_id1' => $user_id, 'group_id' => $group_id ) );

      if ( $success === false ) {
        $to      = 'colton@dollarclub.net';
        $subject = 'Failed Adding Client to Group';
        $message = 'Error adding user ' . $billing_email . ' to group ' . $group_id;

        wp_mail($to, $subject, $message);
      }
    }
  }

}


/**
 * The main function responsible for returning The UMGroupCustomizations
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = UMGroupCustomizations(); ?>
 *
 * @since 1.0
 * @return object The UMGroupCustomizations Instance
 */
function UMGroupCustomizations() {

    return UMGroupCustomizations::instance();

}

UMGroupCustomizations();

endif;
