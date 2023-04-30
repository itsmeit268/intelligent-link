<?php

/**
 * @link              https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @since             1.0.3
 * @package           Preplink
 *
 * @wordpress-plugin
 * Plugin Name:       Prepare Link
 * Plugin URI:        https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * Description:       The plugin will encrypt the links you allow and perform a countdown time before redirecting to the specified "endpoint" page, helping to increase user engagement time and making it suitable for websites that allow file downloads.
 * Version:           1.0.3
 * Author:            itsmeit <itsmeit.biz@gmail.com>
 * Author URI:        https://itsmeit.biz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       prep-link
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

// Plugin name
define( 'PREPLINK_NAME', 'PrepareLink' );

// Plugin version
define('PREPLINK_VERSION', '1.0.3');

// Plugin Root File
define( 'PREPLINK_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'PREPLINK_PLUGIN_BASE',	plugin_basename( PREPLINK_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'PREPLINK_PLUGIN_DIR',	plugin_dir_path( PREPLINK_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'PREPLINK_PLUGIN_URL',	plugin_dir_url( PREPLINK_PLUGIN_FILE ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-preplink-activator.php
 */
function activate_preplink()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-preplink-activator.php';
    Preplink_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-preplink-deactivator.php
 */
function deactivate_preplink()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-preplink-deactivator.php';
    Preplink_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_preplink');
register_deactivation_hook(__FILE__, 'deactivate_preplink');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-preplink.php';

function run_preplink()
{
    $plugin = new Preplink();
    $plugin->run();
}

run_preplink();
