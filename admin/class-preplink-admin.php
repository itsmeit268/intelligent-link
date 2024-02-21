<?php

/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @package    Preplink
 * @subpackage Preplink/admin
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

class Preplink_Admin {

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
    public function __construct($plugin_name, $version){
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_menu', array($this, 'add_prep_link_admin_menu'), 10);
        add_action('admin_init', array($this, 'register_and_build_fields'));
        add_action('plugin_action_links_' . PREPLINK_PLUGIN_BASE, array($this, 'add_plugin_action_link'), 20);

        add_action('add_meta_boxes', array($this, 'add_html_field_content'), 22);
        add_action('save_post', array($this, 'save_html_field_content'), 20);
        add_action('before_delete_post', array($this, 'delete_links_filed'), 20, 1);
    }

    /**
     * Register the stylesheets for the admin area.
     *

     */
    public function enqueue_styles(){
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/preplink-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts(){
        wp_enqueue_script('preplink-admin', plugin_dir_url(__FILE__) . 'js/preplink-admin.js', array('wp-i18n'), $this->version, false);
    }

    public function add_prep_link_admin_menu(){

        add_menu_page(
            __('Intelligent Link Settings', 'prep-link'),
            __('Intelligent Link', 'prep-link'),
            'manage_options',
            $this->plugin_name . '-settings',
            [$this, 'prep_link_admin_form_settings'],
            'dashicons-admin-links',
            90
        );
    }

    public function prep_link_admin_form_settings(){
        // set active tab based on query parameter or default to 'general'
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

        // define tabs array
        $tabs = array(
            'general'   => __( 'General', 'prep-link' ),
            'advertising'  => __( 'Advertising', 'prep-link' ),
            'faq' => __( 'FAQ', 'prep-link' ),
            'endpoint' => __( 'Endpoint', 'prep-link' )
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
                settings_fields('ads_code_settings');
                do_settings_sections('ads_code_settings');
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
            'general'   => __( 'General Settings', 'prep-link' ),
            'ads_code'  => __( 'Advertising Settings', 'prep-link' ),
            'preplink_faq'  => __( 'FAQ Settings', 'preplink' ),
            'preplink_endpoint'  => __( 'Endpoint Settings', 'prep-link' )
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
    public function add_plugin_action_link($links){
        $setting_link = '<a href="' . esc_url(get_admin_url()) . 'admin.php?page=preplink-settings">' . __('Settings', 'prep-link') . '</a>';
        array_unshift($links, $setting_link);
        return $links;
    }

    public function register_and_build_fields(){
        add_settings_section(
            'preplink_general_section',
            '',
            array($this, 'preplink_display_general'),
            'preplink_general_settings'
        );

        add_settings_section(
            'ads_code_section',
            '',
            array($this, 'ads_code_display'),
            'ads_code_settings'
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
            'preplink_cookie_time',
            __('Link expiration time', 'prep-link'),
            array($this, 'preplink_cookie_time'),
            'preplink_endpoint_settings',
            'preplink_endpoint_section');

        add_settings_field(
            'preplink_template',
            __('Display mode', 'prep-link'),
            array($this, 'enpoint_display_mode'),
            'preplink_endpoint_settings',
            'preplink_endpoint_section',
            array(
                'default'    => __('Default', 'prep-link'),
                'countdown'  => __('Countdown', 'prep-link'),
            )
        );

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
            'pr_faq',
            __('FAQ Settings', 'prep-link'),
            array($this, 'pr_faq'),
            'preplink_faq_settings',
            'preplink_faq_section',
            array('label_for' => 'preplink_faq')
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
            'replace_text_complete',
            __('Replace text after complete', 'prep-link'),
            array($this, 'replace_text_complete'),
            'preplink_general_settings',
            'preplink_general_section',
            array(
                'yes' => 'Replace',
                'no' => 'No replace',
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
            'pr_ad_1',
            __('Ads code 1', 'prep-link'),
            array($this, 'pr_ad_1'),
            'ads_code_settings',
            'ads_code_section'
        );

        add_settings_field(
            'pr_ad_2',
            __('Ads code 2', 'prep-link'),
            array($this, 'pr_ad_2'),
            'ads_code_settings',
            'ads_code_section'
        );

        add_settings_field(
            'pr_ad_3',
            __('Ads code 3', 'prep-link'),
            array($this, 'pr_ad_3'),
            'ads_code_settings',
            'ads_code_section'
        );

        add_settings_field(
            'pr_ad_4',
            __('Ads code 4', 'prep-link'),
            array($this, 'pr_ad_4'),
            'ads_code_settings',
            'ads_code_section'
        );

        add_settings_field(
            'pr_ad_5',
            __('Ads code 5', 'prep-link'),
            array($this, 'pr_ad_5'),
            'ads_code_settings',
            'ads_code_section'
        );

        add_settings_field(
            'pr_ad_6',
            __('Ads code 6', 'prep-link'),
            array($this, 'pr_ad_6'),
            'ads_code_settings',
            'ads_code_section'
        );

        add_settings_field(
            'pr_ad_7',
            __('Ads code 7', 'prep-link'),
            array($this, 'pr_ad_7'),
            'ads_code_settings',
            'ads_code_section'
        );

        add_settings_field(
            'preplink_link_field_lists',
            __('Number link list', 'prep-link'),
            array($this, 'preplink_link_field_lists'),
            'preplink_general_settings',
            'preplink_general_section');

        add_settings_field(
            'preplink_link_url_rewriting',
            __('URL Rewriting', 'prep-link'),
            array($this, 'preplink_link_url_rewriting'),
            'preplink_general_settings',
            'preplink_general_section');

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
            'ads_code_settings',
            'ads_code'
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

    public function preplink_display_general(){
        ?>
        <div class="prep-link-admin-settings">
            <h3>These settings are applicable to all Intelligent Link functionalities.</h3>
            <span>Author  : buivanloi.2010@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a></span>
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Intelligent Link Plugin</a></span>
        </div>
        <?php
    }

    public function ads_code_display(){
        ?>
        <div class="prep-link-ads-settings">
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a></span> |
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Intelligent Link Plugin</a></span>
            <h3>Please enter your advertisement code, allowing HTML, JS, CSS.</h3>
        </div>
        <?php
    }

    public function preplink_faq_display(){
        ?>
        <div class="prep-link-faq-settings">
            <h3>You can add the FAQ HTML code here, it will apply to the page endpoint.</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Intelligent Link Plugin</a></span>
        </div>
        <?php
    }

    public function preplink_endpoint_display(){
        ?>
        <div class="prep-link-endpoint-settings">
            <h3>This setting will apply only to the endpoint page.</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html" target="_blank">WordPress Intelligent Link Plugin</a></span>
        </div>
        <?php
    }

    public function preplink_enable_plugin($args){
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

    public function preplink_endpoint_field(){
        $settings = get_option('preplink_endpoint', array());
        ?>
        <input type="text" id="endpoint" name="preplink_endpoint[endpoint]" placeholder="download"
               value="<?= esc_attr(!empty($settings['endpoint']) ? $settings['endpoint'] : false) ?>"/>
        <p class="description">The default endpoint for link format is set to "download", meaning the link format will be as follows: domain.com/post/download. You should use a string without accents and spaces.</p>
        <p class="description"><strong style="color: red">IMPORTANT</strong>: If you make any changes to the endpoint, you need to navigate to <strong style="color: red">Settings -> Permalinks -> Save</strong> to synchronize the endpoint.</p>
        <?php
        if (isset($_POST['preplink_endpoint'])) {
            $settings = $_POST['preplink_endpoint'];
            update_option('preplink_endpoint', $settings);
        }
    }

    public function replace_text_complete() {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_text_enable">
                <td style="padding: 5px 0;">
                    <select name="preplink_setting[replace_text_enable]" id="replace_text" class="replace_text_enable">
                        <option value="yes" <?php selected(isset($settings['replace_text_enable']) && $settings['replace_text_enable'] == 'yes'); ?>>
                            <?= __('Yes')?>
                        </option>
                        <option value="no" <?php selected(isset($settings['replace_text_enable']) && $settings['replace_text_enable'] == 'no'); ?>>
                            <?= __('No')?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="replace_text">
                <td style="padding: 5px 0;">
                    <input type="text" id="replace_text" name="preplink_setting[replace_text]" placeholder="link is ready"
                           value="<?= esc_attr(!empty($settings['replace_text']) ? $settings['replace_text'] : false) ?>"/>
                    <p class="description">The replacement text when the countdown is complete.</p>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function preplink_link_field_lists(){
        $settings = get_option('preplink_setting', array());
        ?>
        <input type="number" id="preplink_number_field_lists" name="preplink_setting[preplink_number_field_lists]" placeholder="5"
               value="<?= esc_attr(!empty($settings['preplink_number_field_lists']) ? $settings['preplink_number_field_lists'] : false) ?>"/>
        <p class="description">The number of related fields, you'll find it within the post or product. Here you can add different links.</p>
        <?php
    }

    public function preplink_link_url_rewriting(){
        $settings = get_option('preplink_setting', array());
        ?>
        <p><input type="text" name="preplink_setting[prefix]" value="<?= esc_attr(!empty($settings['prefix']) ? $settings['prefix'] : $this->generateRandomString(12)) ?>"/></p>
        <p><input type="text" name="preplink_setting[between]" value="<?= esc_attr(!empty($settings['between']) ? $settings['between'] : $this->generateRandomString(11)) ?>"/></p>
        <p><input type="text" name="preplink_setting[suffix]" value="<?= esc_attr(!empty($settings['suffix']) ? $settings['suffix'] : $this->generateRandomString(6)) ?>"/></p>
        <p class="description">Despite the URL being encoded, we additionally incorporate various strings for insertion into the URL. This practice serves a security purpose and renders it non-decodable.</p>
        <?php
    }

    function generateRandomString($length = 20) {
        $regex = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return preg_replace('/[^' . $regex . ']/', '', substr(str_shuffle(str_repeat($regex, ceil($length / strlen($regex)))), 0, $length));
    }

    public function preplink_cookie_time(){
        $settings = get_option('preplink_endpoint', array());
        ?>
        <input type="number" id="cookie_time" name="preplink_endpoint[cookie_time]" placeholder="5"
               value="<?= isset($settings['cookie_time']) ? ($settings['cookie_time'] == '0' ? 0 : $settings['cookie_time']) : '' ?>"/>
        <p class="description">Default link expiration time is 5 minutes</p>
        <?php
    }

    public function enpoint_display_mode($args){
        $settings = get_option('preplink_endpoint', array());
        $selected = isset($settings['ep_mode']) ? $settings['ep_mode'] : 'default';
        $html = '<select id="ep_mode" name="preplink_endpoint[ep_mode]" class="ep_mode">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        echo $html;
    }

    public function preplink_textarea_field(){
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_url" cols="50" rows="5" name="preplink_setting[preplink_url]" placeholder="domain1.com, domain2.com,">';
        $html .= isset($settings["preplink_url"]) ? $settings["preplink_url"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">Any links containing these specific strings will be redirected to the countdown page. 
                    Each link should be separated by a comma. <br>
                    Please be aware that these strings might match any text in your post, so you should provide the domain of the link to ensure accurate redirection.
                   </p>';
        echo $html;
    }

    public function preplink_excludes_element(){
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_excludes_element" cols="50" rows="5" name="preplink_setting[preplink_excludes_element]" placeholder=".prep-link-download-btn,.prep-link-btn">';
        $html .= isset($settings["preplink_excludes_element"]) ? $settings["preplink_excludes_element"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">The elements will be excluded, each separated by a comma (,).</p>';
        $html .= '<p class="description">For example: #prep-link-download-btn, .prep-link-download-btn.</p>';
        echo $html;
    }

    public function preplink_image_field($args){
        $settings = get_option('preplink_endpoint', array());
        $selected = isset($settings['preplink_image']) ? $settings['preplink_image'] : '1';
        $html = '<select id="preplink_image" name="preplink_endpoint[preplink_image]" class="preplink_image">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Allow displaying featured image.</p>';
        echo $html;
    }

    public function pr_faq(){
        $settings = get_option('preplink_faq', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="faq_enabled">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">Enable/Disable</label>
                    <select name="preplink_faq[faq_enabled]" id="faq_enabled">
                        <option value="1" <?php selected(isset($settings['faq_enabled']) && $settings['faq_enabled'] == '1'); ?>>
                            Yes
                        </option>
                        <option value="0" <?php selected(isset($settings['faq_enabled']) && $settings['faq_enabled'] == '0'); ?>>
                            No
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="faq_title">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">FAQ Title</label>
                    <input type="text" name="preplink_faq[faq_title]" placeholder="Notes before continuing" value="<?= esc_attr(isset($settings['faq_title']) ? $settings['faq_title'] : false); ?>"/>
                </td>
            </tr>
            <tr class="faq_description">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">FAQ Description (HTML)</label>
                    <?php
                    $html = '<textarea name="preplink_faq[faq_description]" rows="10" cols="70">';
                    $html .= esc_html(isset($settings['faq_description']) ? $settings['faq_description'] : false);
                    $html .= '</textarea>';
                    $html .= '<p class="description">Click on this <a href="' . plugin_dir_url(__DIR__) . 'faq.txt" target="_blank">link</a> to view the FAQ structure.</p>';
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

    public function preplink_related_post() {
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

    public function preplink_comment($args) {
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

    public function preplink_custom_style() {
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_custom_style" cols="50" rows="5" name="preplink_setting[preplink_custom_style]">';
        $html .= isset($settings["preplink_custom_style"]) ? $settings["preplink_custom_style"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">Your CSS code, for example: .backgroud{background-color: transparent;}.</p>';
        echo $html;
    }

    public function preplink_display_mode() {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_wait_text">
                <td style="padding: 5px 0;">
                    <select name="preplink_setting[preplink_wait_text]" id="countdown-select" class="preplink_related_post">
                        <option value="wait_time" <?php selected(isset($settings['preplink_wait_text']) && $settings['preplink_wait_text'] == 'wait_time'); ?>>
                            <?= __('Countdown')?>
                        </option>
                        <option value="progress" <?php selected(isset($settings['preplink_wait_text']) && $settings['preplink_wait_text'] == 'progress'); ?>>
                            <?= __('Progress')?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="countdown-select">
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

    public function preplink_post_auto_direct() {
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
    
    public function preplink_endpoint_auto_direct() {
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

    public function pr_ad_1(){
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_1]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_1']) ? $settings['ads_1'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">Display position: At the top of the page.</p>';
        echo $html;
    }
    public function pr_ad_2(){
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_2]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_2']) ? $settings['ads_2'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">Display position: Below the featured image.</p>';
        echo $html;
    }

    public function pr_ad_3() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_2]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_3']) ? $settings['ads_3'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">Display position: Below the download/countdown button.</p>';
        echo $html;
    }

    public function pr_ad_4() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_4]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_4']) ? $settings['ads_4'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">Display position: Below the FAQ, if the FAQ is enabled.</p>';
        echo $html;
    }

    public function pr_ad_5() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_5]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_5']) ? $settings['ads_5'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">Display position: Below custom text 2.</p>';
        echo $html;
    }

    public function pr_ad_6() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_6]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_6']) ? $settings['ads_6'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">Display position: Below related posts, if related posts are enabled.</p>';
        echo $html;
    }

    public function pr_ad_7() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_7]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_7']) ? $settings['ads_7'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">Display position: At the bottom of the page when the link expires.</p>';
        echo $html;
    }

    public function preplink_delete_option_on_uninstall() {
        $settings = get_option('preplink_setting', array());
        $delete_option = isset( $settings['preplink_delete_option'] ) ? $settings['preplink_delete_option'] : false;
        echo '<input type="checkbox" name="preplink_setting[preplink_delete_option]" value="1" ' . checked( $delete_option, true, false ) . '/>';
    }

    public function add_html_field_content() {
        add_meta_box( 'link_meta_box', __( 'Link Options (preplink)' ), array($this,'link_meta_box_callback'), 'post', 'side', 'default' );
    }

    public function link_meta_box_callback($post) {
        wp_nonce_field('link_field', 'link_field');
        ?>

        <h2 class="list-h3-title" style="font-size: 14px; padding: 7px 0px 0 0; margin-top: 15px; font-weight: 600; text-transform: none; border-bottom: 4px solid #4350a7; margin-bottom: 10px;max-width: 91%;">
            Link Details</h2>
        <div class="app-fields">
            <?php
            $fields = array(
                'file_format' => 'Format: APK/IPA/ZIP/RAR...',
                'require' => 'OS/FW: Windows/Wordpress/IOS...',
                'os_version' => 'OS/FW Version (Ex: 11, 11+)',
                'file_version' => 'File Version (Ex: 1.0.0)',
                'mod_feature' => 'MOD Feature (Ex: Unlocked Premium)',
                'link_no_login' => 'Link No Login (required)',
                'link_is_login' => 'Link Is Login (required)',
                'file_name' => 'File Name (required)',
                'file_size' => 'File Size (100MB)'
            );

            $field_count = count($fields);
            $fields_per_row = 3;
            $field_index = 0;

            foreach ($fields as $field_name => $field_label) {
                if ($field_index % $fields_per_row === 0) {
                    echo '<div class="app-row">';
                }

                $field_value = get_post_meta($post->ID, $field_name, true);

                if ($field_name == 'rate_star') {
                    $input_type = 'number';
                } else {
                    $input_type = 'text';
                }

                ?>
                <div class="app-field">
                    <label for="<?= $field_name; ?>"><?= $field_label; ?></label>
                    <input type="<?= $input_type; ?>" name="<?= $field_name; ?>" value="<?= esc_attr($field_value); ?>"/>
                </div>
                <?php

                $field_index++;

                if ($field_index % $fields_per_row === 0 || $field_index === $field_count) {
                    echo '</div>';
                }
            }
            ?>
        </div>

        <h2 class="list-h3-title" style="font-size: 14px; padding: 7px 0px 0 0; margin-top: 15px; font-weight: 600; text-transform: none; border-bottom: 4px solid #4350a7; margin-bottom: 10px;max-width: 91%;">
            Additional Link Information</h2>
        <div class="list-link-fields">
            <?php
            $list_field = [
                'file_name', 'link_no_login', 'link_is_login', 'size'
            ];
            $link_download_data = get_post_meta($post->ID, 'link-download-metabox', true);
            $settings = get_option('preplink_setting', array());
            $total = !empty($settings['preplink_number_field_lists'])? (int) $settings['preplink_number_field_lists'] : 5;

            for ($i = 1; $i <= $total; $i++) : ?>
                <?php
                $file_name = isset($link_download_data[$list_field[0] . '-' . $i]) ? $link_download_data[$list_field[0] . '-' . $i] : '';
                $link_no_login = isset($link_download_data[$list_field[1] . '-' . $i]) ? $link_download_data[$list_field[1] . '-' . $i] : '';
                $link_is_login = isset($link_download_data[$list_field[2] . '-' . $i]) ? $link_download_data[$list_field[2] . '-' . $i] : '';
                $size_value = isset($link_download_data[$list_field[3] . '-' . $i]) ? $link_download_data[$list_field[3] . '-' . $i] : '';
                ?>
                <div class="list-link-row-wrap">
                    <h3 class="list-h3-title"><?= 'Link ' . $i ?></h3>
                    <div class="list-link-row">
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[0] . '-' . $i); ?>">File Name (require):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[0] . '-' . $i); ?>" name="<?php echo esc_attr($list_field[0] . '-' . $i); ?>" value="<?php echo $file_name ? esc_attr($file_name) : ''; ?>" />
                        </div>
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[1] . '-' . $i); ?>">Link no login (require):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[1] . '-' . $i); ?>" name="<?php echo esc_attr($list_field[1] . '-' . $i); ?>" value="<?php echo $link_no_login ? esc_attr($link_no_login) : ''; ?>" />
                        </div>
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[2] . '-' . $i); ?>">Link is login (require):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[2] . '-' . $i); ?>" name="<?php echo esc_attr($list_field[2] . '-' . $i); ?>" value="<?php echo $link_is_login ? esc_attr($link_is_login) : ''; ?>" />
                        </div>
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[3] . '-' . $i); ?>">Size (ex: 100 GB):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[3] . '-' . $i); ?>" name="<?php echo esc_attr($list_field[3] . '-' . $i); ?>" value="<?php echo $size_value ? esc_attr($size_value) : ''; ?>" />
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <?php
    }

