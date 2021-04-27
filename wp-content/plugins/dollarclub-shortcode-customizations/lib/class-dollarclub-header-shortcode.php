<?php
/*
Copyright (C)  2020, Tractus, Inc
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'DollarClubHeaderMaker' ) ):

class DollarClubHeaderMaker {

  private static $product_data_tab_id = 'codeable_test_custom_product_data';
  private static $extra_fee_enabled_id = '_codeable_extra_fee';
  private static $extra_fee_amount_id = '_codeable_extra_fee_amount';

  private static $instance;

  public static function instance() {

    if ( !isset( self::$instance ) || !( self:: $instance instanceof DollarClubHeaderMaker ) ) {

      self::$instance = new DollarClubHeaderMaker();

    }
    return self::$instance;

  }

  public function __construct() {

    add_action( 'wp_enqueue_scripts', __CLASS__ . '::dollarclub_shortcode_enqueue_style' );

    add_shortcode( 'dollarclub-header', __CLASS__ . '::dollarclub_header_shortcode' );

  }

  public static function dollarclub_shortcode_enqueue_style() {
      wp_enqueue_style( 'dollarclub-header-style', plugin_dir_url( dirname( __FILE__ ) ) . '/assets/css/dollarclub-header-shortcode-styling.css' );
  }

  public static function dollarclub_header_shortcode( $atts ) {


    $a = shortcode_atts( array(
      'id'                                 => uniqid('id_'),
      'background_image'                   => '',
      'background_overlay'                 => 'false',
      'background_overlay_color'           => '#000000B0',
      'image_height'                       => '42.86vw',
      'heading'                            => '',
      'heading_2'                          => '',
      'sub_heading'                        => '',
      'sub_heading_2'                      => '',
      'button'                             => '',
      'button-link'                        => '#',
      'button-class'                       => '',
      'heading_font_size'                  => '3.5vw',
      'heading_font_size_2'                => '3.5vw',
      'heading_font_size_large_screen'     => '3.5vw',
      'heading_font_size_large_screen_2'   => '3.5vw',
      'heading_max_width'                  => '55%',
      'heading_max_width_2'                => '55%',
      'heading_color'                      => '#000',
      'heading_color_2'                    => '#000',
      'sub_heading_color'                  => '#000',
      'sub_heading_color_2'                => '#000',
      'sub_heading_font_size'              => '32px',
      'sub_heading_font_size_2'            => '32px',
      'position_text'                      => 'center',
      'position_text_vertical'             => 'top',
      'text_background_color'              => '',
      'show_mobile'                        => 'true',
      'mobile_background_image'            => '',
      'mobile_image_height'                => '70vw',
      'mobile_fade_color'                  => '#000'
    ), $atts );

    $header_id_selector = '#' . $a['id']



    ?>
    <style>
      <?php if($a['show_mobile'] !== 'true'){?>
        <?php echo $header_id_selector ?> {
          display: none;
        }
      <?php } else { ?>
        .woocommerce-products-header {
          display: inherit;
        }

        <?php echo $header_id_selector ?> {
          background-color: <?php echo $a['mobile_fade_color'];?>;
        }

        <?php echo $header_id_selector ?> .dollarclub-header-content {
          background: linear-gradient(0, <?php echo $a['mobile_fade_color'];?> 65%, transparent);
        }

      <?php } ?>

      <?php if($a['mobile_background_image']){?>
        <?php echo $header_id_selector ?> .dollarclub-header-wrapper {
          background: url(<?php echo $a['mobile_background_image'] ?>) no-repeat center / cover;
        }
      <?php } else if($a['background_image']) { ?>
        <?php echo $header_id_selector ?> .dollarclub-header-wrapper {
          background: url(<?php echo $a['background_image'] ?>) no-repeat center / cover;
        }
      <?php } ?>

      <?php echo $header_id_selector ?> .dollarclub-header-wrapper {
        min-height:<?php echo $a['mobile_image_height'] ?>;
      }

      <?php if ($a['background_overlay'] == 'mobile') { ?>
        .dollarclub-header-wrapper-overlay {
          background-color: <?php echo $a['background_overlay_color'];?>;
        }
      <?php } ?>;

      <?php echo $header_id_selector ?> .dollarclub-position-text{

          <?php if($a['text_background_color']){?>
            background: <?php echo $a['text_background_color'] ?>;
          <?php } ?>

      }
      <?php echo $header_id_selector ?> .dollarclub-heading-1 {
        color:<?php echo $a['heading_color'] ?>;
      }
      <?php echo $header_id_selector ?> .dollarclub-heading-2 {
        color:<?php echo $a['heading_color_2'] ?>;
      }
      <?php echo $header_id_selector ?> .dollarclub-sub-heading-1{
        color:<?php echo $a['sub_heading_color'] ?>;
      }
      <?php echo $header_id_selector ?> .dollarclub-sub-heading-2{
        color:<?php echo $a['sub_heading_color_2'] ?>;
      }

      @media (min-width: 767px) {
        <?php if($a['mobile_background_image'] && $a['background_image']){?>
          <?php echo $header_id_selector ?> .dollarclub-header-wrapper {
            background: url(<?php echo $a['background_image'] ?>) no-repeat center / cover;
          }
        <?php } ?>
        <?php echo $header_id_selector ?> .dollarclub-header-wrapper {
          min-height:<?php echo $a['image_height'] ?>;
        }
        <?php if ($a['background_overlay'] == 'desktop') { ?>
          .dollarclub-header-wrapper-overlay {
            background-color: <?php echo $a['background_overlay_color'];?>;
          }
        <?php } ?>;
        <?php echo $header_id_selector ?> .dollarclub-header-content {
          background: none;
        }
        <?php echo $header_id_selector ?> .dollarclub-position-text{
            max-width:<?php echo $a['heading_max_width'] ?>;
        }
        <?php echo $header_id_selector ?> .dollarclub-heading-1 {
          font-size:<?php echo $a['heading_font_size'] ?>;
        }
        <?php echo $header_id_selector ?> .dollarclub-heading-2 {
          font-size:<?php echo $a['heading_font_size_2'] ?>;
        }
        <?php echo $header_id_selector ?> .dollarclub-sub-heading-1{
          font-size: <?php echo $a['sub_heading_font_size'] ?>;
        }
        <?php echo $header_id_selector ?> .dollarclub-sub-heading-2{
          font-size: <?php echo $a['sub_heading_font_size'] ?>;
        }
      }

      @media (min-width: 1600px) {
        <?php echo $header_id_selector ?> .dollarclub-heading-1 {
          font-size: <?php echo $a['heading_font_size_large_screen'] ?>;
        }
        <?php echo $header_id_selector ?> .dollarclub-heading-2 {
          font-size: <?php echo $a['heading_font_size_large_screen_2'] ?>;
        }
      }
    </style>
    <div class="dollarclub-header-outer-wrapper" id="<?php echo $a['id'] ?>">
      <div class="dollarclub-header-wrapper" >
        <?php if ($a['background_overlay'] !== 'false') {?>
        <div class="dollarclub-header-wrapper-overlay" >
        <?php } ?>
          <div class="dollarclub-header-content <?php echo $a['position_text_vertical'];?>">
            <div class="dollarclub-position-text <?php echo $a['position_text'];?>">
              <?php if($a['heading']){?><h1 class="dollarclub-heading-1"><?php echo $a['heading'] ?></h1><?php } ?>
              <?php if($a['heading_2']){?><h2 class="dollarclub-heading-2"><?php echo $a['heading_2'] ?></h2><?php } ?>
              <?php if($a['sub_heading']){?><p class="dollarclub-sub-heading-1"><?php echo $a['sub_heading'] ?></p><?php } ?>
              <?php if($a['sub_heading_2']){?><p class="dollarclub-sub-heading-2"><?php echo $a['sub_heading_2'] ?></p><?php } ?>
              <?php if($a['button-link']){?><a href="<?php echo $a['button-link']?>"><?php } ?>
              <?php if($a['button']){?><button class="dollarclub-button <?php echo $a['button-class']?>" style="border-radius:7px;"><?php echo $a['button'] ?></button><?php } ?>
              <?php if($a['button-link']){?></a><?php } ?>
            </div>
          </div>
        <?php if($a['background_overlay'] !== 'false') {?>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php
  }

}


/**
 * The main function responsible for returning The DollarClubHeaderMaker
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = DollarClubHeaderMaker(); ?>
 *
 * @since 1.0
 * @return object The DollarClubHeaderMaker Instance
 */
function DollarClubHeaderMaker() {

    return DollarClubHeaderMaker::instance();

}

DollarClubHeaderMaker();

endif;
