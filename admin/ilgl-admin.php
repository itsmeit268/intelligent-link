<?php

defined('ABSPATH') || exit;

class ILGL_Admin {

    private static $instance = null;

    public function __construct(){
        $this->load_form_template();
        add_action('admin_menu', array($this, 'add_prep_link_admin_menu'), 10);
        add_action('admin_init', array($this, 'register_and_build_fields'));
        add_action('plugin_action_links_' . INTELLIGENT_LINK_PLUGIN_BASE, array($this, 'add_plugin_action_link'), 20);
        add_action('add_meta_boxes', array($this, 'add_html_field_content'), 22);
        add_action('save_post', array($this, 'save_html_field_content'), 20);
        add_action('before_delete_post', array($this, 'delete_links_filed'), 20, 1);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

   private function load_form_template() {
        if (!class_exists('Form_Html')) {
            require_once plugin_dir_path( __FILE__ ) . 'templates/ilgl-form.php';
        }
    }

    public function enqueue_scripts(){
        wp_enqueue_style('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'admin/css/intelligent-link.css', array(), INTELLIGENT_LINK_VERSION, 'all');
        wp_enqueue_script('intelligent-link', INTELLIGENT_LINK_PLUGIN_URL . 'admin/js/intelligent-link.js', array('wp-i18n'), INTELLIGENT_LINK_VERSION, false);
    }

    public function add_prep_link_admin_menu(){
        add_menu_page(
                __(INTELLIGENT_LINK_NAME. ' Settings', 'intelligent-link'),
                __(INTELLIGENT_LINK_NAME, 'intelligent-link'),
                'manage_options',
                'intelligent-link-settings',
                [$this, 'prep_link_admin_form_settings'],
                'dashicons-admin-links',
                90
        );
    }

    public function prep_link_admin_form_settings(){

        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

        $tabs = array(
                'general'   => __( 'General', 'intelligent-link' ),
                'meta_attr' => __( 'Post links', 'intelligent-link' ),
                'advertising'  => __( 'Advertising', 'intelligent-link' ),
                'faq' => __( 'FAQ', 'intelligent-link' ),
                'endpoint' => __( 'Endpoint', 'intelligent-link' )
        );

        Form_Html::render_tabs($tabs, $active_tab);

        switch ($active_tab) {
            case 'general':
                Form_Html::render_general_settings();
                break;
            case 'meta_attr':
                Form_Html::render_meta_attr_settings();
                break;
            case 'advertising':
                Form_Html::render_advertising_settings();
                break;
            case 'faq':
                Form_Html::render_faq_settings();
                break;
            case 'endpoint':
                Form_Html::render_endpoint_settings();
                break;
        }
    }

    public function add_plugin_action_link($links){
        $setting_link = '<a href="' . esc_url(get_admin_url()) . 'admin.php?page=intelligent-link-settings">' . __('Settings', 'intelligent-link') . '</a>';
        array_unshift($links, $setting_link);
        return $links;
    }

    public function register_and_build_fields(){
        add_settings_section( 'preplink_general_section', '', array($this, 'preplink_display_general'), 'preplink_general_settings' );
        add_settings_section( 'preplink_meta_attr_section', '', array($this, 'preplink_meta_display'), 'preplink_meta_attr' );
        add_settings_section( 'ads_code_section', '', array($this, 'ads_code_display'), 'ads_code_settings' );
        add_settings_section( 'preplink_faq_section', '', array($this, 'preplink_faq_display'), 'preplink_faq_settings' );
        add_settings_section( 'preplink_endpoint_section', '', array($this, 'preplink_endpoint_display'), 'preplink_endpoint_settings' );
        $this->register_settings_fields();
        $this->register_settings();
    }

    private function register_settings_fields(){

        // General Settings Fields
        add_settings_field(
                'preplink_enable_plugin',
                __('Enable/Disable', 'intelligent-link'),
                array('Form_Html', 'preplink_enable_plugin'),
                'preplink_general_settings',
                'preplink_general_section',
                array(1 => 'Enabled', 0 => 'Disabled')
        );

        add_settings_field(
                'preplink_textarea',
                __('Domain / subdomain', 'intelligent-link'),
                array('Form_Html', 'preplink_textarea_field'),
                'preplink_general_settings',
                'preplink_general_section'
        );

        add_settings_field(
                'preplink_excludes_element',
                __('Element excluded', 'intelligent-link'),
                array('Form_Html', 'preplink_excludes_element'),
                'preplink_general_settings',
                'preplink_general_section'
        );

        add_settings_field(
                'preplink_display_mode',
                __('Display Mode', 'intelligent-link'),
                array('Form_Html', 'preplink_display_mode'),
                'preplink_general_settings',
                'preplink_general_section',
                array('wait_time' => 'Countdown', 'progress' => 'ProgressBar')
        );

        add_settings_field(
                'replace_text_complete',
                __('Replace text after complete', 'intelligent-link'),
                array('Form_Html', 'replace_text_complete'),
                'preplink_general_settings',
                'preplink_general_section',
                array('yes' => 'Replace', 'no' => 'No replace')
        );

        add_settings_field(
                'preplink_auto_direct',
                __('Automatic redirection', 'intelligent-link'),
                array('Form_Html', 'preplink_post_auto_direct'),
                'preplink_general_settings',
                'preplink_general_section',
                array(1 => 'Yes', 0 => 'No')
        );

        add_settings_field(
                'preplink_link_url_rewriting',
                __('Rewrite URL Encoding', 'intelligent-link'),
                array('Form_Html', 'preplink_link_url_rewriting'),
                'preplink_general_settings',
                'preplink_general_section'
        );

        add_settings_field(
                'preplink_delete_option',
                __('Delete all data after remove plugin', 'intelligent-link'),
                array('Form_Html', 'preplink_delete_option_on_uninstall'),
                'preplink_general_settings',
                'preplink_general_section'
        );

        // Meta Attribute Fields
        add_settings_field(
                'meta_attr_auto_direct',
                __('Automatic redirection', 'intelligent-link'),
                array('Form_Html', 'meta_attr_auto_direct'),
                'preplink_meta_attr',
                'preplink_meta_attr_section',
                array(1 => 'Yes', 0 => 'No')
        );

        add_settings_field(
                'preplink_link_field_lists',
                __('Number link list', 'intelligent-link'),
                array('Form_Html', 'preplink_link_field_lists'),
                'preplink_meta_attr',
                'preplink_meta_attr_section'
        );

        add_settings_field(
                'meta_elm_option',
                __('Render Element', 'intelligent-link'),
                array('Form_Html', 'meta_elm_option'),
                'preplink_meta_attr',
                'preplink_meta_attr_section',
                array(
                        'div' => __('div', 'intelligent-link'),
                        'h2' => __('h2', 'intelligent-link'),
                        'h3' => __('h3', 'intelligent-link'),
                        'h4' => __('h4', 'intelligent-link'),
                        'h5' => __('h5', 'intelligent-link'),
                )
        );

        add_settings_field(
                'product_elm_option',
                __('Display position on product page.', 'intelligent-link'),
                array('Form_Html', 'product_elm_option'),
                'preplink_meta_attr',
                'preplink_meta_attr_section',
                array(
                        'after_product_content' => __('After Product Content', 'intelligent-link'),
                        'after_short_description' => __('Short description below', 'intelligent-link'),
                )
        );

        // Endpoint Settings Fields
        add_settings_field(
                'preplink_endpoint',
                __('Endpoint URL string', 'intelligent-link'),
                array('Form_Html', 'preplink_endpoint_field'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section'
        );

        add_settings_field(
                'preplink_cookie_time',
                __('Link expiration time', 'intelligent-link'),
                array('Form_Html', 'preplink_cookie_time'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section'
        );

        add_settings_field(
                'preplink_template',
                __('Display Mode', 'intelligent-link'),
                array('Form_Html', 'enpoint_display_mode'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section',
                array('default' => __('Default', 'intelligent-link'), 'countdown' => __('Countdown', 'intelligent-link'))
        );

        add_settings_field(
                'preplink_endpoint_auto_direct',
                __('Automatic redirection', 'intelligent-link'),
                array('Form_Html', 'preplink_endpoint_auto_direct'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section',
                array(1 => 'Yes', 0 => 'No')
        );

        add_settings_field(
                'preplink_image',
                __('Display Post Image', 'intelligent-link'),
                array('Form_Html', 'preplink_image_field'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section',
                array(1 => 'Yes', 0 => 'No')
        );

        add_settings_field(
                'preplink_related_post',
                __('Display Post Related', 'intelligent-link'),
                array('Form_Html', 'preplink_related_post'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section',
                array(1 => 'Yes', 0 => 'No')
        );

        add_settings_field(
                'preplink_comment',
                __('Display Comment', 'intelligent-link'),
                array('Form_Html', 'preplink_comment'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section',
                array(1 => 'Yes', 0 => 'No')
        );

        add_settings_field(
                'redirect_notice',
                __('Redirection Notice', 'intelligent-link'),
                array('Form_Html', 'redirect_notice'),
                'preplink_endpoint_settings',
                'preplink_endpoint_section'
        );

        // FAQ Settings Fields
        add_settings_field(
                'pr_faq',
                __('FAQ Settings', 'intelligent-link'),
                array('Form_Html', 'pr_faq'),
                'preplink_faq_settings',
                'preplink_faq_section',
                array('label_for' => 'preplink_faq')
        );

        // Advertising Fields
        add_settings_field('pr_ad_1', __('Ads code 1', 'intelligent-link'), array('Form_Html', 'pr_ad_1'), 'ads_code_settings', 'ads_code_section');
        add_settings_field('pr_ad_2', __('Ads code 2', 'intelligent-link'), array('Form_Html', 'pr_ad_2'), 'ads_code_settings', 'ads_code_section');
        add_settings_field('pr_ad_3', __('Ads code 3', 'intelligent-link'), array('Form_Html', 'pr_ad_3'), 'ads_code_settings', 'ads_code_section');
        add_settings_field('pr_ad_4', __('Ads code 4', 'intelligent-link'), array('Form_Html', 'pr_ad_4'), 'ads_code_settings', 'ads_code_section');
        add_settings_field('pr_ad_5', __('Ads code 5', 'intelligent-link'), array('Form_Html', 'pr_ad_5'), 'ads_code_settings', 'ads_code_section');
    }

    private function register_settings(){
        register_setting('preplink_general_settings', 'preplink_setting');
        register_setting('preplink_meta_attr', 'meta_attr');
        register_setting('ads_code_settings', 'ads_code');
        register_setting('preplink_faq_settings', 'preplink_faq');
        register_setting('preplink_endpoint_settings', 'preplink_endpoint');
    }

    // Section display methods
    public function preplink_display_general(){
        Form_Html::preplink_display_general();
    }

    public function preplink_meta_display(){
        Form_Html::preplink_meta_display();
    }

    public function ads_code_display(){
        Form_Html::ads_code_display();
    }

    public function preplink_faq_display(){
        Form_Html::preplink_faq_display();
    }

    public function preplink_endpoint_display(){
        Form_Html::preplink_endpoint_display();
    }

    // Meta box methods
    public function add_html_field_content() {
        add_meta_box( 'link_meta_box', __( 'Intelligent Link (Options)' ), array($this,'link_meta_box_callback'), ['post', 'product'], 'side', 'default' );
    }

    public function link_meta_box_callback($post) {
        Form_Html::link_meta_box_callback($post);
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

        // Lưu các field cố định
        $fields = array(
            'file_name',
            'file_size',
            'link_no_login',
            'link_is_login',
            'file_format',
            'require',
            'os_version',
            'file_version',
            'mod_feature'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Lưu các dynamic fields
        $list_link = [];
        $field_list = ['file_name', 'link_no_login', 'link_is_login', 'size'];

        // Tìm số field tối đa có dữ liệu (tối đa 20)
        $max_fields = 0;
        for ($i = 1; $i <= 20; $i++) {
            $has_data = false;
            foreach ($field_list as $field_name) {
                $meta_key = $field_name . '-' . $i;
                if (isset($_POST[$meta_key]) && !empty(trim($_POST[$meta_key]))) {
                    $has_data = true;
                    break;
                }
            }
            if ($has_data) {
                $max_fields = $i;
            }
        }

        // Lưu dữ liệu các field có thông tin
        for ($i = 1; $i <= $max_fields; $i++) {
            foreach ($field_list as $field_name) {
                $meta_key = $field_name . '-' . $i;
                if (isset($_POST[$meta_key])) {
                    $field_content = sanitize_text_field($_POST[$meta_key]);
                    $list_link[$meta_key] = $field_content;
                }
            }
        }

        update_post_meta($post_id, 'link-download-metabox', $list_link);
        do_action('intelligent_link_save_field_meta_box', $post_id);
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
        do_action('intelligent_link_delete_field_meta_box', $post_id);
    }
}

ILGL_Admin::get_instance();