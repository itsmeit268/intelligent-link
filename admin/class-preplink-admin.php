<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
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
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_menu', array($this, 'add_prep_link_admin_menu'), 9);
        add_action('admin_init', array($this, 'register_and_build_fields'));
        add_action('plugin_action_links_' . PREPLINK_PLUGIN_BASE, array($this, 'add_plugin_action_link'), 20);
    }

    /**
     * Register the stylesheets for the admin area.
     *

     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/preplink-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *

     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('preplink-admin', plugin_dir_url(__FILE__) . 'js/preplink-admin.js', array('wp-i18n'), $this->version, false);
//        wp_set_script_translations('preplink-admin', 'prep-link', plugin_dir_path( __FILE__ ) . 'languages' );
    }

    public function add_prep_link_admin_menu()
    {
        add_menu_page($this->plugin_name, 'Prepare Link', 'manage_options', $this->plugin_name, array($this, 'prep_link_admin_form_settings'), 'dashicons-chart-area', 26);
        add_submenu_page($this->plugin_name, 'Settings', 'Settings', 'manage_options', $this->plugin_name . '-settings', array($this, 'prep_link_admin_form_settings'));
    }

    public function prep_link_admin_form_settings()
    {
        // set active tab based on query parameter or default to 'general'
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

        // define tabs array
        $tabs = array(
            'general'   => __( 'General', 'preplink' ),
            'advertising'  => __( 'Advertising', 'preplink' ),
            'faq' => __( 'FAQ', 'preplink' ),
            'endpoint' => __( 'Endpoint', 'preplink' )
        );

        // output tabs
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $active_tab) ? ' nav-tab-active' : '';
            echo '<a class="nav-tab' . $class . '" href="?page=preplink-settings&tab=' . $tab . '">' . $name . '</a>';
        }
        echo '</h2>';

        // output settings page content based on active tab
        switch ($active_tab) {
            case 'general':
                echo '<div class="wrap"><h1>' . __('General Settings', 'preplink') . '</h1>';
                settings_errors();
                echo '<form method="post" action="options.php">';
                settings_fields('preplink_general_settings');
                do_settings_sections('preplink_general_settings');
                submit_button();
                echo '</form></div>';
                break;
            case 'advertising':
                echo '<div class="wrap"><h1>' . __('Advertising Settings', 'preplink') . '</h1>';
                settings_errors();
                echo '<form method="post" action="options.php">';
                settings_fields('preplink_advertising_settings');
                do_settings_sections('preplink_advertising_settings');
                submit_button();
                echo '</form></div>';
                break;
            case 'faq':
                echo '<div class="wrap"><h1>' . __('FAQ Settings', 'preplink') . '</h1>';
                settings_errors();
                echo '<form method="post" action="options.php">';
                settings_fields('preplink_faq_settings');
                do_settings_sections('preplink_faq_settings');
                submit_button();
                echo '</form></div>';
                break;
            case 'endpoint':
                echo '<div class="wrap"><h1>' . __('Endpoint Settings', 'preplink') . '</h1>';
                settings_errors();
                echo '<form method="post" action="options.php">';
                settings_fields('preplink_endpoint_settings');
                do_settings_sections('preplink_endpoint_settings');
                submit_button();
                echo '</form></div>';
                break;
        }
    }

    public function prep_link_settings_tabs( $current = 'general' ) {
        $tabs = array(
            'general'   => __( 'General Settings', 'preplink' ),
            'preplink_advertising'  => __( 'Advertising Settings', 'preplink' ),
            'preplink_faq'  => __( 'FAQ Settings', 'preplink' ),
            'preplink_endpoint'  => __( 'Endpoint Settings', 'preplink' )
        );
        $html = '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
            $class = ( $tab == $current ) ? 'nav-tab-active' : '';
            $html .= '<a class="nav-tab ' . $class . '" href="?page=preplink-settings&tab=' . $tab . '">' . $name . '</a>';
        }
        $html .= '</h2>';
        echo $html;
    }

    /**
     * @param $links
     * @return mixed
     */
    public function add_plugin_action_link($links)
    {
        $setting_link = '<a href="' . esc_url(get_admin_url()) . 'admin.php?page=preplink-settings">' . __('Settings', 'preplink') . '</a>';
        $donate_link = '<a href="//itsmeit.co" title="' . __('Donate Now', 'preplink') . '" target="_blank" style="font-weight:bold">' . __('Donate', 'preplink') . '</a>';
        array_unshift($links, $donate_link);
        array_unshift($links, $setting_link);
        return $links;
    }

    public function register_and_build_fields()
    {
        add_settings_section(
            'preplink_general_section',
            '',
            array($this, 'preplink_display_general'),
            'preplink_general_settings'
        );

        add_settings_section(
            'preplink_advertising_section',
            '',
            array($this, 'preplink_advertising_display'),
            'preplink_advertising_settings'
        );

        add_settings_section(
            'preplink_faq_section',
            '',
            array($this, 'preplink_faq_display'),
            'preplink_faq_settings'
        );

        add_settings_section(
            'preplink_endpoint_section',
            '',
            array($this, 'preplink_endpoint_display'),
            'preplink_endpoint_settings'
        );

        unset($args);

        add_settings_field(
            'preplink_enable_plugin', 
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_enable_plugin'),
            'preplink_general_settings', 
            'preplink_general_section', 
            array( 
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_endpoint',
            __('Endpoint', 'prep-link'),
            array($this, 'preplink_endpoint_field'),
            'preplink_endpoint_settings',
            'preplink_endpoint_section');

        add_settings_field(
            'preplink_text_complete',
            __('Text Complete', 'prep-link'),
            array($this, 'preplink_text_complete'),
            'preplink_general_settings',
            'preplink_general_section');

        add_settings_field(
            'preplink_cookie_time',
            __('Link expiration time', 'prep-link'),
            array($this, 'preplink_cookie_time'),
            'preplink_endpoint_settings',
            'preplink_endpoint_section');

        add_settings_field(
            'preplink_endpoint_auto_direct', 
            __('Auto redirect', 'prep-link'),
            array($this, 'preplink_endpoint_auto_direct'),
            'preplink_endpoint_settings', 
            'preplink_endpoint_section', 
            array( 
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'preplink_textarea',
            __('Links allowed', 'prep-link'),
            array($this, 'preplink_textarea_field'),
            'preplink_general_settings',
            'preplink_general_section'
        );

        add_settings_field(
            'preplink_excludes_element',
            __('Element excluded', 'prep-link'),
            array($this, 'preplink_excludes_element'),
            'preplink_general_settings',
            'preplink_general_section'
        );

        add_settings_field(
            'preplink_image', 
            __('Post Image', 'prep-link'),
            array($this, 'preplink_image_field'),
            'preplink_endpoint_settings', 
            'preplink_endpoint_section', 
            array( 
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'preplink_excerpt', 
            __('Post Excerpt', 'prep-link'),
            array($this, 'preplink_excerpt_field'),
            'preplink_endpoint_settings', 
            'preplink_endpoint_section', 
            array( 
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'preplink_excerpt', 
            __('Post Excerpt', 'prep-link'),
            array($this, 'preplink_excerpt_field'),
            'preplink_endpoint_settings', 
            'preplink_endpoint_section', 
            array( 
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'preplink_faq_1',
            __('FAQ 1', 'prep-link'),
            array($this, 'preplink_display_faq_1'),
            'preplink_faq_settings',
            'preplink_faq_section',
            array('label_for' => 'preplink_faq')
        );

        add_settings_field(
            'preplink_faq_2',
            __('FAQ 2', 'prep-link'),
            array($this, 'preplink_display_faq_2'),
            'preplink_faq_settings',
            'preplink_faq_section',
            array('label_for' => 'preplink_faq_2')
        );

        add_settings_field(
            'preplink_related_post', 
            __('Post Related', 'prep-link'),
            array($this, 'preplink_related_post'),
            'preplink_endpoint_settings', 
            'preplink_endpoint_section', 
            array( 
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'preplink_comment', 
            __('Comment', 'prep-link'),
            array($this, 'preplink_comment'),
            'preplink_endpoint_settings', 
            'preplink_endpoint_section', 
            array( 
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'preplink_display_mode',
            __('Display mode', 'prep-link'),
            array($this, 'preplink_display_mode'),
            'preplink_general_settings',
            'preplink_general_section',
            array(
                'wait_time' => 'Countdown',
                'progress' => 'ProgressBar',
            )
        );

        add_settings_field(
            'preplink_auto_direct', 
            __('Automatically redirect post to endpoint', 'prep-link'),
            array($this, 'preplink_post_auto_direct'),
            'preplink_general_settings', 
            'preplink_general_section', 
            array( 
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'preplink_advertising_1', 
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_1'),
            'preplink_advertising_settings', 
            'preplink_advertising_section', 
            array( 
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_advertising_2', 
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_2'),
            'preplink_advertising_settings', 
            'preplink_advertising_section', 
            array( 
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_advertising_3', 
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_3'),
            'preplink_advertising_settings', 
            'preplink_advertising_section', 
            array( 
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_advertising_4', 
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_4'),
            'preplink_advertising_settings', 
            'preplink_advertising_section', 
            array( 
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_advertising_5', 
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_5'),
            'preplink_advertising_settings', 
            'preplink_advertising_section', 
            array( 
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_advertising_6', 
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_6'),
            'preplink_advertising_settings', 
            'preplink_advertising_section', 
            array( 
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_advertising_7',
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_7'),
            'preplink_advertising_settings',
            'preplink_advertising_section',
            array(
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_advertising_8',
            __('Enable/Disable', 'prep-link'),
            array($this, 'preplink_filed_advertising_8'),
            'preplink_advertising_settings',
            'preplink_advertising_section',
            array(
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );


        add_settings_field(
            'preplink_custom_style',
            __('Custom Style', 'prep-link'),
            array($this, 'preplink_custom_style'),
            'preplink_general_settings',
            'preplink_general_section'
        );

        add_settings_field(
            'preplink_delete_option',
            __('Delete all data after remove plugin', 'prep-link'),
            array($this, 'preplink_delete_option_on_uninstall'),
            'preplink_general_settings',
            'preplink_general_section'
        );


        register_setting(
            'preplink_general_settings',
            'preplink_setting'
        );

        register_setting(
            'preplink_advertising_settings',
            'preplink_advertising'
        );

        register_setting(
            'preplink_faq_settings',
            'preplink_faq'
        );

        register_setting(
            'preplink_endpoint_settings',
            'preplink_endpoint'
        );
    }

    public function preplink_display_general()
    {
        ?>
        <div class="prep-link-admin-settings">
            <h3>These settings are applicable to all Prepare link functionalities.</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a> | <a href="//itsmeit.biz"
                                                                                       target="_blank">itsmeit.biz</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Preplink Plugin</a></span>
        </div>
        <?php
    }

    public function preplink_advertising_display()
    {
        ?>
        <div class="prep-link-ads-settings">
            <h3>You can add the advertising code here, it will apply to the page endpoint. You can also use the <a href="//wordpress.org/plugins/ad-inserter/" target="_blank">Ad Inserter</a> plugin to insert the ad code</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a> | <a href="//itsmeit.biz"
                                                                                       target="_blank">itsmeit.biz</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Preplink Plugin</a></span>
        </div>
        <?php
    }

    public function preplink_faq_display()
    {
        ?>
        <div class="prep-link-faq-settings">
            <h3>You can add the FAQ HTML code here, it will apply to the page endpoint.</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a> | <a href="//itsmeit.biz"
                                                                                       target="_blank">itsmeit.biz</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Preplink Plugin</a></span>
        </div>
        <?php
    }

    public function preplink_endpoint_display()
    {
        ?>
        <div class="prep-link-endpoint-settings">
            <h3>This setting will apply only to the endpoint page.</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a> | <a href="//itsmeit.biz"
                                                                                       target="_blank">itsmeit.biz</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Preplink Plugin</a></span>
        </div>
        <?php
    }

    public function preplink_enable_plugin($args)
    {
        $settings = get_option('preplink_setting', array());
        $selected = isset($settings['preplink_enable_plugin']) ? $settings['preplink_enable_plugin'] : '1';
        $html = '<select id="preplink_enable_plugin" name="preplink_setting[preplink_enable_plugin]" class="preplink_enable_plugin">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Enable or disable plugin (The prepared link will be ready when enabled).</p>';
        echo $html;
    }

    public function preplink_endpoint_field()
    {
        $settings = get_option('preplink_endpoint', array());
        ?>
        <input type="text" id="endpoint" name="preplink_endpoint[endpoint]" placeholder="download"
               value="<?= esc_attr(!empty($settings['endpoint']) ? $settings['endpoint'] : false) ?>"/>
        <p class="description">The default endpoint for the link format is set to "download", which means that the link will be in the following format: domain.com/post/download.</p>
        <p class="description" style="color: red">If you make changes to the endpoint, it is necessary to navigate to Settings->Permalinks->Save in order to synchronize the endpoint.</p>
        <?php
        if (isset($_POST['preplink_endpoint'])) {
            $settings = $_POST['preplink_endpoint'];
            update_option('preplink_endpoint', $settings);
        }
    }

    public function preplink_text_complete()
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <input type="text" id="preplink_text_complete" name="preplink_setting[preplink_text_complete]"
               placeholder="[Link ready!]"
               value="<?= esc_attr(!empty($settings['preplink_text_complete']) ? $settings['preplink_text_complete'] : false) ?>"/>
        <p class="description">Text display after countdown complete. (default [Link ready!])</p>
        <?php
    }

    public function preplink_cookie_time()
    {
        $settings = get_option('preplink_endpoint', array());
        ?>
        <input type="number" id="cookie_time" name="preplink_endpoint[cookie_time]" placeholder="5"
               value="<?= isset($settings['cookie_time']) ? ($settings['cookie_time'] == '0' ? 0 : $settings['cookie_time']) : '' ?>"/>
        <p class="description">Default link expiration time is 5 minutes</p>
        <?php
    }

    public function preplink_textarea_field()
    {
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_url" cols="50" rows="5" name="preplink_setting[preplink_url]" placeholder="drive.google.com,">';
        $html .= isset($settings["preplink_url"]) ? $settings["preplink_url"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">These links/URLs will be redirected to the endpoint (Prepare Link), each separated by a comma (,).</p>';
        $html .= '<p class="description">Default: fshare.vn,drive.google.com</p>';
        echo $html;
    }

    public function preplink_excludes_element()
    {
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_excludes_element" cols="50" rows="5" name="preplink_setting[preplink_excludes_element]" placeholder=".prep-link-download-btn,.prep-link-btn">';
        $html .= isset($settings["preplink_excludes_element"]) ? $settings["preplink_excludes_element"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">The elements will be excluded, each separated by a comma (,).</p>';
        $html .= '<p class="description">For example: #prep-link-download-btn, .prep-link-download-btn.</p>';
        $html .= '<p class="description">Default: .prep-link-download-btn,.prep-link-btn</p>';
        echo $html;
    }

    public function preplink_image_field($args)
    {
        $settings = get_option('preplink_endpoint', array());
        $selected = isset($settings['preplink_image']) ? $settings['preplink_image'] : '1';
        $html = '<select id="preplink_image" name="preplink_endpoint[preplink_image]" class="preplink_image">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Enable or disable post featured image.</p>';
        echo $html;
    }

    public function preplink_excerpt_field($args)
    {
        $settings = get_option('preplink_endpoint', array());
        $selected = isset($settings['preplink_excerpt']) ? $settings['preplink_excerpt'] : '1';
        $html = '<select id="preplink_excerpt" name="preplink_endpoint[preplink_excerpt]" class="preplink_excerpt">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        echo $html;
    }

    public function preplink_display_faq_1()
    {
        $settings = get_option('preplink_faq', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_faq1_enabled">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">Enable/Disable</label>
                    <select name="preplink_faq[preplink_faq1_enabled]" id="preplink_faq1_enabled">
                        <option value="1" <?php selected(isset($settings['preplink_faq1_enabled']) && $settings['preplink_faq1_enabled'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_faq1_enabled']) && $settings['preplink_faq1_enabled'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_faq1_title">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">FAQ Title</label>
                    <input type="text" name="preplink_faq[preplink_faq1_title]"
                           placeholder="Notes before continuing"
                           value="<?= esc_attr(isset($settings['preplink_faq1_title']) ? $settings['preplink_faq1_title'] : false); ?>"/>
                </td>
            </tr>
            <tr class="preplink_faq1_description">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">FAQ Description (HTML)</label>
                    <?php
                    $html = '<textarea name="preplink_faq[preplink_faq1_description]" rows="10" cols="70">';
                    $html .= esc_html(isset($settings['preplink_faq1_description']) ? $settings['preplink_faq1_description'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">You can modify the text/content or add new elements in your own way, but you should maintain the structure of the <strong>"div"</strong> element.</p>';
                    $html .= '<p class="description"><a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">References</a></p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
        if (isset($_POST['preplink_faq'])) {
            $settings = $_POST['preplink_faq'];
            update_option('preplink_faq', $settings);
        }
    }

    public function preplink_display_faq_2()
    {
        $settings = get_option('preplink_faq', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_faq2_enabled">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">Enable/Disable</label>
                    <select name="preplink_faq[preplink_faq2_enabled]" id="preplink_faq2_enabled">
                        <option value="1" <?php selected(isset($settings['preplink_faq2_enabled']) && $settings['preplink_faq2_enabled'] == '1'); ?>>Yes</option>
                        <option value="0" <?php selected(isset($settings['preplink_faq2_enabled']) && $settings['preplink_faq2_enabled'] == '0'); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_faq2_title">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">FAQ Title</label>
                    <input type="text" name="preplink_faq[preplink_faq2_title]" placeholder="Download FAQs" value="<?= esc_attr(isset($settings['preplink_faq2_title']) ? $settings['preplink_faq2_title'] : false); ?>"/>
                </td>
            </tr>
            <tr class="preplink_faq2_description">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">FAQ Description (HTML)</label>
                    <?php
                    $html = '<textarea name="preplink_faq[preplink_faq2_description]" rows="10" cols="70">';
                    $html .= esc_html(isset($settings['preplink_faq2_description']) ? $settings['preplink_faq2_description'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">You can modify the text/content or add new elements in your own way, but you should maintain the structure of the <strong>"div"</strong> element.</p>';
                    $html .= '<p class="description"><a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">References</a></p>';
                    echo $html;
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_related_post()
    {
        $settings = get_option('preplink_endpoint', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_related_enabled">
                <td style="padding: 2px 0;">
                    <select name="preplink_endpoint[preplink_related_post]" id="preplink_related_enabled"
                            class="preplink_related_post">
                        <option value="1" <?php selected(isset($settings['preplink_related_post']) && $settings['preplink_related_post'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_related_post']) && $settings['preplink_related_post'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_related_number">
                <td class="related_number" style="padding: 2px 0;">
                    <label><p>Number of posts displayed, default 10</p></label>
                    <input type="number" id="related_number" name="preplink_endpoint[preplink_related_number]" placeholder="10"
                           value="<?= isset($settings['preplink_related_number']) ? ($settings['preplink_related_number'] == '0' ? 0 : $settings['preplink_related_number']) : '' ?>"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_comment($args)
    {
        $settings = get_option('preplink_endpoint', array());
        $selected = isset($settings['preplink_comment']) ? $settings['preplink_comment'] : '1';
        $html = '<select id="preplink_comment" name="preplink_endpoint[preplink_comment]">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Enable or disable comments.</p>';
        echo $html;
    }

    public function preplink_custom_style()
    {
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_custom_style" cols="50" rows="5" name="preplink_setting[preplink_custom_style]">';
        $html .= isset($settings["preplink_custom_style"]) ? $settings["preplink_custom_style"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">Your CSS code, for example: .backgroud{background-color: transparent;}.</p>';
        echo $html;
    }

    public function preplink_display_mode()
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_wait_text">
                <td style="padding: 5px 0;">
                    <select name="preplink_setting[preplink_wait_text]" id="preplink_wait_text" class="preplink_related_post">
                        <option value="wait_time" <?php selected(isset($settings['preplink_wait_text']) && $settings['preplink_wait_text'] == 'wait_time'); ?>>
                            <?= __('Countdown')?>
                        </option>
                        <option value="progress" <?php selected(isset($settings['preplink_wait_text']) && $settings['preplink_wait_text'] == 'progress'); ?>>
                            <?= __('Progress')?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="wait_text_replace">
                <td style="padding: 5px 0;">
                    <input type="text" id="wait_text_replace" name="preplink_setting[wait_text_replace]" placeholder="waiting"
                           value="<?= esc_attr(!empty($settings['wait_text_replace']) ? $settings['wait_text_replace'] : false) ?>"/>
                    <p class="description">Text displayed while the countdown is pending.</p>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_post_auto_direct()
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_post_enabled">
                <td style="padding: 2px 0">
                    <select name="preplink_setting[preplink_auto_direct]" id="preplink_auto_direct" class="preplink_auto_direct">
                        <option value="1" <?php selected(isset($settings['preplink_auto_direct']) && $settings['preplink_auto_direct'] == '1'); ?>>Yes</option>
                        <option value="0" <?php selected(isset($settings['preplink_auto_direct']) && $settings['preplink_auto_direct'] == '0'); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_post_number">
                <td class="preplink_post_number_notice" style="padding: 2px 0">
                    <label><p>The default countdown time is set to 5 seconds.</p></label>
                    <input type="number" id="preplink_countdown" name="preplink_setting[preplink_countdown]" placeholder="5"
                           value="<?= isset($settings['preplink_countdown']) ? ($settings['preplink_countdown'] == '0' ? 0 : $settings['preplink_countdown']) : '' ?>"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }


    public function preplink_endpoint_auto_direct()
    {
        $settings = get_option('preplink_endpoint', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_endpoint_enabled">
                <td style="padding: 2px 0">
                    <select name="preplink_endpoint[endpoint_auto_direct]" id="endpoint_auto_direct" class="endpoint_auto_direct">
                        <option value="1" <?php selected(isset($settings['endpoint_auto_direct']) && $settings['endpoint_auto_direct'] == '1'); ?>>Yes</option>
                        <option value="0" <?php selected(isset($settings['endpoint_auto_direct']) && $settings['endpoint_auto_direct'] == '0'); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_endpoint_number">
                <td class="preplink_endpoint_number_notice" style="padding: 2px 0">
                    <label><p>The default countdown time is set to 5 seconds.</p></label>
                    <input type="number" id="countdown_endpoint" name="preplink_endpoint[countdown_endpoint]" placeholder="5"
                           value="<?= isset($settings['countdown_endpoint']) ? ($settings['countdown_endpoint'] == '0' ? 0 : $settings['countdown_endpoint']) : '' ?>"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_filed_advertising_1()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 1:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_8]" id="preplink_advertising_1">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_1']) && $settings['preplink_advertising_1'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_1']) && $settings['preplink_advertising_1'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_1]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_1']) ? $settings['preplink_advertising_code_1'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: Before featured image</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php

    }

    public function preplink_filed_advertising_2()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 2:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_1]" id="preplink_advertising_2">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_2']) && $settings['preplink_advertising_2'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_2']) && $settings['preplink_advertising_2'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_2]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_2']) ? $settings['preplink_advertising_code_2'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: After featured image.</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_filed_advertising_3()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 3:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_2]" id="preplink_advertising_3">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_3']) && $settings['preplink_advertising_3'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_3']) && $settings['preplink_advertising_3'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_3]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_3']) ? $settings['preplink_advertising_code_3'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: After post excerpt.</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_filed_advertising_4()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 4:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_4]" id="preplink_advertising_4">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_4']) && $settings['preplink_advertising_4'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_4']) && $settings['preplink_advertising_4'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_4]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_4']) ? $settings['preplink_advertising_code_4'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: After download button.</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_filed_advertising_5()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 5:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_5]" id="preplink_advertising_5">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_5']) && $settings['preplink_advertising_5'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_5']) && $settings['preplink_advertising_5'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_5]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_5']) ? $settings['preplink_advertising_code_5'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: FAQ Center.</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_filed_advertising_6()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 6:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_6]" id="preplink_advertising_6">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_6']) && $settings['preplink_advertising_6'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_6']) && $settings['preplink_advertising_6'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_6]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_6']) ? $settings['preplink_advertising_code_6'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: Before Progress button</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_filed_advertising_7()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 7:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_7]" id="preplink_advertising_7">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_7']) && $settings['preplink_advertising_7'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_7']) && $settings['preplink_advertising_7'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_7]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_7']) ? $settings['preplink_advertising_code_7'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: After related posts</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_filed_advertising_8()
    {
        $settings = get_option('preplink_advertising', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_advertising_enable">
                <th scope="row">Enable Advertising 8:</th>
                <td>
                    <select name="preplink_advertising[preplink_advertising_7]" id="preplink_advertising_8">
                        <option value="1" <?php selected(isset($settings['preplink_advertising_8']) && $settings['preplink_advertising_8'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['preplink_advertising_8']) && $settings['preplink_advertising_8'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_advertising_code">
                <th scope="row">Advertising HTML code:</th>
                <td>
                    <?php
                    $html = '<textarea name="preplink_advertising[preplink_advertising_code_8]" rows="5" cols="50">';
                    $html .= esc_html(isset($settings['preplink_advertising_code_8']) ? $settings['preplink_advertising_code_8'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Display position: After comments</p>';
                    echo $html;
                    ?></td>
            </tr>
            </tbody>
        </table>
        <?php
        if (isset($_POST['preplink_advertising'])) {
            $settings = $_POST['preplink_advertising'];
            update_option('preplink_advertising', $settings);
        }
    }

    public function preplink_delete_option_on_uninstall()
    {
        $settings = get_option('preplink_setting', array());
        $delete_option = isset( $settings['preplink_delete_option'] ) ? $settings['preplink_delete_option'] : false;
        echo '<input type="checkbox" name="preplink_setting[preplink_delete_option]" value="1" ' . checked( $delete_option, true, false ) . '/>';
    }
}
