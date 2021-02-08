<?php

if( !defined( 'ABSPATH' ) ) die();
require( __DIR__ . '/vendor/autoload.php' );
require( __DIR__ . '/app/Settings_Page.php' );

class Licensing_Addon_Example_Plugin {

public $statusmESSAGE;

  function __construct() {

    // Validate license
    $license_check = new \WordPress_ToolKit\Licensing\WHMCS_License( __DIR__ . '/plugin.json', array( 'plugin' => array( 'path' => __DIR__ ) ) );

    $result = $license_check->validate( $this->get_plugin_option( $license_check->get_config( 'prefix' ) . '_license_key', $license_check->get_config( 'prefix' ) . '_options' ), get_option( $license_check->get_config( 'prefix' ) . '_local_key' ) );
    if( isset( $result['remotecheck'] ) ) update_option( $license_check->get_config( 'prefix' ) . '_local_key', isset( $result['localkey'] ) ? $result['localkey'] : '' );

    // Load settings page
    new \Licensing_Example\Settings_Page( $license_check->get_config(), $result['status'] );
  
	$this->statusmESSAGE = $result['status'];

    // Run plugin logic - in this example, we'll create a [hello_world] shortcode
    if ( ! shortcode_exists( 'hello_world' ) ) {
        add_shortcode( 'hello_world', array( $this, 'hello_world_shortcode' ) );
    }
	
	add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );
  }

  /**
    * Get plugin option, with object caching (if available).
    *
    * @param string $key The name of the option key
    * @return mixed The value of specified option key
    * @link https://github.com/tareq1988/wordpress-settings-api-class WPSAC options
    */
  private function get_plugin_option( $key, $group, $cache = true ) {

    $options = get_option( $group );
    return isset( $options[ $key ] ) ? $options[ $key ] : null;

  }

  /**
    * Display admin notice on license check failure
    */
  public function show_admin_notice() {

     if( $this->statusmESSAGE!=='Active'){
        $class = 'notice notice-error';
  	$message = 'Invalid license. Response: ' . $this->statusmESSAGE;

  	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
      }
  }

  /**
    * Create a simple [hello_world] shortcode.
    */
  public function hello_world_shortcode() {
    return "Hello World!";
  }

}

//new Licensing_Addon_Example_Plugin();