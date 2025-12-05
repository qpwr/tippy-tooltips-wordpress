<?php
/**
 * Plugin Name:       Tippy Tooltips
 * Description:       Add Tippy.js tooltips to WordPress
 * Version:           1.0.1
 * Requires at least: 4.7
 * Requires PHP:      5.6
 * Author:            Daniel M. Hendricks
 * Author URI:        https://daniel.hn/
 * Plugin URI:        https://github.com/dmhendricks/tippy-tooltips-wordpress/
 * License:           GPL2+
 * License URI:       https://github.com/dmhendricks/tippy-tooltips-wordpress/blob/master/LICENSE
 */
namespace CloudVerve;
defined( 'ABSPATH' ) || die();

final class Tippy_Tooltips {

  private static $instance;
  protected static $config;
  
  final public static function init() {

    if ( !isset( self::$instance ) && !( self::$instance instanceof Tippy_Tooltips ) ) {

      self::$instance = new Tippy_Tooltips;
      self::$config = self::get_plugin_meta();

      // Enqueue scripts
      add_action( 'wp_enqueue_scripts', array( self::$instance, 'enqueue_scripts' ) );
      add_action( 'admin_enqueue_scripts', array( self::$instance, 'enqueue_scripts' ) );

    }

    return self::$instance;

  }


  /**
   * Enqueue required scripts. Usage:
   *    WP Admin only:  define( 'TIPPY_ENQUEUE_SCRIPTS', 'admin' );
   *    Frontend only:  define( 'TIPPY_ENQUEUE_SCRIPTS', 'public' );
   *    Both (default): define( 'TIPPY_ENQUEUE_SCRIPTS', true );
   *
   * @since 1.0.0
   */
  public static function enqueue_scripts() {

    $action = current_action();
    $target = defined( 'TIPPY_ENQUEUE_SCRIPTS' ) ? TIPPY_ENQUEUE_SCRIPTS : true;
    $script_url = self::get_config( 'plugin_url' ) . 'dist/js/tippy-tooltips.js';

    // Register script
    wp_register_script( 'tippy-tooltips', $script_url, null, self::get_config( 'version' ), true );
    if( defined( 'TIPPY_ENQUEUE_SCRIPTS') && TIPPY_ENQUEUE_SCRIPTS === false ) return;

    // Enqueue script
    if( $target === true || ( $action == 'wp_enqueue_scripts' && $target == 'public' ) || ( $action == 'admin_enqueue_scripts' && $target == 'admin' ) ) {
      wp_enqueue_script( 'tippy-tooltips' );
    }

  }

  /**
   * Get meta fields from plugin header
   *
   * @return array
   * @since 1.0.0
   */
  public static function get_plugin_meta() {

    // Get plugin meta data
    $plugin_data = get_file_data( __FILE__, [
      'name' => 'Plugin Name',
      'version' => 'Version'
    ], 'plugin' );

    // Add plugin directory and URL
    $plugin_meta = array_merge( $plugin_data, [
      'plugin_dir' => dirname( __FILE__ ),
      'plugin_url' => plugin_dir_url( __FILE__ )
    ]);

    return $plugin_meta;

  }

  /**
   * Get plugin configuration variable. Example usage:
   *    var_dump( self::get_config( 'key' ) );
   *
   * @param string $key Configuration variable path to retrieve
   * @param mixed $default The default value to return if $key is not found
   * @since 1.0.0
   */
  public static function get_config( $key = null, $default = null ) {

    // If key not specified, return entire registry
    if ( !$key ) {
        return self::$config;
    }

    // Else return $key value or null if doesn't exist
    $value = self::$config;
    foreach( explode( '/', $key ) as $k ) {
        if ( !isset( $value[$k] ) ) {
            return $default;
        }
        $value = &$value[$k];
    }
    return $value;

  }

}

// Initialize plugin
if( !wp_doing_cron() && !wp_doing_ajax() ) Tippy_Tooltips::init();
