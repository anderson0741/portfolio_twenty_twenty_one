<?php
/*
Copyright (C)  2020, Tractus, Inc
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'UMRemoveAssets' ) ):

class UMRemoveAssets {

  private static $instance;

  public static function instance() {

    if ( !isset( self::$instance ) || !( self:: $instance instanceof UMRemoveAssets ) ) {

      self::$instance = new UMRemoveAssets();

    }
    return self::$instance;

  }

  public function __construct() {

    add_action( 'wp_print_footer_scripts', __CLASS__ . '::um_remove_scripts_and_styles', 9 );
    add_action( 'wp_print_scripts', __CLASS__ . '::um_remove_scripts_and_styles', 9 );
    add_action( 'wp_print_styles', __CLASS__ . '::um_remove_scripts_and_styles', 9 );
    add_action( 'dynamic_sidebar', __CLASS__ . '::um_remove_scripts_and_styles_widget' );

  }

  /**
   * Maybe remove Ultimate Member CSS and JS
   * @global WP_Post $post
   * @global bool $um_load_assets
   * @global WP_Scripts $wp_scripts
   * @global WP_Styles $wp_styles
   * @return NULL
   */
  public static function um_remove_scripts_and_styles() {
  	global $post, $um_load_assets, $wp_scripts, $wp_styles;

  	// Set here IDs of the pages, that use Ultimate Member scripts and styles
  	$um_posts = array('174','175','176','177','178','179','180','184','185','186','187');

  	// Set here URLs of the pages, that use Ultimate Member scripts and styles
  	$um_urls = array(
  		'/account/',
  		// '/activity/',
  		'/groups/',
  		'/login/',
  		'/logout/',
  		'/members/',
  		'/my-groups/',
      '/create-new-group/',
  		'/password-reset/',
  		'/register/',
  		'/user/',
      '/my-invites/',
  	);

  	if ( is_ultimatemember() ) {
  		return;
  	}

  	$REQUEST_URI = $_SERVER['REQUEST_URI'];
  	if ( in_array( $REQUEST_URI, $um_urls ) ) {
  		return;
  	}
  	foreach ( $um_urls as $key => $um_url ) {
  		if ( strpos( $REQUEST_URI, $um_url ) !== FALSE ) {
  			return;
  		}
  	}

  	if ( !empty( $um_load_assets ) ) {
  		return;
  	}

  	if ( isset( $post ) && is_a( $post, 'WP_Post' ) ) {
  		if ( in_array( $post->ID, $um_posts ) ) {
  			return;
  		}
  		if ( strpos( $post->post_content, '[ultimatemember_' ) !== FALSE ) {
  			return;
  		}
  		if ( strpos( $post->post_content, '[ultimatemember form_id' ) !== FALSE ) {
  			return;
  		}
  	}

  	if ( empty( $wp_scripts->queue ) || empty( $wp_styles->queue ) ) {
  		return;
  	}

  	foreach ( $wp_scripts->queue as $key => $script ) {
  		if ( strpos( $script, 'um_' ) === 0 || strpos( $script, 'um-' ) === 0 || strpos( $wp_scripts->registered[$script]->src, '/ultimate-member/assets/' ) !== FALSE ) {
  			unset( $wp_scripts->queue[$key] );
  		}
  	}

  	foreach ( $wp_styles->queue as $key => $style ) {
  		if ( strpos( $style, 'um_' ) === 0 || strpos( $style, 'um-' ) === 0 || strpos( $wp_styles->registered[$style]->src, '/ultimate-member/assets/' ) !== FALSE ) {
  			unset( $wp_styles->queue[$key] );
  		}
  	}
  }

  /**
   * Check whether Ultimate Member widget was used
   * @param array $widget
   */
  public static function um_remove_scripts_and_styles_widget( $widget ) {
  	if ( strpos( $widget['id'], 'um_' ) === 0 || strpos( $widget['id'], 'um-' ) === 0 ) {
  		$GLOBALS['um_load_assets'] = TRUE;
  	}
  }

}


/**
 * The main function responsible for returning The UMRemoveAssets
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = UMRemoveAssets(); ?>
 *
 * @since 1.0
 * @return object The UMRemoveAssets Instance
 */
function UMRemoveAssets() {

    return UMRemoveAssets::instance();

}

UMRemoveAssets();

endif;
