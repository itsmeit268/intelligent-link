<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Intelligent Link
 * Plugin URI:        https://itsmeit.co/
 * Description:       Encrypts permitted links, initiates countdown timer before redirection, increases user interaction time, boosts page views, and enhances revenue for websites with advertising like AdSense, Ezoic, etc.
 * Version:           1.1.7
 * Author:            itsmeit.co
 * Author URI:        https://itsmeit.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       intelligent-link
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    die;
}

define('INTELLIGENT_LINK_NAME', 'Intelligent Link');
define('INTELLIGENT_LINK_VERSION', '1.1.7');
define('INTELLIGENT_LINK_PLUGIN_FILE',	__FILE__);
define('INTELLIGENT_LINK_PLUGIN_BASE',	plugin_basename(INTELLIGENT_LINK_PLUGIN_FILE ));
define('INTELLIGENT_LINK_DEV', 1);
define('INTELLIGENT_LINK_PLUGIN_URL', plugin_dir_url( INTELLIGENT_LINK_PLUGIN_FILE ));

$admin_path = plugin_dir_path( __FILE__ ) . 'admin/';

if ( is_dir( $admin_path ) ) {
    foreach ( glob( $admin_path . '*.php' ) as $file ) {
        require_once $file;
    }
}

$include_path = plugin_dir_path( __FILE__ ) . 'includes/';

if ( is_dir( $include_path ) ) {
    foreach ( glob( $include_path . '*.php' ) as $file ) {
        require_once $file;
    }
}

require_once plugin_dir_path( __FILE__ ) . 'templates/process-link.php';