<?php
/*
Copyright (C)  2020, Dollar Club
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'DollarClubReferralPlugin' ) ):

class DollarClubReferralPlugin {

  private static $instance;

  public static function instance() {

    if ( !isset( self::$instance ) || !( self:: $instance instanceof DollarClubReferralPlugin ) ) {

      self::$instance = new DollarClubReferralPlugin();

    }
    return self::$instance;

  }

  public function __construct() {

    add_action('woocommerce_account_content', __CLASS__ . '::affiliate_url', 9);
    add_action('wp_loaded', __CLASS__ . '::dollar_club_referral_cookie');
    add_action('woocommerce_thankyou', __CLASS__ . '::dollar_club_remove_cookie', 10, 1);
    add_action('woocommerce_checkout_create_order', __CLASS__ . '::dollar_club_store_meta', 10, 2);

  }

  // My Account Affiliate Link
  public static function affiliate_url() {
    global $current_user;
    $link =  $_SERVER['HTTP_HOST'] . '/?ref=' . $current_user->ID;
    echo '<div class="referral-section"><h3>Your Referral Link is: </h3><a id="dc-referral-link">'. $link .'</a><button id="dc-referral-copy-button">Copy</button></div>';
  }
  // End

  public static function dollar_club_store_meta( $order, $data ) {

    if ( isset( $_COOKIE['ref'] ) )$order->update_meta_data( '_dollar_club_referral_meta', $_COOKIE['ref'] );

  }

  public static function dollar_club_referral_cookie() {

    if ( isset($_GET['ref'])) {
      setcookie('ref', $_GET['ref']);
    }
  }

  public static function dollar_club_remove_cookie() {
    if ( isset( $_COOKIE['ref'] ) ) {
        unset( $_COOKIE['ref'] );
        setcookie( 'ref', null, -1, '/' );
        return true;
    } else {
        return false;
    }
  }

}

/**
 * The main function responsible for returning The DollarClubReferralPlugin
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = DollarClubReferralPlugin(); ?>
 *
 * @since 1.0
 * @return object The DollarClubReferralPlugin Instance
 */
function DollarClubReferralPlugin() {

    return DollarClubReferralPlugin::instance();

}

DollarClubReferralPlugin();

endif;
