<?php


class Dollar_Club_Icons_Shortcode {

    private static $instance;

    public static function instance() {

        if ( !isset( self::$instance ) || !( self:: $instance instanceof Dollar_Club_Icons_Shortcode ) ) {

            self::$instance = new Dollar_Club_Icons_Shortcode();

        }
        return self::$instance;

    }

    public function __construct() {

        add_shortcode( 'dollarclubicons', __CLASS__ . '::dollarclubicons_shortcode' );

    }

    /**
     * Basically we're hiding overnight options on weekends.
     */
    public static function dollarclubicons_shortcode( $atts ) {

      $a = shortcode_atts( array(
    		'icon' => '',
        'desktopw' => '',
        'tabletw' => '',
        'mobilew' => '',
        'fill' => '',
        'stroke' => '',
        'padding' => ''
    	), $atts );
      if ( empty( $a['icon'] ) ) { return ''; }
      $icon = str_replace( '.svg', '', $a['icon'] );
      $file_contents = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'assets/icons/' . $icon . '.svg' );
      if ( !$file_contents ) {
        return '';
      }
      $style_str = '<style type="text/css" rel="stylesheet">.dollarclub-icon-' . $icon . ' { display: inline-block; }';
      $size_class = '';
      $fill_class = '';
      $stroke_class = '';
      $padding_class = '';
      if ( !empty( $a['mobilew'] ) ) {
        $size_class = 'mobilew' . $a['mobilew'];
        $style_str .= '.' . $size_class . ' { width: ' . $a['mobilew'] . 'px; height: auto; }';
      }
      if ( !empty( $a['tabletw'] ) ) {
        $tablet_class = 'tabletw' . $a['tabletw'];
        $size_class .= ' ' . $tablet_class;
        $style_str .= '@media only screen and (min-width: 768px) { .' . $tablet_class . ' { width: ' . $a['tabletw'] . 'px; height: auto; } }';
      }
      if ( !empty( $a['desktopw'] ) ) {
        $desktop_class = 'desktopw' . $a['desktopw'];
        $size_class .= ' ' . $desktop_class;
        $style_str .= '@media only screen and (min-width: 1025px) { .' . $desktop_class . ' { width: ' . $a['desktopw'] . 'px; height: auto; } }';
      }
      if ( !empty( $a['fill'] ) ) {
        $fill = str_replace( '#', '', $a['fill'] );
        $fill_class = 'svgfill' . $fill;
        $style_str .= '.' . $fill_class . ' path { fill: #' . $fill . '; }';
      }
      if ( !empty( $a['stroke'] ) ) {
        $stroke = str_replace( '#', '', $a['stroke'] );
        $stroke_class = 'svgstroke' . $stroke;
        $style_str .= '.' . $stroke_class . ' path { stroke: #' . $stroke . '; }';
      }
      if ( !empty( $a['padding'] ) ) {
        $padding = str_replace( 'px', '', $a['padding'] );
        $padding_class = 'svgpadding' . $padding;
        $style_str .= '.' . $padding_class . ' { padding: ' . $padding . 'px; }';
      }
      $style_str .= '</style>';
      return $style_str . '<div class="dollarclub-icon-' . $icon . ' ' . $size_class . ' ' . $fill_class . ' ' . $stroke_class . ' ' . $padding_class . '">' . $file_contents . '</div>';

    }

}

/**
 * The main function responsible for returning The Dollar_Club_Icons_Shortcode
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $variable = Dollar_Club_Icons_Shortcode(); ?>
 *
 * @since 1.0
 * @return object The Dollar_Club_Icons_Shortcode Instance
 */
function Dollar_Club_Icons_Shortcode() {

    return Dollar_Club_Icons_Shortcode::instance();

}

Dollar_Club_Icons_Shortcode();
