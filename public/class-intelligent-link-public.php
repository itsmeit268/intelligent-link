<?php

/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

class Intelligent_Link_Public {

    public function __construct(){
        add_action('init', array($this, 'preplink_rewrite_endpoint'), 10, 0);
        add_action('wp_head', array($this, 'add_prep_custom_styles'), 10, 2);
        add_filter('the_content', array($this, 'render_meta_link_info'), 10);
        add_action('woocommerce_short_description', array($this,'render_meta_short_description'), 10);
    }

    public function il_settings() {
        return get_option('preplink_setting');
    }

    public function ep_settings() {
        return get_option('preplink_endpoint');
    }

    public function enqueue_styles(){
        if ($this->is_plugin_enable()){
            wp_enqueue_style('g-intelligent-link', plugin_dir_url(__FILE__) . 'css/intelligent-link.css', array(), PREPLINK_VERSION, 'all');
        }
    }

    public function enqueue_scripts() {
        if ($this->is_plugin_enable()){

            wp_enqueue_script('wp-i18n', includes_url('/js/dist/i18n.js'), array('wp-element'), '1.0', true);
            wp_enqueue_script('intelligent-link', plugin_dir_url(__FILE__) . 'js/intelligent-link.js', array('jquery'), PREPLINK_VERSION, true);
            
            $settings = $this->il_settings();
            $meta_attr = get_option('meta_attr', []);
            wp_localize_script('intelligent-link', 'href_process', [
                'end_point'              => $this->endpoint_conf(),
                'prep_url'               => $this->allow_domain(),
                'pre_elm_exclude'        => $this->exclude_elm(),
                'count_down'             => !empty($settings['preplink_countdown']) ? $settings['preplink_countdown'] : 0,
                'cookie_time'            => !empty($settings['cookie_time']) ? $settings['cookie_time'] : 5,
                'display_mode'           => !empty($settings['preplink_wait_text']) ? $settings['preplink_wait_text'] : 'wait_time',
                'wait_text'              => !empty($settings['wait_text_replace']) ? $settings['wait_text_replace'] : 'please wait',
                'auto_direct'            => !empty($settings['preplink_auto_direct']) ? $settings['preplink_auto_direct'] : 0,
                'modify_href'            => $this->modify_href(),
                'replace_text'           => [
                    'enable' => !empty($settings['replace_text_enable']) ? $settings['replace_text_enable'] : 0,
                    'text'   => !empty($settings['replace_text']) ? $settings['replace_text'] : 'link is ready',
                ],
                'meta_attr'       => [
                    'auto_direct' => !empty($meta_attr['auto_direct']) ? $meta_attr['auto_direct'] : 0,
                    'time'        => isset($meta_attr['time']) ? $meta_attr['time'] : 5,
                ]
            ]);
        }
    }

    public function preplink_rewrite_endpoint(){
        if ($this->is_plugin_enable()){
            add_rewrite_endpoint($this->endpoint_conf(), EP_ALL );
            add_filter('template_include', [$this, 'intelligent_link_template_include']);
//            flush_rewrite_rules();
        }
    }

    public function intelligent_link_template_include($template) {
        global $wp_query;
        $rewrite_template = dirname( __FILE__ ) . '/templates/default.php';

        $product_category = isset($wp_query->query_vars['product_cat']) ? $wp_query->query_vars['product_cat']: '';

        if ($product_category == $this->endpoint_conf()) {
            remove_all_actions('woocommerce_before_main_content');
            remove_all_actions('woocommerce_archive_description');
            remove_all_actions('woocommerce_before_shop_loop');
            remove_all_actions('woocommerce_shop_loop');
            remove_all_actions('woocommerce_after_shop_loop');
            remove_all_actions('woocommerce_sidebar');
            include_once $rewrite_template;
            exit;
        }

        if (isset($wp_query->query_vars[$this->endpoint_conf()])) {

            wp_enqueue_style('prep-template', plugin_dir_url(__FILE__) . 'css/template.css', [], PREPLINK_VERSION, 'all');
            wp_enqueue_script('prep-template', plugin_dir_url(__FILE__) . 'js/template.js', array('jquery'), PREPLINK_VERSION, false);
            wp_localize_script('prep-template', 'prep_template', [
                'countdown_endpoint'     => !empty($this->ep_settings()['countdown_endpoint']) ? $this->ep_settings()['countdown_endpoint'] : 5,
                'endpoint_direct'        => !empty($this->ep_settings()['endpoint_auto_direct']) ? $this->ep_settings()['endpoint_auto_direct'] : 0,
                'modify_href'            => $this->modify_href()
            ]);

            include_once plugin_dir_path(PREPLINK_PLUGIN_FILE) . 'includes/class-endpoint-template.php';

            if (is_singular('product')) {
                remove_all_actions( 'woocommerce_single_product_summary' );
                include_once $rewrite_template;
                exit;
            }

            return $rewrite_template;
        }
        return $template;
    }

