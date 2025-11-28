<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Intelligent Link
 * Plugin URI:        https://itsmeit.co/
 * Description:       Encrypts permitted links, initiates countdown timer before redirection, increases user interaction time, boosts page views, and enhances revenue for websites with advertising like AdSense, Ezoic, etc.
 * Version:           1.1.8
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
define('INTELLIGENT_LINK_VERSION', '1.1.8');
define('INTELLIGENT_LINK_PLUGIN_FILE',	__FILE__);
define('INTELLIGENT_LINK_PLUGIN_BASE',	plugin_basename(INTELLIGENT_LINK_PLUGIN_FILE ));
define('INTELLIGENT_LINK_DEV', 1);
define('INTELLIGENT_LINK_PLUGIN_URL', plugin_dir_url( INTELLIGENT_LINK_PLUGIN_FILE ));

function load_admin_file() {

    if (!is_admin()) {
        return;
    }

    $admin_files = array(
        'ilgl-admin.php',
    );

    $admin_path = plugin_dir_path( __FILE__ ) . 'admin/';

    foreach ( $admin_files as $file ) {
        $file_path = $admin_path . $file;
        if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
            require_once $file_path;
        }
    }
}

load_admin_file();

$include_path = plugin_dir_path( __FILE__ ) . 'includes/';

if ( is_dir( $include_path ) ) {
    foreach ( glob( $include_path . '*.php' ) as $file ) {
        require_once $file;
    }
}

require_once plugin_dir_path( __FILE__ ) . 'templates/process-link.php';

add_action('init', 'load_text_domain');
function load_text_domain() {
    load_plugin_textdomain('intelligent-link', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
