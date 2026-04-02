<?php
/**
 * Plugin Name: WooCommerce Subscription Access Control Demo
 * Description: Demo plugin for subscription-based access control.
 * Version: 1.0.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'IILLC_WSACD_VERSION', '1.0.0' );
define( 'IILLC_WSACD_PATH', plugin_dir_path( __FILE__ ) );

require_once IILLC_WSACD_PATH . 'includes/class-plugin.php';

function iillc_wsacd_init() {
    $plugin = new IILLC_Plugin();
    $plugin->init();
}
add_action( 'plugins_loaded', 'iillc_wsacd_init' );
