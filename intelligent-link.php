<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Intelligent Link
 * Plugin URI:        https://itsmeit.co/
 * Description:       Encrypts permitted links, initiates countdown timer before redirection, increases user interaction time, boosts page views, and enhances revenue for websites with advertising like AdSense, Ezoic, etc.
 * Version:           1.0.6
 * Author:            itsmeit.co
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

define('INTELLIGENT_LINK_PLUGIN_URL', 'https://itsmeit.co');
define('INTELLIGENT_LINK_NAME', 'Intelligent Link');
define('INTELLIGENT_LINK_VERSION', '1.0.6');
define('INTELLIGENT_LINK_PLUGIN_FILE',	__FILE__);
define('INTELLIGENT_LINK_PLUGIN_BASE',	plugin_basename(INTELLIGENT_LINK_PLUGIN_FILE ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-preplink-activator.php
 */
function activate_intelligent_link() {
    require_once plugin_dir_path(INTELLIGENT_LINK_PLUGIN_FILE) . 'includes/class-intelligent-link-activator.php';
    Intelligent_Link_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-preplink-deactivator.php
 */
function deactivate_intelligent_link() {
    require_once plugin_dir_path(INTELLIGENT_LINK_PLUGIN_FILE) . 'includes/class-intelligent-link-deactivator.php';
    Intelligent_Link_Deactivator::deactivate();
}

register_activation_hook(INTELLIGENT_LINK_PLUGIN_FILE, 'activate_intelligent_link');
register_deactivation_hook(INTELLIGENT_LINK_PLUGIN_FILE, 'deactivate_intelligent_link');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(INTELLIGENT_LINK_PLUGIN_FILE) . 'includes/class-intelligent-link.php';

function run_intelligent_link() {
    $plugin = new Intelligent_Link();
    $plugin->run();
}

run_intelligent_link();