    public function save_html_field_content($post_id) {
        if (!isset($_POST['link_field']) || !wp_verify_nonce($_POST['link_field'], 'link_field')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'file_format',
            'require',
            'os_version',
            'file_version',
            'mod_feature',
            'link_no_login',
            'link_is_login',
            'file_name',
            'file_size'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        $list_link = [];

        $field_list = ['file_name', 'link_no_login', 'link_is_login', 'size'];
        $settings = get_option('preplink_setting', array());
        $total = (int) $settings['preplink_number_field_lists']? : 5;

        for ($i = 1; $i <= $total; $i++) {
            foreach ($field_list as $field_name) {
                $meta_key = $field_name . '-' . $i;

                if (isset($_POST[$meta_key])) {
                    $field_content = sanitize_text_field($_POST[$meta_key]);
                    $list_link[$meta_key] = $field_content;
                }
            }
        }
        update_post_meta($post_id, 'link-download-metabox', $list_link);
    }

    public function delete_links_filed($post_id) {
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        $link_fields = array(
            'file_format',
            'require',
            'os_version',
            'file_version',
            'mod_feature',
            'link_no_login',
            'link_is_login',
            'file_name',
            'file_size',
        );

        foreach ($link_fields as $field) {
            delete_post_meta($post_id, $field);
        }
        
        delete_post_meta($post_id, 'link-download-metabox');
    }

}
