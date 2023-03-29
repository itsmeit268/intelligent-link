<?php

/**
 * @link              https://github.com/itsmeit268/preplink
 * @since             1.0.3
 * @package           Settings_Page
 *
 * @wordpress-plugin
 * Plugin Name:       Prepare Link
 * Plugin URI:        https://itsmeit.co
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.3
 * Author:            itsmeit <itsmeit.biz@gmail.com>
 * Author URI:        https://www.wplauncher.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       prep-link
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('PREPLINK_VERSION', '1.0.3');

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
