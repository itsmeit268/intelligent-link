<?php

defined('ABSPATH') || exit;

class Handler_Link {
    private static $instance = null;

    const SUBFIX = '1';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        add_action('init', array($this, 'add_link_param'), 10, 0);
        add_filter('the_content', array($this, 'process_content'), 99);
        add_action('woocommerce_short_description', array($this,'render_meta_short_description'), 99);
        add_action('wp_enqueue_scripts', array($this, 'process_link_scripts'), 99);
        add_action('wp_ajax_handle_direct_link', array($this, 'handle_direct_link'));
        add_action('wp_ajax_nopriv_handle_direct_link', array($this, 'handle_direct_link'));
        add_action('wp_ajax_validate_password', array($this, 'validate_password'));
        add_action('wp_ajax_nopriv_validate_password', array($this, 'validate_password'));
    }

    public function handle_direct_link() {
        $link = isset($_POST['link']) ? sanitize_text_field($_POST['link']) : '';

        if (empty($link)) {
            wp_send_json_error(['message' => 'No URL provided']);
            return;
        }

        $link = ilgl_settings()->decrypt_url($link);

        if ($link) {
            wp_send_json_success(['link' => $link]);
        } else {
            wp_send_json_error(['message' => 'Decryption failed']);
        }
    }

    public function validate_password() {
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
        $link = isset($_POST['link']) ? sanitize_text_field($_POST['link']) : '';

        if (empty($link)) {
            wp_send_json_error(['message' => __('Missing required parameters', 'intelligent-link')]);
            return;
        }

        $ep_settings = ilgl_settings()->ep_settings();
        $stored_password = !empty($ep_settings['endpoint_password']) ? $ep_settings['endpoint_password'] : '';

        if (empty($stored_password)) {
            $decrypted_link = ilgl_settings()->decrypt_url($link);
            
            if ($decrypted_link) {
                wp_send_json_success(['link' => $decrypted_link]);
            } else {
                wp_send_json_success(['link' => $link]);
            }
            return;
        }

        if (empty($password)) {
            wp_send_json_error(['message' => __('Password required', 'intelligent-link')]);
            return;
        }

        if ($password !== $stored_password) {
            wp_send_json_error(['message' => __('Incorrect password', 'intelligent-link')]);
            return;
        }

        $decrypted_link = ilgl_settings()->decrypt_url($link);
        
        if ($decrypted_link) {
            wp_send_json_success(['link' => $decrypted_link]);
        } else {
            wp_send_json_success(['link' => $link]);
        }
    }

    public function process_link_scripts(){
        if (!ilgl_helper()->is_plugin_enable() || !is_singular(['post', 'product'])) {
            return;
        }

        wp_enqueue_style('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'assets/css/intelligent-link.css', array(), INTELLIGENT_LINK_VERSION, 'all');
        wp_enqueue_script('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'assets/js/intelligent-link.min.js', array('jquery'), INTELLIGENT_LINK_VERSION, true);

        $settings    = ilgl_settings()->global_settings();
        $ep_settings = ilgl_settings()->ep_settings();
        $meta_option = ilgl_settings()->meta_option();

        wp_localize_script('intelligent-link', 'href_vars', [
                'end_point'       => ilgl_helper()->param_url(),
                'subfix'          => self::SUBFIX,
                'count_down'      => !empty($settings['preplink_countdown']) ? $settings['preplink_countdown'] : 0,
                'cookie_time'     => !empty($ep_settings['cookie_time']) ? $ep_settings['cookie_time'] : 5,
                'display_mode'    => !empty($settings['preplink_wait_text']) ? $settings['preplink_wait_text'] : 'wait_time',
                'wait_text'       => !empty($settings['wait_text_replace']) ? $settings['wait_text_replace'] : 'please wait',
                'auto_direct'     => !empty($settings['preplink_auto_direct']) ? $settings['preplink_auto_direct'] : 0,
                'replace_text'    => [
                    'enable' => !empty($settings['replace_text_enable']) ? $settings['replace_text_enable'] : 0,
                    'text'   => !empty($settings['replace_text']) ? $settings['replace_text'] : 'link is ready',
                ],
                'enable_rewrite' => isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes',
                'meta_attr'       => [
                    'auto_direct' => !empty($meta_option['auto_direct']) ? $meta_option['auto_direct'] : 0,
                    'time'        => isset($meta_option['time']) ? $meta_option['time'] : 5,
                ]
            ]
        );
    }

    public function add_link_param(){
        if (!ilgl_helper()->is_plugin_enable()) {
            return;
        }

        if (is_admin()) {
            return;
        }

        add_filter('query_vars', function ($vars) {
            $vars[] = ilgl_helper()->param_url();
            return $vars;
        });

        add_filter('template_include', [$this, 'link_template']);
    }

    public function link_template($template) {
        include_once INTELLIGENT_LINK_PLUGIN_DIR . 'includes/helper.php';
        $link_template = INTELLIGENT_LINK_PLUGIN_DIR . 'templates/layout/default.php';

        global $wp_query;

        $current_link = get_query_var(ilgl_helper()->param_url());
        $subfix = self::SUBFIX;

        if (!empty($current_link) && $current_link === $subfix) {
            $this->prep_head();

            if (is_singular('product')) {
                remove_all_actions('woocommerce_single_product_summary');
                include_once $link_template;
                exit;
            }

            return $link_template;
        }

        $product_category = isset($wp_query->query_vars['product_cat']) ? $wp_query->query_vars['product_cat']: '';

        if ($product_category == $subfix) {
            remove_all_actions('woocommerce_before_main_content');
            remove_all_actions('woocommerce_archive_description');
            remove_all_actions('woocommerce_before_shop_loop');
            remove_all_actions('woocommerce_shop_loop');
            remove_all_actions('woocommerce_after_shop_loop');
            remove_all_actions('woocommerce_sidebar');

            $this->prep_head();
            include_once $link_template;
            exit;
        }

        return $template;
    }

    private function prep_head() {
        ilgl_helper()->set_no_index_page();
        add_action('wp_head', function() {
            wp_enqueue_style('ilgl-template', INTELLIGENT_LINK_PLUGIN_URL . 'assets/css/template.css', [], INTELLIGENT_LINK_VERSION, 'all');
        }, 99);

        wp_enqueue_script('ilgl-template', INTELLIGENT_LINK_PLUGIN_URL . 'assets/js/template.js', array('jquery'), INTELLIGENT_LINK_VERSION, false);

        $settings = ilgl_settings()->global_settings();

        $ep_settings = ilgl_settings()->ep_settings();
        wp_localize_script('ilgl-template', 'prep_template', [
            'enable_rewrite'      => isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes',
            'countdown_endpoint'  => !empty($ep_settings['countdown_endpoint']) ? $ep_settings['countdown_endpoint'] : 5,
            'endpoint_direct'     => !empty($ep_settings['endpoint_auto_direct']) ? $ep_settings['endpoint_auto_direct'] : 0,
            'ajax_url'            => admin_url('admin-ajax.php'),
        ]);
    }

    public function process_content($content) {
        if (!ilgl_helper()->is_plugin_enable()) {
            return $content;
        }

        if (is_singular(['post', 'product'])) {
            $content = $this->process_content_links_internal($content);
            $content = $this->render_link_info_internal($content);
        }
        return $content;
    }

    private function process_content_links_internal($content) {
        if (empty($content) || strpos($content, '<a') === false) {
            return $content;
        }

        $allow_domains = ilgl_helper()->allow_domain();
        if (empty($allow_domains)) {
            return $content;
        }

        $settings = ilgl_settings()->global_settings();
        $allowed_domains = array_map('trim', explode(',', $allow_domains));
        $exclude_selectors = ilgl_helper()->exclude_elm();

        $excludes = !empty($exclude_selectors) ? array_map('trim', explode(',', $exclude_selectors)) : [];

        $excludes = array_filter($excludes, function($val) {
            return !empty($val);
        });

        $hide_url_text = !empty($settings['hide_url_text']) ? $settings['hide_url_text']: '[Link]';
        $display_mode = $settings['preplink_display'] ?? 'progress';

        $pattern = '/<a\s+([^>]*?)href=(["\'])([^"\']+)\2([^>]*?)>(.*?)<\/a>/is';

        return preg_replace_callback($pattern, function($matches) use ($allowed_domains, $excludes, $hide_url_text, $display_mode) {
            return $this->process_single_link($matches, $allowed_domains, $excludes, $hide_url_text, $display_mode);
        }, $content);
    }

    private function process_single_link($matches, $allowed_domains, $excludes, $hide_url_text, $display_mode) {
        $before_href = $matches[1];
        $href = $matches[3];
        $after_href = $matches[4];
        $inner_html = $matches[5];
        $full_attributes = $before_href . $after_href;

        $is_allowed = false;
        foreach ($allowed_domains as $domain) {
            if (!empty($domain) && strpos($href, $domain) !== false) {
                $is_allowed = true;
                break;
            }
        }

        if (!$is_allowed) {
            return $matches[0];
        }

        foreach ($excludes as $exclude) {
            if (empty($exclude)) continue;

            if (strpos($exclude, '.') === 0) {
                $class_name = substr($exclude, 1);
                if (preg_match('/class=["\'][^"\']*\b' . preg_quote($class_name, '/') . '\b[^"\']*["\']/', $full_attributes)) {
                    return $matches[0];
                }
            } elseif (strpos($exclude, '#') === 0) {
                $id_name = substr($exclude, 1);
                if (preg_match('/id=["\']' . preg_quote($id_name, '/') . '["\']/', $full_attributes)) {
                    return $matches[0];
                }
            }
        }

        if ($href === rawurlencode(rawurldecode($href))) {
            $href = rawurldecode($href);
        }

        $text_link = trim(strip_tags($inner_html));

        if (empty($text_link)) {
            $text_link = '[Link]';
        }

        $settings = ilgl_settings()->global_settings();
        $enable_rewrite = isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes';

        if ($enable_rewrite) {
            if (preg_match('/^(https?:\/\/|www\.)/i', $text_link)) {
                $text_link = $hide_url_text;
            }

            $href = ilgl_settings()->encrypt_url($href);
        }

        $has_media = preg_match('/<(img|svg|i)\b/i', $inner_html);

        if ($has_media) {
            $new_attrs = 'href="#" data-request="' . esc_attr($href) . '" data-text="' . esc_attr($text_link) . '" data-image="1" rel="nofollow"';

            if (preg_match('/class=["\']([^"\']*)["\']/', $full_attributes, $class_match)) {
                $new_attrs .= ' class="' . esc_attr($class_match[1]) . ' prep-request"';
                $full_attributes = preg_replace('/class=["\'][^"\']*["\']/', '', $full_attributes);
            } else {
                $new_attrs .= ' class="prep-request"';
            }

            return '<a ' . $new_attrs . ' ' . trim($full_attributes) . '>' . $inner_html . '</a>';
        } else {
            if ($display_mode === 'progress') {
                return '<a href="#" class="prep-request" data-request="' . esc_attr($href) . '" data-text="' . esc_attr($text_link) . '"><strong class="post-progress">' . esc_html($text_link) . '</strong></a>';
            } else {
                return '<a href="#" class="prep-request" data-request="' . esc_attr($href) . '" data-text="' . esc_attr($text_link) . '"><strong class="link-countdown">' . esc_html($text_link) . '</strong></a>';
            }
        }
    }

    private function render_link_info_internal($content) {
        $post_id = get_the_ID();

        $file_name = get_post_meta($post_id, 'file_name', true);
        $link_no_login = get_post_meta($post_id, 'link_no_login', true);
        $link_is_login = get_post_meta($post_id, 'link_is_login', true);

        if ($file_name && $link_is_login && $link_no_login) {
            $meta_option = ilgl_settings()->meta_option();
            $product_elm_after_content = isset($meta_option['product_elm']) && $meta_option['product_elm'] == 'after_product_content';
            $html = $this->prep_link_html($meta_option, $file_name, $link_is_login, $link_no_login);
            $is_post_or_product = is_singular('post') || (is_singular('product') && $product_elm_after_content);

            if ($is_post_or_product) {
                $last_p = strrpos($content, '</p>');
                if ($last_p !== false) {
                    $content = substr_replace($content, $html, $last_p + 4, 0);
                } else {
                    $content .= $html;
                }
            }
        }

        return $content;
    }

    public function render_meta_short_description($content) {

        if (!ilgl_helper()->is_plugin_enable()) {
            return $content;
        }

        $post_id = get_the_ID();

        $file_name = get_post_meta($post_id, 'file_name', true);
        $link_no_login = get_post_meta($post_id, 'link_no_login', true);
        $link_is_login = get_post_meta($post_id, 'link_is_login', true);

        if (!$file_name || !$link_is_login || !$link_no_login) {
            return $content;
        }

        $meta_option = ilgl_settings()->meta_option();
        $after_description = isset($meta_option['product_elm']) ? $meta_option['product_elm'] == 'after_short_description' : false;

        $content = $this->process_content_links_internal($content);

        if (empty(get_the_excerpt()) || !$after_description) {
            return $content;
        }

        $html = $this->prep_link_html($meta_option, $file_name, $link_is_login, $link_no_login);
        return $content . $html;
    }

    public function prep_link_html($meta_attr, $file_name, $link_is_login, $link_no_login) {
        $settings = ilgl_settings()->global_settings();
        $display_mode = $settings['preplink_wait_text'] ?? 'wait_time';

        $elm = $meta_attr['elm'] ?? 'h3';
        $pre_fix = $meta_attr['pre_fix'] ?? 'Link download: ';

        $html = '<' . $elm . ' class="igl-download-now">' . $pre_fix;

        $link = is_user_logged_in() ? $link_is_login : $link_no_login;

        $enable_rewrite = isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes';

        if ($enable_rewrite) {
            $link = ilgl_settings()->encrypt_url($link);
        }

        if ($display_mode === 'progress') {
            $html .= '<a href="#" class="prep-request" data-request="'.esc_attr($link).'" data-meta="1"><strong class="post-progress meta-link">' . $file_name . '</strong><a/>';
        } else {
            $html .= '<a href="#" class="prep-request" data-request="'.esc_attr($link).'" data-meta="1"><strong class="link-countdown">' . $file_name . '</strong><a/>';
        }

        $html .= '</' . $elm . '>';

        $list_link = get_post_meta(get_the_ID(), 'link-download-metabox', true);
        $total = (int) ($meta_attr['field_lists'] ?? 5);

        if (isset($list_link) && !empty($list_link) && is_array($list_link) && array_filter($list_link)) {
            $list_title = $meta_attr['list_title'] ? : 'Other Version';
            $html .= '<div class="list-link-redirect">';
            $html .= '<div class="ilgl-other-version">'. $list_title.'</div>';
            $html .= '<ul class="ilgl-list-link">';

            for ($i = 1; $i <= $total; $i++) {
                $file_name_key = 'file_name-' . $i;
                $link_no_login_key = 'link_no_login-' . $i;
                $link_is_login_key = 'link_is_login-' . $i;
                $size_key = 'size-' . $i;

                if (isset($list_link[$file_name_key]) && !empty($list_link[$link_no_login_key]) && isset($list_link[$link_is_login_key])) {
                    $file_name_item = $list_link[$file_name_key];
                    $size = $list_link[$size_key] ?? '';
                    $link = is_user_logged_in() ? $list_link[$link_is_login_key] : $list_link[$link_no_login_key];
                    if ($enable_rewrite) {
                        $link = ilgl_settings()->encrypt_url($link);
                    }

                    $html .= '<li>';

                    $html .= '<a href="#" class="prep-request" data-request="' . esc_attr($link) . '" data-meta="1">';
                    $html .= '<strong class="post-progress">' . esc_html($file_name_item . ' ' . $size) . '</strong>';
                    $html .= '</li>';
                }
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        return $html;
    }
}

Handler_Link::get_instance();