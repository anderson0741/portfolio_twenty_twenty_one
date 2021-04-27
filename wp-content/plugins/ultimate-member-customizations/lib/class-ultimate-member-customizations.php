<?php
/*
Copyright (C)  2020, Tractus, Inc
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'UMCustomizations' ) ):

class UMCustomizations {

  private static $instance;

  public static function instance() {

    if ( !isset( self::$instance ) || !( self:: $instance instanceof UMCustomizations ) ) {

      self::$instance = new UMCustomizations();

    }
    return self::$instance;

  }

  public function __construct() {

    add_filter( 'um_profile_tabs', __CLASS__ . '::um_mycustomtabs_add_tab', 1000 );

    add_action( 'um_profile_content_mydonationstab_default', __CLASS__ . '::um_profile_content_mydonationstab_default' );

    add_action( 'um_profile_content_myreferraltab_default', __CLASS__ . '::um_profile_content_myreferraltab_default' );

    add_filter('um_restrict_post_thumbnail','__return_true');
  }

  /**
   * Add a new Profile tab
   *
   * @param array $tabs
   * @return array
   */
  public static function um_mycustomtabs_add_tab( $tabs ) {

  	$tabs[ 'mydonationstab' ] = array(
  		'name'   => 'Donations',
  		'icon'   => 'um-icon-heart',
  		'custom' => true
  	);
    $tabs[ 'myreferraltab' ] = array(
      'name'   => 'Referral Link',
      'icon'   => 'um-icon-android-share',
      'custom' => true
    );

  	UM()->options()->options[ 'profile_tab_' . 'mydonationstab' ] = true;
    UM()->options()->options[ 'profile_tab_' . 'myreferraltab' ] = true;

  	return $tabs;
  }

  public static function um_myreferraltab_add_tab( $tabs ) {





  	return $tabs;
  }

  /**
   * Render tab content
   *
   * @param array $args
   */
  public static function um_profile_content_mydonationstab_default( $args ) {

    DollarClubDonationMeter::add_to_page();

  }

  /**
   * Render tab content
   *
   * @param array $args
   */
  public static function um_profile_content_myreferraltab_default( $args ) {


DollarClubReferralPlugin::affiliate_url();
  }


}


/**
 * The main function responsible for returning The UMCustomizations
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = UMCustomizations(); ?>
 *
 * @since 1.0
 * @return object The UMCustomizations Instance
 */
function UMCustomizations() {

    return UMCustomizations::instance();

}

UMCustomizations();

endif;
