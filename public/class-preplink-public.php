<?php

/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

class Preplink_Public
{

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
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->settings    = get_option('preplink_setting');
        $this->preplink    = get_option('preplink_endpoint');
        add_action('init', array($this, 'preplink_rewrite_endpoint'), 10, 0);
        add_action('wp_head', array($this, 'add_prep_custom_styles'), 10, 2);
    }

    public function enqueue_styles()
    {
        if (!is_front_page() && is_singular('post') && $this->is_plugin_enable()){
            wp_enqueue_style('preplink-global', plugin_dir_url(__FILE__) . 'css/preplink-global.css', array(), $this->version, 'all');
        }
    }

    public function enqueue_scripts()
    {
        if (!is_front_page() && is_singular('post')) {
            $endpoint = $this->getEndPointValue();
            if ($this->is_plugin_enable() && isset($this->preplink['endpoint'])){
                wp_enqueue_script('preplink-global', plugin_dir_url(__FILE__) . 'js/preplink-global.js', array('jquery'), $this->version, false);
                wp_localize_script('preplink-global', 'prep_vars', array(
                    'end_point'           => $endpoint,
                    'pre_elm_exclude'     => $this->getExcludedElements(),
                    'prep_url'            => $this->getPrepLinkUrls(),
                    'count_down'          => !empty($this->settings['preplink_countdown']) ? $this->settings['preplink_countdown'] : 0,
                    'cookie_time'         => !empty($this->preplink['cookie_time']) ? $this->preplink['cookie_time'] : 5,
                    'countdown_endpoint'  => !empty($this->preplink['countdown_endpoint']) ? $this->preplink['countdown_endpoint'] : 5,
                    'display_mode'        => !empty($this->settings['preplink_wait_text']) ? $this->settings['preplink_wait_text'] : 'wait_time',
                    'wait_text'           => !empty($this->settings['wait_text_replace']) ? $this->settings['wait_text_replace'] : 'waiting',
                    'auto_direct'         => !empty($this->settings['preplink_auto_direct']) ? $this->settings['preplink_auto_direct'] : 0,
                    'endpoint_direct'     => !empty($this->preplink['endpoint_auto_direct']) ? $this->preplink['endpoint_auto_direct'] : 0,
                    'text_complete'       => !empty($this->settings['preplink_text_complete']) ? $this->settings['preplink_text_complete'] : '[Link ready!]'
                ));

                global $wp_query;
                if (isset($wp_query->query_vars[$endpoint])) {
                    wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/preplink-endpoint.js', array('jquery'), $this->version, false);
                    wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/preplink-endpoint.css', array(), $this->version, 'all');
                }
            }
        }
    }

    public function preplink_rewrite_endpoint()
    {
        if ($this->is_plugin_enable()){
            add_rewrite_endpoint($this->getEndPointValue(), EP_PERMALINK | EP_PAGES );

            add_filter('template_include', function($template) {
                global $wp_query;
                if (isset($wp_query->query_vars[$this->getEndPointValue()]) && is_singular('post')) {
                    $this->set_robots_filter();
                    return dirname( __FILE__ ) . '/templates/preplink_template.php';
                }
                return $template;
            });
        }
    }

    /**
     * @return mixed|string
     */
    public function getEndPointValue()
    {
        $endpoint = 'download';
        if (!empty($this->preplink['endpoint'])) {
            $endpoint = preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim($this->preplink['endpoint']));
        }
        return $endpoint;
    }

    public function add_prep_custom_styles()
    {
        if ($this->is_plugin_enable() && !empty($this->settings['preplink_custom_style'])) {
            ?>
            <style><?= $this->settings['preplink_custom_style'] ?></style>
            <?php
        }
    }

    public function set_robots_filter() {
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

    public function getExcludedElements()
    {
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


    public function getPrepLinkUrls()
    {
        $prepList = $this->settings['preplink_url'];

        if (!empty($prepList)) {
            $prepArr = explode(',', $prepList);
            $prepArr = array_map('trim', $prepArr);
            $prepArr = array_merge($prepArr, ['drive.google.com', 'play.google.com']);
            $prepArr = array_unique($prepArr);
            $prepList = implode(',', $prepArr);
        } else {
            $prepList = 'play.google.com,drive.google.com';
        }
        return $prepList;
    }
}