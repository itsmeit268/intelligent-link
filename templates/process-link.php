<?php

class Process_Link {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        add_action('init', array($this, 'preplink_rewrite_endpoint'), 10, 0);
        add_filter('the_content', array($this, 'process_content_links'), 99);
        add_action('woocommerce_short_description', array($this,'render_meta_short_description'), 10);
        add_action('wp_enqueue_scripts', array($this, 'process_link_scripts'));
    }

    public function process_link_scripts(){
        if ($this->is_plugin_enable() && is_singular(['post', 'product'])){
            wp_enqueue_style('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'assets/css/intelligent-link'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.css', array(), INTELLIGENT_LINK_VERSION, 'all');
            $href_vars = [];

            wp_enqueue_script('wp-i18n', includes_url('/js/dist/i18n.js'), array('wp-element'), '1.0', true);
            wp_enqueue_script('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'assets/js/intelligent-link.js', array('jquery'), INTELLIGENT_LINK_VERSION, true);

            $href_vars = apply_filters('ilgl_href_vars', $href_vars);
            $settings = $this->ilgl_settings();
            $meta_option = $this->ilgl_meta_option();
            wp_localize_script('intelligent-link', 'href_vars', array_merge([
                    'end_point'       => $this->endpoint_conf(),
                    'count_down'      => !empty($settings['preplink_countdown']) ? $settings['preplink_countdown'] : 0,
                    'cookie_time'     => !empty($this->ep_settings()['cookie_time']) ? $this->ep_settings()['cookie_time'] : 5,
                    'display_mode'    => !empty($settings['preplink_wait_text']) ? $settings['preplink_wait_text'] : 'wait_time',
                    'wait_text'       => !empty($settings['wait_text_replace']) ? $settings['wait_text_replace'] : 'please wait',
                    'auto_direct'     => !empty($settings['preplink_auto_direct']) ? $settings['preplink_auto_direct'] : 0,
                    'modify_conf'     => $this->modify_conf(),
                    'replace_text'    => [
                            'enable' => !empty($settings['replace_text_enable']) ? $settings['replace_text_enable'] : 0,
                            'text'   => !empty($settings['replace_text']) ? $settings['replace_text'] : 'link is ready',
                    ],
                    'meta_attr'       => [
                            'auto_direct' => !empty($meta_option['auto_direct']) ? $meta_option['auto_direct'] : 0,
                            'time'        => isset($meta_option['time']) ? $meta_option['time'] : 5,
                    ]
            ],
                    $href_vars
            ));
        }
    }


    public function preplink_rewrite_endpoint(){
        if ($this->is_plugin_enable()){
            add_rewrite_endpoint($this->endpoint_conf(), EP_PERMALINK | EP_ROOT );
            add_filter('template_include', [$this, 'intelligent_link_template_include']);
            if (INTELLIGENT_LINK_DEV == 1) {
                flush_rewrite_rules();
            }
        }
    }

    public function prep_head() {
        wp_enqueue_style('ilgl-template', INTELLIGENT_LINK_PLUGIN_URL . 'assets/css/template.css', [], INTELLIGENT_LINK_VERSION, 'all');
        wp_enqueue_script('ilgl-template', INTELLIGENT_LINK_PLUGIN_URL . 'assets/js/template.js', array('jquery'), INTELLIGENT_LINK_VERSION, false);
        $settings = $this->ep_settings();
        wp_localize_script('ilgl-template', 'prep_template', [
            'modify_conf'         => $this->modify_conf(),
            'countdown_endpoint'  => !empty($settings['countdown_endpoint']) ? $settings['countdown_endpoint'] : 5,
            'endpoint_direct'     => !empty($settings['endpoint_auto_direct']) ? $settings['endpoint_auto_direct'] : 0
        ]);
    }

    public function intelligent_link_template_include($template) {

        include_once plugin_dir_path( __FILE__ ) . '../includes/helper.php';
        $intelligent_link_template = plugin_dir_path( __FILE__ ) . 'layout/default.php';

        global $wp_query;
        if (isset($wp_query->query_vars[$this->endpoint_conf()])) {
            $this->prep_head();
            if (is_singular('product')) {
                remove_all_actions( 'woocommerce_single_product_summary' );
                include_once $intelligent_link_template;
                exit;
            }

            return $intelligent_link_template;
        }

        $product_category = isset($wp_query->query_vars['product_cat']) ? $wp_query->query_vars['product_cat']: '';

        if ($product_category == $this->endpoint_conf()) {
            remove_all_actions('woocommerce_before_main_content');
            remove_all_actions('woocommerce_archive_description');
            remove_all_actions('woocommerce_before_shop_loop');
            remove_all_actions('woocommerce_shop_loop');
            remove_all_actions('woocommerce_after_shop_loop');
            remove_all_actions('woocommerce_sidebar');

            $this->prep_head();
            include_once $intelligent_link_template;
            exit;
        }

        return $template;
    }

    public function endpoint_conf(){
        $endpoint = '1';
        if (!empty($this->ep_settings()['endpoint'])) {
            $endpoint = preg_replace(
                    '/[^\p{L}a-zA-Z0-9_\-.]/u',
                    '',
                    trim($this->ep_settings()['endpoint'])
            );
        }
        return $endpoint;
    }


    public function ilgl_settings() {
        return get_option('preplink_setting', []);
    }

    public function ep_settings() {
        return get_option('preplink_endpoint', []);
    }

    public function ilgl_meta_option(){
        return get_option('meta_attr', []);
    }

    public function is_plugin_enable(){
        $settings = $this->ilgl_settings();
        return !empty($settings['preplink_enable_plugin']) && (int)$settings['preplink_enable_plugin'] == 1;
    }

    public function modify_conf() {
        $settings = $this->ilgl_settings();
        $modify_href = [
                'pfix'  => !empty($settings['prefix']) ? $settings['prefix']: 'gqbQsbQjv4Wd9NP',
                'mstr'  => !empty($settings['between']) ? base64_encode($settings['between']): base64_encode('aC5mQ1sj9Nvo9AK'),
                'sfix'  => !empty($settings['suffix']) ? base64_encode($settings['suffix']): base64_encode('FTTvYmbQ9Ni1mmVf'),
        ];
        return $modify_href;
    }

    public function modify_href($url_encode) {
        $modify_conf = $this->modify_conf();
        $url_encode = substr($url_encode, 0, 5) . $modify_conf['pfix'] . substr($url_encode, 5);
        $url_encode = substr($url_encode, 0, strlen($url_encode) / 2) . $modify_conf['mstr'] . substr($url_encode, strlen($url_encode) / 2);
        $url_encode = substr($url_encode, 0, -12) . $modify_conf['sfix'] . substr($url_encode, -12);
        return $url_encode;
    }

    public function modify_list_href($url_encode) {
        $modify_conf = $this->modify_conf();
        $url_encode = substr($url_encode, 0, 3) .$modify_conf['mstr'] . substr($url_encode, 3);
        $url_encode = substr($url_encode, 0, strlen($url_encode) / 2) . $modify_conf['pfix'] . substr($url_encode, strlen($url_encode) / 2);
        $url_encode = substr($url_encode, 0, -8) . $modify_conf['sfix'] . substr($url_encode, -8);
        return $url_encode;
    }

    public function exclude_elm(){
        $settings = $this->ilgl_settings();
        $excludeList = $settings['preplink_excludes_element'];

        if (!empty($excludeList)) {
            $excludesArr = explode(',', $excludeList);
            $excludesArr = array_map('trim', $excludesArr);
            $excludesArr = array_merge($excludesArr, ['.prep-link-download-btn', '.prep-link-btn', '.comment', '.session-expired']);
            $excludesArr = array_unique($excludesArr);
            $excludeList = implode(',', $excludesArr);
        } else {
            $excludeList = '.prep-link-download-btn,.prep-link-btn,.session-expired,.comment';
        }
        return $excludeList;
    }

    public function allow_domain(){
        $allow_domain = '';
        $settings = $this->ilgl_settings();
        $prepList = $settings['preplink_url'];
        if (!empty($prepList)) {
            $prepArr = explode(',', $prepList);
            $prepArr = array_map('trim', $prepArr);

            $lastIndex = count($prepArr) - 1;
            if (empty($prepArr[$lastIndex])) {
                unset($prepArr[$lastIndex]);
            }
            $allow_domain = implode(',', $prepArr);
            $allow_domain = rtrim($allow_domain, ',');
        }
        return $allow_domain;
    }

    public function render_meta_short_description($content) {
        $file_name = get_post_meta(get_the_ID(), 'file_name', true);
        $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
        $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);

        if ($file_name && $link_is_login && $link_no_login && $this->is_plugin_enable()) {
            $meta_option = $this->ilgl_meta_option();
            $after_description = isset($meta_option['product_elm'])? $meta_option['product_elm'] == 'after_short_description': '';
            $html = $this->prep_link_html($meta_option, $file_name);
            if (!empty(get_the_excerpt()) && $after_description) {
                return $content. $html;
            }
        }

        return $content;
    }

    public function process_content_links($content) {
        if (!is_single() && !is_page() ) {
            return $content;
        }

        if (!$this->is_plugin_enable()) {
            return $content;
        }

        $settings = $this->ilgl_settings();
        $allow_domains = $this->allow_domain();

        if (empty($allow_domains)) {
            return $content;
        }

        $allowed_domains = array_map('trim', explode(',', $allow_domains));
        $exclude_selectors = !empty($settings['preplink_exclude']) ? $settings['preplink_exclude'] : '';
        $hide_url_text = !empty($settings['hide_url_text']) ? $settings['hide_url_text'] : '[Link]';
        $display_mode = !empty($settings['preplink_display']) ? $settings['preplink_display'] : 'progress';

        $excludes = array();
        if (!empty($exclude_selectors)) {
            $excludes = array_map('trim', explode(',', $exclude_selectors));
        }

        $pattern = '/<a\s+([^>]*?)href=(["\'])([^"\']+)\2([^>]*?)>(.*?)<\/a>/is';

        $content = preg_replace_callback($pattern, function($matches) use ($allowed_domains, $excludes, $hide_url_text, $display_mode) {
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

            $text_link = strip_tags($inner_html);
            $text_link = trim($text_link);

            if (empty($text_link)) {
                $text_link = '>> Redirect Link <<';
            }

            if (preg_match('/^(https?:\/\/|www\.)/i', $text_link)) {
                $text_link = $hide_url_text;
            }

            $encoded_url = $this->modify_href(base64_encode($href));

            $has_media = preg_match('/<(img|svg|i)\b/i', $inner_html);

            if ($has_media) {
                $new_attrs = 'href="javascript:void(0)" data-id="' . esc_attr($encoded_url) . '" data-text="' . esc_attr($text_link) . '" data-image="1" rel="nofollow noopener noreferrer"';

                if (preg_match('/class=["\']([^"\']*)["\']/', $full_attributes, $class_match)) {
                    $new_attrs .= ' class="' . esc_attr($class_match[1]) . ' prep-request"';
                    $full_attributes = preg_replace('/class=["\'][^"\']*["\']/', '', $full_attributes);
                } else {
                    $new_attrs .= ' class="prep-request"';
                }

                return '<a ' . $new_attrs . ' ' . trim($full_attributes) . '>' . $inner_html . '</a>';
            } else {
                if ($display_mode === 'progress') {
                    return '<span class="post-progress-bar" style="display:inline-block;"><span class="prep-request" data-id="' . esc_attr($encoded_url) . '" data-text="' . esc_attr($text_link) . '"><strong class="post-progress">' . esc_html($text_link) . '</strong></span></span>';
                } else {
                    return '<span class="wrap-countdown"><span class="prep-request" data-id="' . esc_attr($encoded_url) . '" data-text="' . esc_attr($text_link) . '"><strong class="link-countdown">' . esc_html($text_link) . '</strong></span></span>';
                }
            }

        }, $content);

        return $content;
    }


    public function prep_link_html($meta_attr, $file_name) {
        $blog_url = base64_encode(get_bloginfo('url'));
        $settings = $this->ilgl_settings();
        $display_mode = !empty($settings['preplink_wait_text']) ? $settings['preplink_wait_text'] : 'wait_time';
        $html = '<' . (!empty($meta_attr['elm']) ? $meta_attr['elm'] : 'h3') . ' class="igl-download-now"><b class="b-h-down">' . (!empty($meta_attr['pre_fix']) ? $meta_attr['pre_fix'] : 'Link download: ') . '</b>';

        if ($display_mode === 'progress') {
            $html .= '<div class="post-progress-bar">';
            $html .= '<span class="prep-request" data-request="1" data-id="' . $blog_url . '"><strong class="post-progress">' . $file_name . '</strong></span></div>';
        } else {
            $html .= '<span class="wrap-countdown">';
            $html .= '<span class="prep-request" data-request="1" data-id="' . $blog_url . '"><strong class="link-countdown">' . $file_name . '</strong></span></span>';
        }

        $html .= '</' . (!empty($meta_attr['elm']) ? $meta_attr['elm'] : 'h3') . '>';

        $list_link = get_post_meta(get_the_ID(), 'link-download-metabox', true);

        $total = (int) $meta_attr['field_lists']? : 5;


        if (isset($list_link) && !empty($list_link) && is_array($list_link)) {
            $html .= '<div class="list-link-redirect">';
            $html .= '<p class="ilgl-other-version">'.__('Other Version').'</p>';
            $html .= '<ul>';

            for ($i = 1; $i <= $total; $i++) {
                $file_name_key = 'file_name-' . $i;
                $link_no_login_key = 'link_no_login-' . $i;
                $link_is_login_key = 'link_is_login-' . $i;
                $size_key = 'size-' . $i;

                if (isset($list_link[$file_name_key]) && !empty($list_link[$link_no_login_key]) && isset($list_link[$link_is_login_key])) {
                    $file_name = $list_link[$file_name_key];
                    $size = $list_link[$size_key];
                    $html .= '<li>';
                    if (is_user_logged_in()) {
                        $html .= '<a href="' . esc_html($list_link[$link_is_login_key]) . '" class="preplink-btn-link list-preplink-btn-link">' . esc_html($file_name . ' ' . $size) . '</a>';
                    } else {
                        $html .= '<a href="' . esc_html($list_link[$link_no_login_key]) . '" class="preplink-btn-link list-preplink-btn-link">' . esc_html($file_name . ' ' . $size) . '</a>';
                    }
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
