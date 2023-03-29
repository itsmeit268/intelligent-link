<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
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
        add_action('admin_menu', array($this, 'addPluginAdminMenu'), 9);
        add_action('admin_init', array($this, 'registerAndBuildFields'));
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
            'preplink_enable_plugin', // ID của field
            __('Enable/Disable', 'preplink'),
            array($this, 'preplink_enable_plugin'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                1 => 'Enabled',
                0  => 'Disabled',
            )
        );

        add_settings_field(
            'preplink_endpoint',
            __('Endpoint', 'preplink'),
            array($this, 'preplink_endpoint_field'),
            'preplink_general_settings',
            'preplink_general_section');

        add_settings_field(
            'preplink_text_complete',
            __('Text Complete', 'preplink'),
            array($this, 'preplink_text_complete'),
            'preplink_general_settings',
            'preplink_general_section');

        add_settings_field(
            'preplink_countdown',
            __('Time countdown for post', 'preplink'),
            array($this, 'preplink_countdown_field'),
            'preplink_general_settings',
            'preplink_general_section');

        add_settings_field(
            'preplink_textarea',
            __('Links allowed', 'preplink'),
            array($this, 'preplink_textarea_field'),
            'preplink_general_settings',
            'preplink_general_section'
        );

        add_settings_field(
            'preplink_excludes_element',
            __('Element excluded', 'preplink'),
            array($this, 'preplink_excludes_element'),
            'preplink_general_settings',
            'preplink_general_section'
        );

        add_settings_field(
            'preplink_image', // ID của field
            __('Post Image', 'preplink'),
            array($this, 'preplink_image_field'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                1 => 'Yes',
                0  => 'No',
            )
        );

        add_settings_field(
            'preplink_excerpt', // ID của field
            __('Post Excerpt', 'preplink'),
            array($this, 'preplink_excerpt_field'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                1 => 'Yes',
                0  => 'No',
            )
        );

        add_settings_field(
            'preplink_excerpt', // ID của field
            __('Post Excerpt', 'preplink'),
            array($this, 'preplink_excerpt_field'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                1 => 'Yes',
                0  => 'No',
            )
        );

        add_settings_field(
            'preplink_faq_1',
            __('Disable/Enable FAQ 1', 'preplink'),
            array($this, 'preplink_display_faq_1'),
            'preplink_general_settings',
            'preplink_general_section',
            array('label_for' => 'preplink_faq')
        );

        add_settings_field(
            'preplink_faq_2',
            __('Disable/Enable FAQ 2', 'preplink'),
            array($this, 'preplink_display_faq_2'),
            'preplink_general_settings',
            'preplink_general_section',
            array('label_for' => 'preplink_faq_2')
        );

        add_settings_field(
            'preplink_related_post', // ID của field
            __('Post Related', 'preplink'),
            array($this, 'preplink_related_post'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                1 => 'Yes',
                0  => 'No',
            )
        );

        add_settings_field(
            'preplink_comment', // ID của field
            __('Comment', 'preplink'),
            array($this, 'preplink_comment'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                1 => 'Yes',
                0  => 'No',
            )
        );

        add_settings_field(
            'preplink_display_mode', // ID của field
            __('Display mode', 'preplink'),
            array($this, 'preplink_display_mode'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                'wait_time' => 'Countdown',
                'progress'  => 'ProgressBar',
            )
        );

        add_settings_field(
            'preplink_auto_direct', // ID của field
            __('Auto-redirect', 'preplink'),
            array($this, 'preplink_post_auto_direct'),
            'preplink_general_settings', // ID của page
            'preplink_general_section', // ID của section
            array( // Mảng các thông số truyền vào callback function
                1 => 'Yes',
                0  => 'No',
            )
        );

        add_settings_field(
            'preplink_wait_text',
            __('Replace waiting text', 'preplink'),
            array($this, 'preplink_wait_text'),
            'preplink_general_settings',
            'preplink_general_section');

        add_settings_field(
            'preplink_custom_style',
            __('Custom Style', 'preplink'),
            array($this, 'preplink_custom_style'),
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
            <h3>These settings are applicable to all Prepare link functionalities.</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank" >itsmeit.co</a> | <a href="//itsmeit.biz" target="_blank" >itsmeit.biz</a></span> |
            <span>Link download: <a href="https://github.com/itsmeit268/preplink" target="_blank">WordPress Preplink Plugin</a></span>
        </div>
        <?php
    }

    function preplink_enable_plugin($args)
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

    function preplink_endpoint_field()
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <input type="text" id="preplink_endpoint" name="preplink_setting[preplink_endpoint]" placeholder="download"
               value="<?= esc_attr(!empty($settings['preplink_endpoint']) ? $settings['preplink_endpoint'] : false) ?>" />
        <p class="description">The default endpoint is set to "download", so the link format will be: domain.com/post/download.</p>
        <p class="description" style="color: red">After you change the endpoint, you need to navigate to <strong>Settings->Permalinks->Save</strong> to sync the endpoint</p>
        <?php
    }

    function preplink_text_complete()
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <input type="text" id="preplink_text_complete" name="preplink_setting[preplink_text_complete]" placeholder="[Link ready!]"
               value="<?= esc_attr(!empty($settings['preplink_text_complete']) ? $settings['preplink_text_complete'] : false) ?>" />
        <p class="description">Text display after countdown complete. (default [Link ready!])</p>
        <?php
    }

    function preplink_countdown_field()
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <input type="text" id="preplink_countdown" name="preplink_setting[preplink_countdown]" placeholder="0"
               value="<?= esc_attr(!empty($settings['preplink_countdown']) ? $settings['preplink_countdown'] : false) ?>" />
        <p class="description">Countdown time, default 0.</p>
        <?php
    }

    public function preplink_textarea_field()
    {
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_url" cols="50" rows="5" name="preplink_setting[preplink_url]">';
        $html .= isset($settings["preplink_url"]) ? $settings["preplink_url"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">These links/URLs will be redirected to the endpoint (Prepare Link), each separated by a comma (,).</p>';
        $html .= '<p class="description">Default: drive.google.com, play.google.com.</p>';
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

    function preplink_image_field($args)
    {
        $settings = get_option('preplink_setting', array());
        $selected = isset($settings['preplink_image']) ? $settings['preplink_image'] : '1';
        $html = '<select id="preplink_image" name="preplink_setting[preplink_image]" class="preplink_image">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Enable or disable post featured image.</p>';
        echo $html;
    }

    function preplink_excerpt_field($args)
    {
        $settings = get_option('preplink_setting', array());
        $selected = isset($settings['preplink_excerpt']) ? $settings['preplink_excerpt'] : '1';
        $html = '<select id="preplink_excerpt" name="preplink_setting[preplink_excerpt]" class="preplink_excerpt">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Enable or disable post excerpt (post excerpt is HTML code and it will show when it is a table).</p>';
        echo $html;
    }

    function preplink_display_faq_1() {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_faq1_enabled">
                <th scope="row">Enable FAQ 1:</th>
                <td>
                    <select name="preplink_setting[preplink_faq1_enabled]" id="preplink_faq1_enabled">
                        <option value="1" <?php selected( isset( $settings['preplink_faq1_enabled'] ) && $settings['preplink_faq1_enabled'] == '1' ); ?>>Yes</option>
                        <option value="0" <?php selected( isset( $settings['preplink_faq1_enabled'] ) && $settings['preplink_faq1_enabled'] == '0' ); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_faq1_title">
                <th scope="row">FAQ Title:</th>
                <td>
                    <input type="text" name="preplink_setting[preplink_faq1_title]" placeholder="Notes before continuing"
                           value="<?= esc_attr(isset($settings['preplink_faq1_title']) ? $settings['preplink_faq1_title'] : false); ?>" />
                </td>
            </tr>
            <tr class="preplink_faq1_description">
                <th scope="row">FAQ HTML:</th>
                <td>
          <?php
          $html = '<textarea name="preplink_setting[preplink_faq1_description]" rows="5" cols="50">';
          $html .= esc_html(isset($settings['preplink_faq1_description']) ? $settings['preplink_faq1_description'] : false);
          $html .= '</textarea>';
          $html .= '<p class="description">You can modify the text/content or add new elements in your own way, but you should maintain the structure of the <strong>"div"</strong> element.</p>';
          $html .= '<p class="description">The file <strong>faq.html</strong> in the plugin directory should be referred to for reference.</p>';
          echo $html;
          ?></td></tr></tbody></table>
            <?php
            if ( isset( $_POST['preplink_setting'] ) ) {
                $settings = $_POST['preplink_setting'];
                update_option( 'preplink_setting', $settings );
            }
    }

    function preplink_display_faq_2() {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_faq2_enabled">
                <th scope="row">Enable FAQ 2:</th>
                <td>
                    <select name="preplink_setting[preplink_faq2_enabled]" id="preplink_faq2_enabled">
                        <option value="1" <?php selected( isset( $settings['preplink_faq2_enabled'] ) && $settings['preplink_faq2_enabled'] == '1' ); ?>>Yes</option>
                        <option value="0" <?php selected( isset( $settings['preplink_faq2_enabled'] ) && $settings['preplink_faq2_enabled'] == '0' ); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_faq2_title">
                <th scope="row">FAQ 2 Title:</th>
                <td>
                    <input type="text" name="preplink_setting[preplink_faq2_title]" placeholder="Download FAQs"
                           value="<?= esc_attr(isset($settings['preplink_faq2_title']) ? $settings['preplink_faq2_title'] : false); ?>" />
                </td>
            </tr>
            <tr class="preplink_faq2_description">
                <th scope="row">FAQ HTML:</th>
                <td>
           <?php
                $html = '<textarea name="preplink_setting[preplink_faq2_description]" rows="5" cols="50">';
                $html .= esc_html(isset($settings['preplink_faq2_description']) ? $settings['preplink_faq2_description'] : false);
                $html .= '</textarea>';
                $html .= '<p class="description">You can modify the text/content or add new elements in your own way, but you should maintain the structure of the <strong>"div"</strong> element.</p>';
                $html .= '<p class="description">The file <strong>faq.html</strong> in the plugin directory should be referred to for reference.</p>';
                echo $html;
            ?>
                </td>
               </tr>
            </tbody>
        </table>
        <?php
        if (isset( $_POST['preplink_setting'] ) ) {
            $settings = $_POST['preplink_setting'];
            update_option( 'preplink_setting', $settings );
        }
    }

    function preplink_related_post($args)
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_related_enabled">
                <th scope="row">Enable Related post:</th>
                <td>
                    <select name="preplink_setting[preplink_related_post]" id="preplink_related_enabled" class="preplink_related_post">
                        <option value="1" <?php selected( isset( $settings['preplink_related_post'] ) && $settings['preplink_related_post'] == '1' ); ?>>Yes</option>
                        <option value="0" <?php selected( isset( $settings['preplink_related_post'] ) && $settings['preplink_related_post'] == '0' ); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_related_number">
                <th scope="row">Number of related post:</th>
                <td>
                    <input type="text" name="preplink_setting[preplink_related_number]"
                           placeholder="10" value="<?= esc_attr(!empty($settings['preplink_related_number']) ? $settings['preplink_related_number'] : false); ?>" />
                </td>
            </tr>
            </tbody>
        </table>
        <?php
        if (isset( $_POST['preplink_setting'] ) ) {
            $settings = $_POST['preplink_setting'];
            update_option( 'preplink_setting', $settings );
        }
    }

    function preplink_comment($args)
    {
        $settings = get_option('preplink_setting', array());
        $selected = isset($settings['preplink_comment']) ? $settings['preplink_comment'] : '1';
        $html = '<select id="preplink_comment" name="preplink_setting[preplink_comment]">';
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

    function preplink_display_mode($args)
    {
        $settings = get_option('preplink_setting', array());
        $selected = isset($settings['preplink_display_mode']) ? $settings['preplink_display_mode'] : '1';
        $html = '<select id="preplink_display_mode" name="preplink_setting[preplink_display_mode]" class="preplink_display_mode">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Display countdown or progress bar on click and URL (Default: Countdown)</p>';
        echo $html;
    }

    function preplink_post_auto_direct($args)
    {
        $settings = get_option('preplink_setting', array());
        $selected = isset($settings['preplink_auto_direct']) ? $settings['preplink_auto_direct'] : '1';
        $html = '<select id="preplink_auto_direct" name="preplink_setting[preplink_auto_direct]" class="preplink_auto_direct">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Automatic direct link when countdown is complete</p>';
        echo $html;
    }

    function preplink_wait_text()
    {
        $settings = get_option('preplink_setting', array());
        ?>
        <input type="text" id="preplink_wait_text" name="preplink_setting[preplink_wait_text]" placeholder="waiting"
               value="<?= esc_attr(!empty($settings['preplink_wait_text']) ? $settings['preplink_wait_text'] : false) ?>" />
        <p class="description">Text displayed while the countdown is pending.</p>
        <?php
    }

}
