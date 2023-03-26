<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @since      1.0.0
 * @link       https://github.com/itsmeit268/preplink
 * @package    Preplink
 * @subpackage Preplink/admin
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

class Preplink_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_menu', array($this, 'addPluginAdminMenu'), 9);
        add_action('admin_init', array($this, 'registerAndBuildFields'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Prep_Link_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Prep_Link_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/preplink-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Prep_Link_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Prep_Link_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/preplink-admin.js', array('jquery'), $this->version, false);

    }

    public function addPluginAdminMenu()
    {
        add_menu_page($this->plugin_name, 'Prepare Link', 'administrator', $this->plugin_name, array($this, 'displayPluginAdminSettings'), 'dashicons-chart-area', 26);
    }

    public function displayPluginAdminSettings()
    {
        // set this var to be used in the settings-display view
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        if (isset($_GET['error_message'])) {
            add_action('admin_notices', array($this, 'prepLinkSettingsMessages'));
            do_action('admin_notices', $_GET['error_message']);
        }
        require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
    }

    public function prepLinkSettingsMessages($error_message)
    {
        switch ($error_message) {
            case '1':
                $message = __('There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain');
                $err_code = esc_attr('preplink_setting');
                $setting_field = 'preplink_setting';
                break;
        }
        $type = 'error';
        add_settings_error(
            $setting_field,
            $err_code,
            $message,
            $type
        );
    }

    public function registerAndBuildFields()
    {
        add_settings_section(
            'preplink_general_section',
            '',
            array($this, 'preplink_display_general'),
            'preplink_general_settings'
        );
        unset($args);

        add_settings_field(
            'preplink_endpoint',
            __('Endpoint', 'preplink'),
            array($this, 'preplink_endpoint_field'),
            'preplink_general_settings',
            'preplink_general_section');

        add_settings_field(
            'preplink_textarea',
            __('Multiple URL', 'preplink'),
            array($this, 'preplink_textarea_field'),
            'preplink_general_settings',
            'preplink_general_section'
        );

        register_setting(
            'preplink_general_settings',
            'preplink_setting'
        );
    }

    public function preplink_display_general()
    {
        ?>
        <div class="prep-link-admin-settings">
            <h3>These settings apply to all Prepare link functionality.</h3>
            <ul>
                <li>Endpoint : mặc định là download</li>
                <li>Multiple URL : Những URL sẽ được chuyển hướng đến Endpoint</li>
            </ul>
        </div>
        <?php
    }

    function preplink_endpoint_field()
    {
        $options = get_option('preplink_setting');
        echo '<input type="text" id="preplink_endpoint" name="preplink_setting[preplink_endpoint]" value="' . esc_attr($options['preplink_endpoint']) . '" />';
    }

    public function preplink_textarea_field()
    {
        $options = get_option('preplink_setting', array());
        ?><textarea id="preplink_url" cols='44' rows='4' name='preplink_setting[preplink_url]'><?= isset($options['preplink_url']) ? $options['preplink_url'] : false; ?></textarea><?php
    }

}
