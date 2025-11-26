<?php

class Process_Link {
    private static $instance = null;

    const CACHE_TTL = 3600;
    const SUBFIX = '1';
    private $settings_cache = null;
    private $ep_settings_cache = null;
    private $meta_cache = null;
    private $compiled_regex = null;
    private $modify_conf_cache = null;
    private $endpoint_cache = null;
    private $allow_domain_cache = null;
    private $exclude_elm_cache = null;
    private $render_cache = [];

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
    }

    public function ilgl_settings() {
        if ($this->settings_cache === null) {
            $this->settings_cache = wp_cache_get('preplink_setting', 'preplink');
            if (false === $this->settings_cache) {
                $this->settings_cache = get_option('preplink_setting', []);
                wp_cache_set('preplink_setting', $this->settings_cache, 'preplink', self::CACHE_TTL);
            }
        }
        return $this->settings_cache;
    }

    public function ep_settings() {
        if ($this->ep_settings_cache === null) {
            $this->ep_settings_cache = wp_cache_get('preplink_endpoint', 'preplink');
            if (false === $this->ep_settings_cache) {
                $this->ep_settings_cache = get_option('preplink_endpoint', []);
                wp_cache_set('preplink_endpoint', $this->ep_settings_cache, 'preplink', self::CACHE_TTL);
            }
        }
        return $this->ep_settings_cache;
    }

    public function ilgl_meta_option(){
        if ($this->meta_cache === null) {
            $this->meta_cache = wp_cache_get('meta_attr', 'preplink');
            if (false === $this->meta_cache) {
                $this->meta_cache = get_option('meta_attr', []);
                wp_cache_set('meta_attr', $this->meta_cache, 'preplink', self::CACHE_TTL);
            }
        }
        return $this->meta_cache;
    }

    public function modify_conf() {
        if ($this->modify_conf_cache === null) {
            $settings = $this->ilgl_settings();
            $this->modify_conf_cache = [
                'pfix'  => !empty($settings['prefix']) ? $settings['prefix']: 'gqbQsbQjv4Wd9NP',
                'mstr'  => !empty($settings['between']) ? base64_encode($settings['between']): base64_encode('aC5mQ1sj9Nvo9AK'),
                'sfix'  => !empty($settings['suffix']) ? base64_encode($settings['suffix']): base64_encode('FTTvYmbQ9Ni1mmVf'),
            ];
        }
        return $this->modify_conf_cache;
    }

    public function param_url(){
        if ($this->endpoint_cache === null) {
            $endpoint = 'link';
            $ep_settings = $this->ep_settings();
            if (!empty($ep_settings['endpoint'])) {
                $endpoint = preg_replace(
                    '/[^\p{L}a-zA-Z0-9_\-.]/u',
                    '',
                    trim($ep_settings['endpoint'])
                );
            }
            $this->endpoint_cache = $endpoint;
        }
        return $this->endpoint_cache;
    }

    public function is_plugin_enable(){
        $settings = $this->ilgl_settings();
        return !empty($settings['preplink_enable_plugin']) && (int)$settings['preplink_enable_plugin'] == 1;
    }

    public function process_link_scripts(){
        if (!$this->is_plugin_enable() || !is_singular(['post', 'product'])) {
            return;
        }

        wp_enqueue_style('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'assets/css/intelligent-link.css', array(), INTELLIGENT_LINK_VERSION, 'all');

        wp_enqueue_script('wp-i18n', includes_url('/js/dist/i18n.js'), array('wp-element'), INTELLIGENT_LINK_VERSION, true);
        wp_enqueue_script('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'assets/js/intelligent-link.min.js', array('jquery'), INTELLIGENT_LINK_VERSION, true);

        $settings = $this->ilgl_settings();
        $meta_option = $this->ilgl_meta_option();
        $ep_settings = $this->ep_settings();

        wp_localize_script('intelligent-link', 'href_vars', [
            'end_point'       => $this->param_url(),
            'subfix'          => self::SUBFIX,
            'count_down'      => !empty($settings['preplink_countdown']) ? $settings['preplink_countdown'] : 0,
            'cookie_time'     => !empty($ep_settings['cookie_time']) ? $ep_settings['cookie_time'] : 5,
            'display_mode'    => !empty($settings['preplink_wait_text']) ? $settings['preplink_wait_text'] : 'wait_time',
            'wait_text'       => !empty($settings['wait_text_replace']) ? $settings['wait_text_replace'] : 'please wait',
            'auto_direct'     => !empty($settings['preplink_auto_direct']) ? $settings['preplink_auto_direct'] : 0,
            'modify_conf'     => $this->modify_conf(),
            'replace_text'    => [
                'enable' => !empty($settings['replace_text_enable']) ? $settings['replace_text_enable'] : 0,
                'text'   => !empty($settings['replace_text']) ? $settings['replace_text'] : 'link is ready',
            ],
            'enable_rewrite' => isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes',
            'meta_attr'       => [
                    'auto_direct' => !empty($meta_option['auto_direct']) ? $meta_option['auto_direct'] : 0,
                    'time'        => isset($meta_option['time']) ? $meta_option['time'] : 5,
                ]
            ]);
    }

    public function add_link_param(){
        if (!$this->is_plugin_enable()) {
            return;
        }

        add_filter('query_vars', function ($vars) {
            $vars[] = $this->param_url();
            return $vars;
        });

        add_filter('template_include', [$this, 'link_template']);
    }

    public function link_template($template) {
        include_once plugin_dir_path( __FILE__ ) . '../includes/helper.php';
        $link_template = plugin_dir_path( __FILE__ ) . 'layout/default.php';

        global $wp_query;

        $current_link = get_query_var($this->param_url());
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

    public function prep_head() {
        add_action('wp_head', function() {
            wp_enqueue_style('ilgl-template', INTELLIGENT_LINK_PLUGIN_URL . 'assets/css/template.css', [], INTELLIGENT_LINK_VERSION, 'all');
        }, 99);

        wp_enqueue_script('ilgl-template', INTELLIGENT_LINK_PLUGIN_URL . 'assets/js/template.min.js', array('jquery'), INTELLIGENT_LINK_VERSION, false);

        $settings = $this->ilgl_settings();
        $ep_settings = $this->ep_settings();
        wp_localize_script('ilgl-template', 'prep_template', [
            'modify_conf'         => $this->modify_conf(),
            'enable_rewrite' =>      isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes',
            'countdown_endpoint'  => !empty($ep_settings['countdown_endpoint']) ? $ep_settings['countdown_endpoint'] : 5,
            'endpoint_direct'     => !empty($ep_settings['endpoint_auto_direct']) ? $ep_settings['endpoint_auto_direct'] : 0
        ]);
    }

    public function process_content($content) {
        if (!is_single() && !is_page()) {
            return $content;
        }

        if (!$this->is_plugin_enable()) {
            return $content;
        }

        $content = $this->process_content_links_internal($content);

        $content = $this->render_link_info_internal($content);

        return $content;
    }

    private function process_content_links_internal($content) {
        if (empty($content) || strpos($content, '<a') === false) {
            return $content;
        }

        $allow_domains = $this->allow_domain();
        if (empty($allow_domains)) {
            return $content;
        }

        $settings = $this->ilgl_settings();
        $allowed_domains = array_map('trim', explode(',', $allow_domains));
        $exclude_selectors = $this->exclude_elm();

        $excludes = !empty($exclude_selectors) ? array_map('trim', explode(',', $exclude_selectors)) : [];

        $excludes = array_filter($excludes, function($val) {
            return !empty($val);
        });

        $hide_url_text = $settings['hide_url_text'] ?? '[Link]';
        $display_mode = $settings['preplink_display'] ?? 'progress';

        $pattern = $this->get_compiled_regex();

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
            $text_link = '>> Redirect Link <<';
        }


        $settings = $this->ilgl_settings();
        $enable_rewrite = isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes';

        if ($enable_rewrite) {
            if (preg_match('/^(https?:\/\/|www\.)/i', $text_link)) {
                $text_link = $hide_url_text;
            }

            $href = $this->modify_href(base64_encode($href));
        }

        $has_media = preg_match('/<(img|svg|i)\b/i', $inner_html);

        if ($has_media) {
            $new_attrs = 'href="javascript:void(0)" data-request="' . esc_attr($href) . '" data-text="' . esc_attr($text_link) . '" data-image="1" rel="nofollow noopener noreferrer"';

            if (preg_match('/class=["\']([^"\']*)["\']/', $full_attributes, $class_match)) {
                $new_attrs .= ' class="' . esc_attr($class_match[1]) . ' prep-request"';
                $full_attributes = preg_replace('/class=["\'][^"\']*["\']/', '', $full_attributes);
            } else {
                $new_attrs .= ' class="prep-request"';
            }

            return '<a ' . $new_attrs . ' ' . trim($full_attributes) . '>' . $inner_html . '</a>';
        } else {
            if ($display_mode === 'progress') {
                return '<span class="post-progress-bar" style="display:inline-block;"><span class="prep-request" data-request="' . esc_attr($href) . '" data-text="' . esc_attr($text_link) . '"><strong class="post-progress">' . esc_html($text_link) . '</strong></span></span>';
            } else {
                return '<span class="wrap-countdown"><span class="prep-request" data-request="' . esc_attr($href) . '" data-text="' . esc_attr($text_link) . '"><strong class="link-countdown">' . esc_html($text_link) . '</strong></span></span>';
            }
        }
    }

    private function render_link_info_internal($content) {
        $post_id = get_the_ID();

        if (!isset($this->render_cache[$post_id])) {
            $this->render_cache[$post_id] = [
                'file_name' => get_post_meta($post_id, 'file_name', true),
                'link_no_login' => get_post_meta($post_id, 'link_no_login', true),
                'link_is_login' => get_post_meta($post_id, 'link_is_login', true),
            ];
        }

        $cached = $this->render_cache[$post_id];

        if ($cached['file_name'] && $cached['link_is_login'] && $cached['link_no_login']) {
            $meta_option = $this->ilgl_meta_option();
            $product_elm_after_content = isset($meta_option['product_elm']) && $meta_option['product_elm'] == 'after_product_content';
            $html = $this->prep_link_html($meta_option, $cached['file_name'], $cached['link_is_login'], $cached['link_no_login']);
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

    public function exclude_elm(){
        if ($this->exclude_elm_cache !== null) {
            return $this->exclude_elm_cache;
        }

        $settings = $this->ilgl_settings();
        $excludeList = $settings['preplink_excludes_element'] ?? '';

        if (!empty($excludeList)) {
            $excludesArr = array_map('trim', explode(',', $excludeList));
            $excludesArr = array_merge($excludesArr, ['.prep-link-download-btn', '.prep-link-btn', '.session-expired']);
            $excludesArr = array_unique($excludesArr);
            $this->exclude_elm_cache = implode(',', $excludesArr);
        } else {
            $this->exclude_elm_cache = '.prep-link-download-btn,.prep-link-btn,.session-expire';
        }

        return $this->exclude_elm_cache;
    }

    public function allow_domain(){
        if ($this->allow_domain_cache !== null) {
            return $this->allow_domain_cache;
        }

        $settings = $this->ilgl_settings();
        $prepList = $settings['preplink_url'] ?? '';

        if (!empty($prepList)) {
            $prepArr = array_map('trim', explode(',', $prepList));
            $prepArr = array_filter($prepArr);
            $this->allow_domain_cache = implode(',', $prepArr);
        } else {
            $this->allow_domain_cache = '';
        }

        return $this->allow_domain_cache;
    }

    public function render_meta_short_description($content) {


        if (!$this->is_plugin_enable()) {
            return $content;
        }

        $post_id = get_the_ID();

        if (!isset($this->render_cache[$post_id])) {
            $this->render_cache[$post_id] = [
                'file_name' => get_post_meta($post_id, 'file_name', true),
                'link_no_login' => get_post_meta($post_id, 'link_no_login', true),
                'link_is_login' => get_post_meta($post_id, 'link_is_login', true),
            ];
        }

        $cached = $this->render_cache[$post_id];

        if (!$cached['file_name'] || !$cached['link_is_login'] || !$cached['link_no_login']) {
            return $content;
        }

        $meta_option = $this->ilgl_meta_option();
        $after_description = isset($meta_option['product_elm']) ? $meta_option['product_elm'] == 'after_short_description' : false;

        $content = $this->process_content_links_internal($content);

        if (empty(get_the_excerpt()) || !$after_description) {
            return $content;
        }

        $html = $this->prep_link_html($meta_option, $cached['file_name'], $cached['link_is_login'], $cached['link_no_login']);
        return $content . $html;
    }

    private function get_compiled_regex() {
        if ($this->compiled_regex === null) {
            $this->compiled_regex = '/<a\s+([^>]*?)href=(["\'])([^"\']+)\2([^>]*?)>(.*?)<\/a>/is';
        }
        return $this->compiled_regex;
    }

    public function modify_href($url_encode) {
        $modify_conf = $this->modify_conf();
        $len = strlen($url_encode);
        $half = (int)($len / 2);

        $parts = [
            substr($url_encode, 0, 5),
            $modify_conf['pfix'],
            substr($url_encode, 5, $half),
            $modify_conf['mstr'],
            substr($url_encode, $half + 5, $len - $half - 17),
            $modify_conf['sfix'],
            substr($url_encode, -12)
        ];

        return implode('', $parts);
    }

    public function modify_list_href($url_encode) {
        $modify_conf = $this->modify_conf();
        $len = strlen($url_encode);
        $half = (int)($len / 2);

        $parts = [
            substr($url_encode, 0, 3),
            $modify_conf['mstr'],
            substr($url_encode, 3, $half),
            $modify_conf['pfix'],
            substr($url_encode, $half + 3, $len - $half - 11),
            $modify_conf['sfix'],
            substr($url_encode, -8)
        ];

        return implode('', $parts);
    }

    public function prep_link_html($meta_attr, $file_name, $link_is_login, $link_no_login) {

        $settings = $this->ilgl_settings();
        $display_mode = $settings['preplink_wait_text'] ?? 'wait_time';

        $elm = $meta_attr['elm'] ?? 'h3';
        $pre_fix = $meta_attr['pre_fix'] ?? 'Link download: ';

        $html = '<' . $elm . ' class="igl-download-now"><b class="b-h-down">' . $pre_fix . '</b>';

        $link = is_user_logged_in() ? $link_is_login : $link_no_login;

        $enable_rewrite = isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes';

        if ($enable_rewrite) {
            $link = $this->modify_href(base64_encode($link));
        }

        if ($display_mode === 'progress') {
            $html .= '<div class="post-progress-bar">';
            $html .= '<span class="prep-request" data-request="'.esc_attr($link).'" data-meta="1"><strong class="post-progress meta-link">' . $file_name . '</strong></span></div>';
        } else {
            $html .= '<span class="wrap-countdown">';
            $html .= '<span class="prep-request" data-request="'.esc_attr($link).'" data-meta="1"><strong class="link-countdown">' . $file_name . '</strong></span></span>';
        }

        $html .= '</' . $elm . '>';

        $list_link = get_post_meta(get_the_ID(), 'link-download-metabox', true);
        $total = (int) ($meta_attr['field_lists'] ?? 5);

        if (isset($list_link) && !empty($list_link) && is_array($list_link) && array_filter($list_link)) {
            $html .= '<div class="list-link-redirect">';
            $html .= '<p class="ilgl-other-version">'.__('Other Version').'</p>';
            $html .= '<ul>';

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
                        $link = $this->modify_list_href(base64_encode($link));
                    }

                    $html .= '<li>';
                    $html .= '<span class="post-progress-bar">';
                    $html .= '<span class="prep-request" data-request="' . esc_attr($link) . '" data-meta="1">';
                    $html .= '<strong class="post-progress">' . esc_html($file_name_item . ' ' . $size) . '</strong>';
                    $html .= '</span>';
                    $html .= '</span>';
                    $html .= '</li>';
                }
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        return $html;
    }
}

Process_Link::get_instance();