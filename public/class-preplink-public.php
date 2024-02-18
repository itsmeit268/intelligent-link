<?php

/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

class Preplink_Public {

    /**
     * The ID of this plugin.
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * @var false|mixed|void
     */
    protected $settings;

    /**
     * @var false|mixed|void
     */
    protected $preplink;

    /**
     * Preplink_Public constructor.
     * @param $plugin_name
     * @param $version
     */
    public function __construct($plugin_name, $version){
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->settings    = get_option('preplink_setting');
        $this->preplink    = get_option('preplink_endpoint');
        add_action('init', array($this, 'preplink_rewrite_endpoint'), 10, 0);
        add_action('wp_head', array($this, 'add_prep_custom_styles'), 10, 2);
        add_filter('the_content', array($this, 'render_link_info'), 10);
    }

    public function enqueue_styles(){
        if (!is_front_page() && is_singular('post') && $this->is_plugin_enable()){
            wp_enqueue_style('prep-global', plugin_dir_url(__FILE__) . 'css/global.css', array(), $this->version, 'all');
        }
    }

    public function enqueue_scripts() {
        $endpoint = $this->getEndPointValue();
        wp_enqueue_script('prep-cookie', plugin_dir_url(__FILE__) . 'js/cookie.js', array('jquery'), $this->version, false);
        wp_localize_script('prep-cookie', 'cookie_vars', ['end_point' => $endpoint]);

        if ($this->is_plugin_enable() && isset($this->preplink['endpoint'])){

            $settings = get_option('email_marketing_settings', []);
            wp_enqueue_script('wp-i18n', includes_url('/js/dist/i18n.js'), array('wp-element'), '1.0', true);
            wp_enqueue_script('preplink-global', plugin_dir_url(__FILE__) . 'js/global.js', array('jquery'), $this->version, true);
            wp_localize_script('preplink-global', 'href_proccess', [
                'end_point'              => $endpoint,
                'prep_url'               => $this->getPrepLinkUrls(),
                'pre_elm_exclude'        => $this->getExcludedElements(),
                'count_down'             => !empty($this->settings['preplink_countdown']) ? $this->settings['preplink_countdown'] : 0,
                'cookie_time'            => !empty($this->preplink['cookie_time']) ? $this->preplink['cookie_time'] : 5,
                'display_mode'           => !empty($this->settings['preplink_wait_text']) ? $this->settings['preplink_wait_text'] : 'wait_time',
                'wait_text'              => !empty($this->settings['wait_text_replace']) ? $this->settings['wait_text_replace'] : 'waiting',
                'auto_direct'            => !empty($this->settings['preplink_auto_direct']) ? $this->settings['preplink_auto_direct'] : 0,
                'text_complete'          => !empty($this->settings['preplink_text_complete']) ? $this->settings['preplink_text_complete'] : 'Link ready!',
                'links_noindex_nofollow' => $this->get_links_nofolow_noindex(),
                'is_user_logged_in'      => is_user_logged_in(),
                'is_popup'               => isset($settings['disable_click_popup']) ? $settings['disable_click_popup'] : '0',
                'remix_url'              => ['prefix'  => 'df5c1kjdhsf81', 'mix_str' => 'gVmk2mf9823c2', 'suffix'  => 'cgy73mfuvkjs3']
            ]);
        }
    }

    public function preplink_rewrite_endpoint(){
        if ($this->is_plugin_enable()){
            add_rewrite_endpoint($this->getEndPointValue(), EP_ALL );
            add_filter('template_include', [$this, 'preplink_template_include']);
            flush_rewrite_rules();
        }
    }

    public function preplink_template_include($template) {
        global $wp_query;
        if (isset($wp_query->query_vars[$this->getEndPointValue()])) {
            $this->set_robots_filter();

            wp_enqueue_style('prep-template', plugin_dir_url(__FILE__) . 'css/template.css', [], PREPLINK_VERSION, 'all');
            wp_enqueue_script('prep-template', plugin_dir_url(__FILE__) . 'js/template.js', array('jquery'), PREPLINK_VERSION, false);
            wp_localize_script('prep-template', 'prep_template', [
                'countdown_endpoint'     => !empty($this->preplink['countdown_endpoint']) ? $this->preplink['countdown_endpoint'] : 5,
                'endpoint_direct'        => !empty($this->preplink['endpoint_auto_direct']) ? $this->preplink['endpoint_auto_direct'] : 0,
                'remix_url'              => ['prefix'  => 'df5c1kjdhsf81', 'mix_str' => 'gVmk2mf9823c2', 'suffix'  => 'cgy73mfuvkjs3'],
                'is_user_logged_in'      => is_user_logged_in(),
            ]);

            return dirname( __FILE__ ) . '/templates/default.php';
        }
        return $template;
    }

