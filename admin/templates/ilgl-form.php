<?php

defined('ABSPATH') || exit;

class Form_Html {

    public static function render_tabs($tabs, $active_tab) {
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $active_tab) ? ' nav-tab-active' : '';
            echo '<a class="nav-tab' . $class . '" href="?page=intelligent-link-settings&tab=' . $tab . '">' . $name . '</a>';
        }
        echo '</h2>';
    }

    public static function render_general_settings() {
        echo '<div class="wrap"><h1>' . __('General Settings', 'intelligent-link') . '</h1>';
        settings_errors();
        echo '<form method="post" action="options.php">';
        settings_fields('preplink_general_settings');
        do_settings_sections('preplink_general_settings');
        submit_button();
        echo '</form></div>';
    }

    public static function render_meta_attr_settings() {
        echo '<div class="wrap"><h1>' . __('Post links settings', 'intelligent-link') . '</h1>';
        settings_errors();
        echo '<form method="post" action="options.php">';
        settings_fields('preplink_meta_attr');
        do_settings_sections('preplink_meta_attr');
        submit_button();
        echo '</form></div>';
    }

    public static function render_advertising_settings() {
        echo '<div class="wrap"><h1>' . __('Advertising Settings', 'intelligent-link') . '</h1>';
        settings_errors();
        echo '<form method="post" action="options.php">';
        settings_fields('ads_code_settings');
        do_settings_sections('ads_code_settings');
        submit_button();
        echo '</form></div>';
    }

    public static function render_faq_settings() {
        echo '<div class="wrap"><h1>' . __('FAQ Settings', 'intelligent-link') . '</h1>';
        settings_errors();
        echo '<form method="post" action="options.php">';
        settings_fields('preplink_faq_settings');
        do_settings_sections('preplink_faq_settings');
        submit_button();
        echo '</form></div>';
    }

    public static function render_endpoint_settings() {
        echo '<div class="wrap"><h1>' . __('Endpoint Settings', 'intelligent-link') . '</h1>';
        settings_errors();
        echo '<form method="post" action="options.php">';
        settings_fields('preplink_endpoint_settings');
        do_settings_sections('preplink_endpoint_settings');
        submit_button();
        echo '</form></div>';
    }

    // Section display methods
    public static function preplink_display_general(){
        ?>
        <div class="prep-link-admin-settings">
            <h3><?= __('These settings are applicable to all Intelligent Link functionalities.', 'intelligent-link')?></h3>
        </div>
        <?php
    }

    public static function preplink_meta_display(){
        ?>
        <div class="meta-attr-display">
            <h3><?= __('This section will allow adding meta attributes such as link, link information, size, etc., for post or product.', 'intelligent-link') ?></h3>
        </div>
        <?php
    }

    public static function ads_code_display(){
        ?>
        <div class="prep-link-ads-settings">
            <h3><?= __('Please enter your advertisement code, allowing HTML, JS, CSS.', 'intelligent-link')?></h3>
        </div>
        <?php
    }

    public static function preplink_faq_display(){
        ?>
        <div class="prep-link-faq-settings">
            <h3><?= __('You can add the FAQ HTML code here, it will apply to the page endpoint.', 'intelligent-link')?></h3>
        </div>
        <?php
    }

    public static function preplink_endpoint_display(){
        ?>
        <div class="prep-link-endpoint-settings">
            <h3><?= __('This setting will apply only to the endpoint page.', 'intelligent-link')?></h3>
        </div>
        <?php
    }

    // General Settings Fields
    public static function preplink_enable_plugin($args){
        $settings = get_option('preplink_setting', array());
        $selected = isset($settings['preplink_enable_plugin']) ? $settings['preplink_enable_plugin'] : '1';
        $html = '<select id="preplink_enable_plugin" name="preplink_setting[preplink_enable_plugin]" class="preplink_enable_plugin">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        echo $html;
    }

    public static function preplink_textarea_field(){
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_url" cols="50" rows="5" name="preplink_setting[preplink_url]" placeholder="domain1.com, domain2.com, sub.domain.com">';
        $html .= isset($settings["preplink_url"]) ? $settings["preplink_url"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">' . __('Domain or subdomain, or domain string, separated by commas (,).', 'intelligent-link') . '</p>';
        echo $html;
    }

    public static function preplink_excludes_element(){
        $settings = get_option('preplink_setting', array());
        $html = '<textarea id="preplink_excludes_element" cols="50" rows="5" name="preplink_setting[preplink_excludes_element]" placeholder="#selector, .selector">';
        $html .= isset($settings["preplink_excludes_element"]) ? $settings["preplink_excludes_element"] : false;
        $html .= '</textarea>';
        $html .= '<p class="description">'.esc_html__('The class or ID of the a tag will be preserved; each item is separated by a comma.', 'intelligent-link').'</p>';
        $html .= '<p class="description">'.__('For example: #selector, .selector', 'intelligent-link').'</p>';
        echo $html;
    }

    public static function preplink_display_mode() {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_wait_text">
                <td style="padding: 5px 0;">
                    <select name="preplink_setting[preplink_wait_text]" id="countdown-select" class="preplink_related_post">
                        <option value="wait_time" <?php selected(!empty($settings['preplink_wait_text']) && $settings['preplink_wait_text'] == 'wait_time'); ?>>
                            <?= __('Countdown')?>
                        </option>
                        <option value="progress" <?php selected(!empty($settings['preplink_wait_text']) && $settings['preplink_wait_text'] == 'progress'); ?>>
                            <?= __('Progress')?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="countdown-select">
                <td style="padding: 5px 0;">
                    <input type="text" id="wait_text_replace" name="preplink_setting[wait_text_replace]" placeholder="waiting"
                           value="<?= esc_attr(!empty($settings['wait_text_replace']) ? $settings['wait_text_replace'] : 'please wait') ?>"/>
                    <p class="description"><?= __('Text displayed while the countdown is pending.', 'intelligent-link')?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public static function replace_text_complete() {
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
                    <p class="description"><?= __('The replacement text when the countdown is complete.', 'intelligent-link')?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public static function preplink_post_auto_direct() {
        $settings = get_option('preplink_setting', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="preplink_auto_direct">
                <td style="padding: 2px 0">
                    <select name="preplink_setting[preplink_auto_direct]">
                        <option value="1" <?php selected(!empty($settings['preplink_auto_direct']) ? ($settings['preplink_auto_direct'] == '1') : false); ?>>Yes</option>
                        <option value="0" <?php selected(!empty($settings['preplink_auto_direct']) ? ($settings['preplink_auto_direct'] == '0') : true); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_post_number">
                <td class="preplink_post_number_notice" style="padding: 2px 0">
                    <label><p><?= __('The default countdown time is set to 1 second. If you set it to 0, it will bypass the automatic redirection configuration.', 'intelligent-link')?></p></label>
                    <input type="number" id="preplink_countdown" name="preplink_setting[preplink_countdown]" placeholder="1"
                           value="<?= !empty($settings['preplink_countdown']) ? ($settings['preplink_countdown'] == '0' ? 0 : $settings['preplink_countdown']) : '1' ?>" min="0" max="300"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public static function preplink_link_url_rewriting() {
        $settings = get_option('preplink_setting', array());
        ?>
        <select name="preplink_setting[enable_rewrite]" id="preplink_enable_rewrite">
            <option value="yes" <?= (!isset($settings['enable_rewrite']) || $settings['enable_rewrite'] === 'yes') ? 'selected' : '' ?>><?= __('Yes', 'intelligent-link') ?></option>
            <option value="no" <?= isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'no' ? 'selected' : '' ?>><?= __('No', 'intelligent-link') ?></option>
        </select>

        <div class="preplink-rewrite-fields">
            <p>Key</p>
            <input type="text" name="preplink_setting[key]"
                   value="<?= esc_attr(!empty($settings['key']) ? $settings['key'] : self::generateRandomString(32)) ?>"
                   placeholder="Key (length 32)" style="width: 100%; max-width: 400px; margin-bottom: 10px" maxlength="32"/>
            <br>
            <p>IV</p>
            <input type="text" name="preplink_setting[iv]"
                   value="<?= esc_attr(!empty($settings['iv']) ? $settings['iv'] : self::generateRandomString(16)) ?>"
                   placeholder="IV (length 16)" style="width: 100%; max-width: 400px;" maxlength="16"/>

            <p>Hide URL text</p>
            <input type="text" id="hide_url_text" name="preplink_setting[hide_url_text]"
                   placeholder="[Link]"
                   value="<?= esc_attr( !empty($settings['hide_url_text']) ? $settings['hide_url_text'] : '[Link]')?>"
                   style="width: 100%; max-width: 400px;" />
            <p class="description">Text to display when the original link text is a URL. This helps hide direct URLs in the interface.</p>
        </div>
        <?php
    }

    public static function generateRandomString($length = 20) {
        $regex = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return preg_replace('/[^' . $regex . ']/', '', substr(str_shuffle(str_repeat($regex, ceil($length / strlen($regex)))), 0, $length));
    }

    public static function preplink_delete_option_on_uninstall() {
        $settings = get_option('preplink_setting', array());
        $delete_option = isset( $settings['preplink_delete_option'] ) ? $settings['preplink_delete_option'] : false;
        echo '<input type="checkbox" name="preplink_setting[preplink_delete_option]" value="1" ' . checked( $delete_option, true, false ) . '/>';
    }

    // Meta Attribute Fields
    public static function meta_attr_auto_direct() {
        $meta_attr = get_option('meta_attr', []);
        ?>
        <table class="form-table">
            <tbody>
            <tr class="meta_attr_auto_direct">
                <td style="padding: 2px 0">
                    <select name="meta_attr[auto_direct]">
                        <option value="1" <?php selected(!empty($meta_attr['auto_direct']) ? ($meta_attr['auto_direct'] == '1') : false); ?>>Yes</option>
                        <option value="0" <?php selected(!empty($meta_attr['auto_direct']) ? ($meta_attr['auto_direct'] == '0') : true); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="tr-time_number">
                <td class="td-time_number" style="padding: 2px 0">
                    <label><p><?= __('The default countdown time is set to 1 second. If you set it to 0, it will bypass the automatic redirection configuration.', 'intelligent-link')?></p></label>
                    <input type="number" name="meta_attr[time]" placeholder="1" value="<?= !empty($meta_attr['time']) ? ($meta_attr['time'] == '0' ? 0 : $meta_attr['time']) : '1' ?>" min="0" max="300"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public static function preplink_link_field_lists(){
        $settings = get_option('meta_attr', array());
        ?>
        <p>Title</p>
        <input type="text" name="meta_attr[list_title]" placeholder="Other Version"
               value="<?= esc_attr(!empty($settings['list_title']) ? $settings['list_title'] : '') ?>" />
        <br><br>

        <input type="number" name="meta_attr[field_lists]" placeholder="3"
               value="<?= esc_attr(!empty($settings['field_lists']) ? $settings['field_lists'] : '3') ?>" min="1" max="20"/>
        <p class="description"><?= __("The number of related fields, you'll find it within the post or product. Here you can add different links.", "intelligent-link")?></p>
        <?php
    }

    public static function meta_elm_option() {
        $meta_attr = get_option('meta_attr', []); ?>
        <select name="meta_attr[elm]">
            <option value="div" <?= isset($meta_attr['elm']) && $meta_attr['elm'] === 'div' ? 'selected' : '' ?>>div</option>
            <option value="h2" <?= isset($meta_attr['elm']) && $meta_attr['elm'] === 'h2' ? 'selected' : '' ?>>h2</option>
            <option value="h3" <?= (!isset($meta_attr['elm']) || $meta_attr['elm'] === 'h3') ? 'selected' : '' ?>>h3</option>
            <option value="h4" <?= isset($meta_attr['elm']) && $meta_attr['elm'] === 'h4' ? 'selected' : '' ?>>h4</option>
            <option value="h5" <?= isset($meta_attr['elm']) && $meta_attr['elm'] === 'h5' ? 'selected' : '' ?>>h5</option>
        </select>
        <input type="text" name="meta_attr[pre_fix]" placeholder="Link download:" value="<?= esc_attr(!empty($meta_attr['pre_fix']) ? $meta_attr['pre_fix'] : 'Link download:') ?>"/>
    <?php }

    public static function product_elm_option() {
        $meta_attr = get_option('meta_attr', []); ?>
        <select name="meta_attr[product_elm]">
            <option value="after_product_content" <?= isset($meta_attr['product_elm']) && $meta_attr['product_elm'] === 'after_product_content' ? 'selected' : '' ?>><?= __('After product content (Description)', 'intelligent-link')?></option>
            <option value="after_short_description" <?= isset($meta_attr['product_elm']) && $meta_attr['product_elm'] === 'after_short_description' ? 'selected' : '' ?>><?= __('Short description below', 'intelligent-link')?></option>
        </select>
    <?php }

    // Endpoint Settings Fields
    public static function preplink_endpoint_field(){
        $settings = get_option('preplink_endpoint', array());
        $endpoint = !empty($settings['endpoint'])? $settings['endpoint']: '';
        $endpoint = preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim($endpoint));
        ?>
        <input type="text" id="endpoint" name="preplink_endpoint[endpoint]" placeholder="link" value="<?= esc_attr($endpoint ? : false) ?>"/>
        <p class="description"><?= __('The default endpoint is (1), and it looks like this:', 'intelligent-link')?> <?= get_bloginfo('url')?>/hello-world/?<?= $endpoint ? : 'link'?>=1</p>
        <?php
        if (isset($_POST['preplink_endpoint'])) {
            $settings = $_POST['preplink_endpoint'];
            update_option('preplink_endpoint', $settings);
        }
    }

    public static function preplink_cookie_time(){
        $settings = get_option('preplink_endpoint', array());
        ?>
        <input type="number" id="cookie_time" name="preplink_endpoint[cookie_time]" placeholder="5"
               value="<?= isset($settings['cookie_time']) ? ($settings['cookie_time'] == '0' ? 0 : $settings['cookie_time']) : '5' ?>" min="1" max="600"/>
        <p class="description"><?= __('The default expiration is 5 minutes. After that, the user must interact again to get the link.', 'intelligent-link')?></p>
        <?php
    }

    public static function enpoint_display_mode(){
        $settings = get_option('preplink_endpoint', array());
        ?>
        <select name="preplink_endpoint[ep_mode]">
            <option value="default" <?php selected(isset($settings['ep_mode']) && $settings['ep_mode'] == 'default'); ?>>Default</option>
            <option value="countdown" <?php selected(isset($settings['ep_mode']) && $settings['ep_mode'] == 'countdown'); ?>>Countdown</option>
        </select>
    <?php }

    public static function preplink_endpoint_auto_direct() {
        $settings = get_option('preplink_endpoint', array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="auto_direct">
                <td style="padding: 2px 0">
                    <select name="preplink_endpoint[endpoint_auto_direct]" id="endpoint_auto_direct" class="endpoint_auto_direct">
                        <option value="1" <?php selected(!empty($settings['endpoint_auto_direct']) ? ($settings['endpoint_auto_direct'] == '1') : false); ?>>Yes</option>
                        <option value="0" <?php selected(!empty($settings['endpoint_auto_direct']) ? ($settings['endpoint_auto_direct'] == '0') : true); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="preplink_endpoint_number">
                <td class="preplink_endpoint_number_notice" style="padding: 2px 0">
                    <label><p><?= __('The default countdown time is set to 15 seconds.', 'intelligent-link')?></p></label>
                    <input type="number" id="countdown_endpoint" name="preplink_endpoint[countdown_endpoint]" placeholder="15"
                           value="<?= !empty($settings['countdown_endpoint']) ? $settings['countdown_endpoint'] : '15' ?>" min="1" max="300"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public static function preplink_image_field($args){
        $settings = get_option('preplink_endpoint', array());
        $selected = isset($settings['preplink_image']) ? $settings['preplink_image'] : '1';
        $html = '<select id="preplink_image" name="preplink_endpoint[preplink_image]" class="preplink_image">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        echo $html;
    }

    public static function preplink_related_post() {
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
                           value="<?= !empty($settings['preplink_related_number']) ? ($settings['preplink_related_number'] == '0' ? 0 : $settings['preplink_related_number']) : '' ?>" min="1" max="50"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public static function preplink_comment() {
        $settings = get_option('preplink_endpoint',[]);
        ?>
        <select name="preplink_endpoint[preplink_comment]">
            <option value="1" <?php selected(isset($settings['preplink_comment']) && $settings['preplink_comment'] == '1'); ?>>Yes</option>
            <option value="0" <?php selected(isset($settings['preplink_comment']) && $settings['preplink_comment'] == '0'); ?>>No</option>
        </select>
    <?php }

    public static function redirect_notice(){
        $settings = get_option('preplink_endpoint', []);
        $example = 'You are being redirected to a link outside of '.str_replace(['https://', 'http://'], '', get_bloginfo('url')) .'. Please click the button below to continue, or press the back arrow to return to the previous page.';
        $html = '<textarea cols="50" rows="5" name="preplink_endpoint[redirect_notice]">';
        $html .= isset($settings["redirect_notice"]) ? $settings["redirect_notice"] : $example;
        $html .= '</textarea>';
        $html .= '<p class="description">'.__('The notification prior to users being redirected to an external link.', 'intelligent-link').'</p>';
        echo $html;
    }

    // FAQ Settings Fields
    public static function pr_faq(){
        $settings = get_option('preplink_faq', []);
        ?>
        <table class="form-table">
            <tbody>
            <tr class="faq_enabled">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">FAQ</label>
                    <select name="preplink_faq[faq_enabled]" id="faq_enabled">
                        <option value="0" <?php selected(isset($settings['faq_enabled']) && $settings['faq_enabled'] == '0'); ?>>Disabled</option>
                        <option value="1" <?php selected(isset($settings['faq_enabled']) && $settings['faq_enabled'] == '1'); ?>>Enabled</option>
                    </select>
                </td>
            </tr>
            <tr class="faq_title">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;">Title</label>
                    <input type="text" name="preplink_faq[faq_title]" placeholder="Notes before continuing" value="<?= esc_attr(isset($settings['faq_title']) ? $settings['faq_title'] : 'Frequently Asked Questions'); ?>"/>
                </td>
            </tr>
            <tr class="faq_description">
                <td style="padding: 5px 0;">
                    <label style="width: 160px;display: inline-table;"><?= __('Description (HTML)', 'intelligent-link')?></label>
                    <?php
                    $faq_content = !empty($settings['faq_description']) ? esc_html($settings['faq_description']) : file_get_contents(plugin_dir_path(__DIR__) . 'faq.txt');
                    echo '<textarea name="preplink_faq[faq_description]" rows="10" cols="70">' . $faq_content . '</textarea>';
                    ?>
                    <p class="description"><?= __('Click on this', 'intelligent-link')?> <a href="<?php echo plugin_dir_url(__DIR__) . 'faq.txt'; ?>" target="_blank">link</a> <?= __('to view the FAQ structure.', 'intelligent-link')?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
        if (isset($_POST['preplink_faq'])) {
            $settings = $_POST['preplink_faq'];
            update_option('preplink_faq', $settings);
        }
    }

    // Advertising Fields
    public static function pr_ad_1(){
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_1]" rows="5" cols="50">';
        $html .= esc_html(!empty($settings['ads_1']) ? $settings['ads_1'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">'.__('Display position: At the top of the page.', 'intelligent-link').'</p>';
        echo $html;
    }

    public static function pr_ad_2(){
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_2]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_2']) ? $settings['ads_2'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">'.__('Display position: Below the featured image.', 'intelligent-link').'</p>';
        echo $html;
    }

    public static function pr_ad_3() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_3]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_3']) ? $settings['ads_3'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">'.__('Display position: Below the download/countdown button.', 'intelligent-link').'</p>';
        echo $html;
    }

    public static function pr_ad_4() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_4]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_4']) ? $settings['ads_4'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">'.__('Display position: Below the FAQ, if the FAQ is enabled.', 'intelligent-link').'</p>';
        echo $html;
    }

    public static function pr_ad_5() {
        $settings = get_option('ads_code', array());
        $html = '<textarea name="ads_code[ads_5]" rows="5" cols="50">';
        $html .= esc_html(isset($settings['ads_5']) ? $settings['ads_5'] : false);
        $html .= '</textarea>';
        $html .= '<p class="description">'.__('Display position: Display when session expires.', 'intelligent-link').'</p>';
        echo $html;
    }

    // Meta Box Template
    public static function link_meta_box_callback($post) {
        wp_nonce_field('link_field', 'link_field');

        $settings = get_option('meta_attr', array());
        $max_filed = !empty($settings['field_lists'])? (int) $settings['field_lists'] : 5;
        ?>

        <?php do_action('link_field_meta_box_before', $post); ?>

        <h2 class="list-h3-title">Link Details</h2>
        <div class="app-fields">
            <?php
            $fields = array(
                    'file_name' => 'File Name (required)',
                    'file_size' => 'File Size (Ex: 100MB)',
                    'link_no_login' => 'Link No Login (required)',
                    'link_is_login' => 'Link Is Login (required)',
                    'file_format' => 'Format: APK/IPA/ZIP/RAR...',
                    'require' => 'OS/FW: Windows/Wordpress/IOS...',
                    'os_version' => 'OS/FW Version (Ex: 11, 11+)',
                    'file_version' => 'File Version (Ex: 1.0.0)',
                    'mod_feature' => 'MOD Feature (Ex: Unlocked Premium)'
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

        <h2 class="list-h3-title">Additional Link Information</h2>

        <div class="list-link-fields" id="link-fields-container">
            <?php
            $list_field = ['file_name', 'link_no_login', 'link_is_login', 'size'];
            $link_download_data = get_post_meta($post->ID, 'link-download-metabox', true);

            // Tính toán chính xác số field đã có dữ liệu
            $saved_fields = 0;
            $field_has_data = []; // Lưu trạng thái field nào có data

            if (!empty($link_download_data)) {
                // Tìm tất cả field có dữ liệu
                for ($i = 1; $i <= $max_filed; $i++) {
                    $has_any_data = false;
                    foreach ($list_field as $field_name) {
                        $meta_key = $field_name . '-' . $i;
                        if (isset($link_download_data[$meta_key]) && !empty(trim($link_download_data[$meta_key]))) {
                            $has_any_data = true;
                            break;
                        }
                    }

                    if ($has_any_data) {
                        $saved_fields = $i;
                        $field_has_data[$i] = true;
                    } else {
                        $field_has_data[$i] = false;
                    }
                }
            }

            // Đảm bảo ít nhất 1 field và không vượt quá MAX
            $fields_to_render = max(1, $saved_fields);
            $fields_to_render = min($fields_to_render, $max_filed);

            // Hiển thị các field
            for ($i = 1; $i <= $fields_to_render; $i++) :
                $file_name = isset($link_download_data[$list_field[0] . '-' . $i]) ? $link_download_data[$list_field[0] . '-' . $i] : '';
                $link_no_login = isset($link_download_data[$list_field[1] . '-' . $i]) ? $link_download_data[$list_field[1] . '-' . $i] : '';
                $link_is_login = isset($link_download_data[$list_field[2] . '-' . $i]) ? $link_download_data[$list_field[2] . '-' . $i] : '';
                $size_value = isset($link_download_data[$list_field[3] . '-' . $i]) ? $link_download_data[$list_field[3] . '-' . $i] : '';

                // Kiểm tra field này có data không
                $current_field_has_data = !empty($file_name) || !empty($link_no_login) || !empty($link_is_login) || !empty($size_value);
                ?>
                <div class="list-link-row-wrap" data-field-index="<?= $i ?>" data-has-data="<?= $current_field_has_data ? 'true' : 'false' ?>">
                    <h3 class="list-h3-title">
                        Link <?= $i ?>
                        <?php if ($i > 1): ?>
                            <button type="button" class="button button-link remove-link-field">❌</button>
                        <?php endif; ?>
                    </h3>
                    <div class="list-link-row">
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[0] . '-' . $i); ?>">File Name (require):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[0] . '-' . $i); ?>"
                                   name="<?php echo esc_attr($list_field[0] . '-' . $i); ?>"
                                   value="<?php echo $file_name ? esc_attr($file_name) : ''; ?>" />
                        </div>
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[1] . '-' . $i); ?>">Link no login (require):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[1] . '-' . $i); ?>"
                                   name="<?php echo esc_attr($list_field[1] . '-' . $i); ?>"
                                   value="<?php echo $link_no_login ? esc_attr($link_no_login) : ''; ?>" />
                        </div>
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[2] . '-' . $i); ?>">Link is login (require):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[2] . '-' . $i); ?>"
                                   name="<?php echo esc_attr($list_field[2] . '-' . $i); ?>"
                                   value="<?php echo $link_is_login ? esc_attr($link_is_login) : ''; ?>" />
                        </div>
                        <div class="link-field">
                            <label for="<?php echo esc_attr($list_field[3] . '-' . $i); ?>">Size (ex: 100 GB):</label>
                            <input type="text" id="<?php echo esc_attr($list_field[3] . '-' . $i); ?>"
                                   name="<?php echo esc_attr($list_field[3] . '-' . $i); ?>"
                                   value="<?php echo $size_value ? esc_attr($size_value) : ''; ?>" />
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Button Add Field -->
        <div style="margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ccd0d4; border-radius: 4px;">
            <button type="button" id="add-link-field" class="button button-secondary">
                + Add Link Field
            </button>
            <span id="link-field-counter" style="margin-left: 10px; color: #666; font-size: 12px;">
            <?= $fields_to_render ?>/<?= $max_filed ?> fields
        </span>
        </div>

        <!-- Template cho field mới (hidden) -->
        <div id="link-field-template" style="display: none;">
            <div class="list-link-row-wrap" data-field-index="__INDEX__" data-has-data="false">
                <h3 class="list-h3-title">
                    Link __INDEX__
                    <button type="button" class="button button-link remove-link-field">❌</button>
                </h3>
                <div class="list-link-row">
                    <div class="link-field">
                        <label for="file_name-__INDEX__">File Name (require):</label>
                        <input type="text" id="file_name-__INDEX__" name="file_name-__INDEX__" value="" />
                    </div>
                    <div class="link-field">
                        <label for="link_no_login-__INDEX__">Link no login (require):</label>
                        <input type="text" id="link_no_login-__INDEX__" name="link_no_login-__INDEX__" value="" />
                    </div>
                    <div class="link-field">
                        <label for="link_is_login-__INDEX__">Link is login (require):</label>
                        <input type="text" id="link_is_login-__INDEX__" name="link_is_login-__INDEX__" value="" />
                    </div>
                    <div class="link-field">
                        <label for="size-__INDEX__">Size (ex: 100 GB):</label>
                        <input type="text" id="size-__INDEX__" name="size-__INDEX__" value="" />
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                const MAX_FIELDS = <?= $max_filed ?>;

                // Luôn tính currentFieldCount từ thực tế
                function getCurrentFieldCount() {
                    return $('#link-fields-container .list-link-row-wrap').length;
                }

                function updateCounter() {
                    const count = getCurrentFieldCount();
                    $('#link-field-counter').text(count + '/' + MAX_FIELDS + ' fields');
                    $('#add-link-field').prop('disabled', count >= MAX_FIELDS);
                }

                // Thêm field mới - CÁCH ĐƠN GIẢN NHẤT
                $('#add-link-field').on('click', function() {
                    const currentCount = getCurrentFieldCount();
                    if (currentCount >= MAX_FIELDS) return;

                    // Số thứ tự mới = số field hiện tại + 1
                    const nextIndex = currentCount + 1;
                    const template = $('#link-field-template').html().replace(/__INDEX__/g, nextIndex);
                    $('#link-fields-container').append(template);

                    updateCounter();
                });

                // Xóa field
                $(document).on('click', '.remove-link-field', function(e) {
                    e.preventDefault();

                    const currentCount = getCurrentFieldCount();
                    if (currentCount <= 1) {
                        alert('You must have at least one link field.');
                        return;
                    }

                    const $field = $(this).closest('.list-link-row-wrap');
                    const hasData = $field.attr('data-has-data') === 'true';

                    if (hasData && !confirm('This field contains data. Are you sure you want to remove it?')) {
                        return;
                    }

                    $field.remove();

                    // Cập nhật số thứ tự SAU KHI XÓA
                    renumberFields();
                    updateCounter();
                });

                // Đánh số lại tất cả field từ 1 -> n
                function renumberFields() {
                    $('#link-fields-container .list-link-row-wrap').each(function(index) {
                        const newIndex = index + 1;
                        const $field = $(this);

                        $field.attr('data-field-index', newIndex);

                        // Cập nhật tiêu đề
                        const $title = $field.find('.list-h3-title');
                        $title.contents().filter(function() {
                            return this.nodeType === 3;
                        })[0].nodeValue = 'Link ' + newIndex + ' ';

                        // Cập nhật tất cả input và label
                        $field.find('input, label').each(function() {
                            const $element = $(this);
                            const isInput = $element.is('input');
                            const oldName = isInput ? $element.attr('name') : $element.attr('for');

                            if (oldName) {
                                const fieldName = oldName.split('-')[0];
                                const newName = fieldName + '-' + newIndex;

                                if (isInput) {
                                    $element.attr({
                                        'name': newName,
                                        'id': newName
                                    });
                                } else {
                                    $element.attr('for', newName);
                                }
                            }
                        });
                    });
                }

                updateCounter();
            });
        </script>

        <?php do_action('link_field_meta_box_after', $post); ?>
        <?php
    }
}