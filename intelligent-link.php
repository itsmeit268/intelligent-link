<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Intelligent Link
 * Plugin URI:        https://itsmeit.co/
 * Description:       Encrypts permitted links, initiates countdown timer before redirection, increases user interaction time, boosts page views, and enhances revenue for websites with advertising like AdSense, Ezoic, etc.
 * Version:           1.0.6
 * Author:            itsmeit <buivanloi.2010@gmail.com>
 * Author URI:        https://itsmeit.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       intelligent-link
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

define('PREPLINK_PLUGIN_URL', 'https://itsmeit.co');
define('PREPLINK_NAME', 'Intelligent Link');
define('PREPLINK_VERSION', '1.0.6');
define('PREPLINK_PLUGIN_FILE',	__FILE__);
define('PREPLINK_PLUGIN_BASE',	plugin_basename(PREPLINK_PLUGIN_FILE ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-preplink-activator.php
 */
function activate_preplink()
{
    require_once plugin_dir_path(PREPLINK_PLUGIN_FILE) . 'includes/class-preplink-activator.php';
    Preplink_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-preplink-deactivator.php
 */
function deactivate_preplink()
{
    require_once plugin_dir_path(PREPLINK_PLUGIN_FILE) . 'includes/class-preplink-deactivator.php';
    Preplink_Deactivator::deactivate();
}

register_activation_hook(PREPLINK_PLUGIN_FILE, 'activate_preplink');
register_deactivation_hook(PREPLINK_PLUGIN_FILE, 'deactivate_preplink');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(PREPLINK_PLUGIN_FILE) . 'includes/class-preplink.php';

function run_preplink() {
    $plugin = new Preplink();
    $plugin->run();
}

run_preplink();