    /**
     * @return mixed|string
     */
    public function getEndPointValue(){
        $endpoint = 'download';
        if (!empty($this->preplink['endpoint'])) {
            $endpoint = preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim($this->preplink['endpoint']));
        }
        return $endpoint;
    }

    public function add_prep_custom_styles(){
        if ($this->is_plugin_enable() && !empty($this->settings['preplink_custom_style'])) {
            ?>
            <style><?= $this->settings['preplink_custom_style'] ?></style>
            <?php
        }
    }

    public function set_robots_filter(){

        if (!function_exists('aioseo' ) && !function_exists('wpseo_init' ) && !function_exists('rank_math' )) {
            $robots['noindex'] = true;
            $robots['nofollow'] = true;
            add_filter('wp_robots', function() use ($robots) {
                return $robots;
            });
        }

        $robots = array(
            'index' => 'noindex', 'follow' => 'nofollow',
            'archive' => 'noarchive', 'snippet' => 'nosnippet',
        );

        if (function_exists('rank_math' )){
            add_filter( 'rank_math/frontend/robots', function() use ($robots) {
                return $robots;
            });
        }

        if (function_exists('wpseo_init' )){
            add_filter( 'wpseo_robots', function() use ($robots) {
                return $robots;
            });
        }

        if (function_exists('aioseo' )){
            add_filter( 'aioseo_robots_meta', function() use ($robots) {
                return $robots;
            });
        }
    }

    public function is_plugin_enable(){
        return !empty($this->settings['preplink_enable_plugin']) && (int)$this->settings['preplink_enable_plugin'] == 1;
    }

    public function getExcludedElements(){
        $excludeList = $this->settings['preplink_excludes_element'];

        if (!empty($excludeList)) {
            $excludesArr = explode(',', $excludeList);
            $excludesArr = array_map('trim', $excludesArr);
            $excludesArr = array_merge($excludesArr, ['.prep-link-download-btn', '.prep-link-btn']);
            $excludesArr = array_unique($excludesArr);
            $excludeList = implode(',', $excludesArr);
        } else {
            $excludeList = '.prep-link-download-btn,.prep-link-btn';
        }
        return $excludeList;
    }

    public function get_links_nofolow_noindex(){
        $noindex_domain = $this->settings['links_noindex_nofollow'];

        if (!empty($noindex_domain)) {
            $excludesArr = explode(',', $noindex_domain);
            $excludesArr = array_map('trim', $excludesArr);
            $excludesArr = array_unique($excludesArr);
            $noindex_domain = implode(',', $excludesArr);
            $noindex_domain = rtrim(ltrim($noindex_domain, ','), ',');
        } else {
            $noindex_domain = '';
        }

        return $noindex_domain;
    }

    public function getPrepLinkUrls(){
        $prepList = $this->settings['preplink_url'];
        if (!empty($prepList)) {

            $prepArr = explode(',', $prepList);
            $prepArr = array_map('trim', $prepArr);

            $lastIndex = count($prepArr) - 1;
            if (empty($prepArr[$lastIndex])) {
                unset($prepArr[$lastIndex]);
            }

            $prepArr = array_merge($prepArr, ['fshare.vn', 'drive.google.com']);
            $prepArr = array_unique($prepArr);
            $prepList = implode(',', $prepArr);
        } else {
            $prepList = 'fshare.vn, drive.google.com';
        }

        return $prepList;
    }

    public function render_link_info($content) {
        $post_id = get_the_ID();
        $file_name = get_post_meta($post_id, 'file_name', true);
        $link_no_login = get_post_meta($post_id, 'link_no_login', true);
        $link_is_login = get_post_meta($post_id, 'link_is_login', true);

        if (is_singular('post') && $file_name && $link_is_login && $link_no_login) {
            $html = $this->prep_link_html($file_name);

            $last_p = strrpos($content, '</p>');
            if ($last_p !== false) {
                $content = substr_replace($content, $html, $last_p + 4, 0);
            }
        }
        return $content;
    }

    public function prep_link_html($file_name) {
        $blog_url = base64_encode(get_bloginfo('url'));
        $display_mode = !empty($this->settings['preplink_wait_text']) ? $this->settings['preplink_wait_text'] : 'wait_time';

        $html = '<h3 class="wp-block-heading" id="download-now"><b>Link download: </b>';

        if (is_user_logged_in()) {
            $display_mode = 'progress';
        }

        if ($display_mode === 'progress') {
            $html .= '<div class="post-progress-bar">';
            $html .= '<span id="prep-request" data-id="' . $blog_url . '"><strong class="post-progress">' . $file_name . '</strong></span></div>';
        } else {
            $html .= '<span class="wrap-countdown">';
            $html .= '<span id="prep-request" data-id="' . $blog_url . '"><strong class="link-countdown">' . $file_name . '</strong></span></span>';
        }

        $html .= '</h3>';
        return $html;
    }
}