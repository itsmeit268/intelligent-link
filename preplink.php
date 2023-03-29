<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @link       https://github.com/itsmeit268/preplink
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
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