    public function modify_href() {
        $settings = $this->il_settings();

        $arr = array(
            'pfix'  => !empty($settings['prefix']) ? $settings['prefix']: 'gqbQ4Wd9NP',
            'mstr'  => !empty($settings['between']) ? $settings['between']: 'aC5Q1sjvo9AK',
            'sfix'  => !empty($settings['suffix']) ? $settings['suffix']: 'FTTvYmo0i1DwVf',
        );

        return $arr;
    }

    public function endpoint_conf(){
        $endpoint = '1';
        if (!empty($this->ep_settings()['endpoint'])) {
            $endpoint = preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim($this->ep_settings()['endpoint']));
        }
        return $endpoint;
    }

    public function add_prep_custom_styles(){
        if ($this->is_plugin_enable() && !empty($this->il_settings()['preplink_custom_style'])) {
            ?>
            <style><?= $this->il_settings()['preplink_custom_style'] ?></style>
            <?php
        }
    }

    public function is_plugin_enable(){
        $settings = $this->il_settings();
        return !empty($settings['preplink_enable_plugin']) && (int)$settings['preplink_enable_plugin'] == 1;
    }

    public function exclude_elm(){
        $excludeList = $this->il_settings()['preplink_excludes_element'];

        if (!empty($excludeList)) {
            $excludesArr = explode(',', $excludeList);
            $excludesArr = array_map('trim', $excludesArr);
            $excludesArr = array_merge($excludesArr, ['.prep-link-download-btn', '.prep-link-btn', '.keyword-search', '.comment', '.session-expired']);
            $excludesArr = array_unique($excludesArr);
            $excludeList = implode(',', $excludesArr);
        } else {
            $excludeList = '.prep-link-download-btn,.prep-link-btn,.keyword-search,.session-expired,.comment';
        }
        return $excludeList;
    }

    public function allow_domain(){
        $allow_domain = '';
        $prepList = $this->il_settings()['preplink_url'];
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

    public function meta_option(){
        return get_option('meta_attr', []);
    }

    public function render_meta_short_description($content) {
        $file_name = get_post_meta(get_the_ID(), 'file_name', true);
        $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
        $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);

        if ($file_name && $link_is_login && $link_no_login) {
            $after_description = isset($this->meta_option()['product_elm'])? $this->meta_option()['product_elm'] == 'after_short_description': '';
            $html = $this->prep_link_html($this->meta_option(), $file_name);
            if (!empty(get_the_excerpt()) && $after_description) {
                return $content. $html;
            }
        }

        return $content;
    }

    public function render_meta_link_info($content) {
        $file_name = get_post_meta(get_the_ID(), 'file_name', true);
        $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
        $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);

        if ($file_name && $link_is_login && $link_no_login) {
            $product_elm_after_content = isset($this->meta_option()['product_elm']) && $this->meta_option()['product_elm'] == 'after_product_content';
            $html = $this->prep_link_html($this->meta_option(), $file_name);

            $is_post_or_product = is_singular('post') || (is_singular('product') && $product_elm_after_content);

            if ($is_post_or_product) {
                $last_p = strrpos($content, '</p>');
                if ($last_p !== false) {
                    $content = substr_replace($content, $html, $last_p + 4, 0);
                }
            }
        }
        return $content;
    }

    public function prep_link_html($meta_attr, $file_name) {
        $blog_url = base64_encode(get_bloginfo('url'));
        $display_mode = !empty($this->il_settings()['preplink_wait_text']) ? $this->il_settings()['preplink_wait_text'] : 'wait_time';
        $html = '<' . (!empty($meta_attr['elm']) ? $meta_attr['elm'] : 'h3') . ' class="igl-download-now"><b class="b-h-down">' . (!empty($meta_attr['pre_fix']) ? $meta_attr['pre_fix'] : 'Link download: ') . '</b>';

        if ($display_mode === 'progress') {
            $html .= '<div class="post-progress-bar">';
            $html .= '<span class="prep-request" data-id="' . $blog_url . '"><strong class="post-progress">' . $file_name . '</strong></span></div>';
        } else {
            $html .= '<span class="wrap-countdown">';
            $html .= '<span class="prep-request" data-id="' . $blog_url . '"><strong class="link-countdown">' . $file_name . '</strong></span></span>';
        }

        $html .= '</' . (!empty($meta_attr['elm']) ? $meta_attr['elm'] : 'h3') . '>';
        return $html;
    }
}

