<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @since      1.0.0
 * @link       https://github.com/itsmeit268/preplink
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

class Preplink_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Preplink_Public constructor.
     * @param $plugin_name
     * @param $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        add_action('init', array($this, 'preplink_rewrite_endpoint'), 10, 0);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        global $wp_query;
        wp_enqueue_style('global' . $this->plugin_name, plugin_dir_url(__FILE__) . 'css/global.css', array(), $this->version, 'all');

        if (!isset($wp_query->query_vars[$this->getEndPointValue()])) {
            return;
        }

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/prep-link.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        global $wp_query, $post;
        $settings = get_option('preplink_setting');
        if (!empty($settings['preplink_endpoint'])) {
            wp_enqueue_script('global-preplink', plugin_dir_url(__FILE__) . 'js/global.js', array('jquery'), $this->version, false);
            wp_localize_script('global-preplink', 'prep_vars', array(
                'end_point' => $this->getEndPointValue(),
                'prep_url'  => isset($settings['preplink_url']) ? $settings['preplink_url'] : 'drive.google.com,fshare.vn'
            ));

        }

        if (!isset($wp_query->query_vars[$this->getEndPointValue()])) {
            return;
        }
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/prep-link.js', array('jquery'), $this->version, false);
    }

    public function preplink_rewrite_endpoint()
    {
        add_rewrite_endpoint($this->getEndPointValue(), EP_PERMALINK | EP_PAGES );

        function preplink_template() {
            global $wp_query, $post;

            $endpoint = 'download';
            $settings = get_option('preplink_setting');
            if (!empty($settings['preplink_endpoint'])) {
                $endpoint = $settings['preplink_endpoint'];
            }

            if (!isset( $wp_query->query_vars[$endpoint])) {
                return;
            }

            include dirname( __FILE__ ) . '/templates/preplink_template.php';
            exit;
        }

        add_action( 'template_redirect', 'preplink_template');
    }

    public function getEndPointValue()
    {
        $endpoint = 'download';
        $settings = get_option('preplink_setting');
        if (!empty($settings['preplink_endpoint'])) {
            $endpoint = $settings['preplink_endpoint'];
        }

        return $endpoint;
    }
}
