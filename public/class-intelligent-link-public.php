<?php

/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

class Intelligent_Link_Public {

    public function __construct(){
        add_action('init', array($this, 'preplink_rewrite_endpoint'), 10, 0);
        add_filter('the_content', array($this, 'render_meta_link_info'), 10);
        add_filter('the_content', array($this, 'render_meta_link_info_bottom'), 10);
        add_action('woocommerce_short_description', array($this,'render_meta_short_description'), 10);
        add_action('wp_head', array($this, 'add_canonical_tag'), 10);
        add_shortcode('link_shortcode', array($this,'link_shortcode_callback'));
    }

    public function enqueue_styles(){
        if (is_plugin_enable()){
            wp_enqueue_style('intelligent-link', plugin_dir_url(__FILE__) . 'css/intelligent-link'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.css', array(), INTELLIGENT_LINK_VERSION, 'all');
        }
    }

    public function enqueue_scripts() {
        if (is_plugin_enable()){
            wp_enqueue_script('wp-i18n', includes_url('/js/dist/i18n.js'), array('wp-element'), '1.0', true);
            wp_enqueue_script('intelligent-link', plugin_dir_url(__FILE__) . 'js/intelligent-link'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.js', array('jquery'), INTELLIGENT_LINK_VERSION, true);

            $href_vars = [];
            $href_vars = apply_filters('ilgl_href_vars', $href_vars);
            wp_localize_script('intelligent-link', 'href_vars', array_merge(
                [
                    'end_point'              => endpoint_conf(),
                    'modify_conf'            => modify_conf(),
                    'prep_url'               => $this->allow_domain(),
                    'pre_elm_exclude'        => $this->exclude_elm(),
                    'encrypt_url'            => !empty(ilgl_settings()['encrypt_url']) ? ilgl_settings()['encrypt_url'] : 0,
                    'nofollow'               => !empty(ilgl_settings()['nofollow']) ? ilgl_settings()['nofollow'] : 0,
                    'cookie_time'            => !empty(ep_settings()['cookie_time']) ? ep_settings()['cookie_time'] : 15,
                    'count_down'             => !empty(ilgl_settings()['preplink_countdown']) ? ilgl_settings()['preplink_countdown'] : 0
                ],
                $href_vars
            ));
        }
    }

    public function preplink_rewrite_endpoint(){
        if (is_plugin_enable()){
            add_rewrite_endpoint(endpoint_conf(), EP_PERMALINK | EP_PAGES | EP_ROOT | EP_CATEGORIES | EP_SEARCH);
            add_filter('template_include', [$this, 'intelligent_link_template_include']);
            if (INTELLIGENT_LINK_DEV == 1) {
                flush_rewrite_rules();
            }
        }
    }

    public function prep_head() {
        wp_enqueue_style('ilgl-template', plugin_dir_url(__FILE__) . 'css/template'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.css', [], INTELLIGENT_LINK_VERSION, 'all');
        wp_enqueue_script('ilgl-template', plugin_dir_url(__FILE__) . 'js/template'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.js', array('jquery'), INTELLIGENT_LINK_VERSION, false);

        $prep_template = [];
        $prep_template = apply_filters('ilgl_prep_template_vars', $prep_template);
        wp_localize_script('ilgl-template', 'prep_template', array_merge(
            [
                'end_point'     => endpoint_conf(),
                'modify_conf'   => modify_conf(),
                'countdown'     => !empty(ep_settings()['countdown_endpoint']) ? ep_settings()['countdown_endpoint'] : 5
            ],
            $prep_template
        ));
    }

    public function add_canonical_tag() {
        $current_url = home_url( add_query_arg( null, null ) );
        if ( strpos( $current_url, '/?'.endpoint_conf().'=' ) !== false ) {
            echo '<link rel="canonical" href="' . esc_url( get_permalink()) . '" />';
        }
    }

    public function intelligent_link_template_include($template) {
        global $wp_query;

        $intelligent_link_template = apply_filters('intelligent_link_template', '');

        if (empty($intelligent_link_template)) {
            $intelligent_link_template = dirname( __FILE__ ) . '/templates/default.php';
        }

        include_once plugin_dir_path(INTELLIGENT_LINK_PLUGIN_FILE) . 'includes/class-intelligent-link-template.php';

        $current_url = home_url( $_SERVER['REQUEST_URI'] );
        $endpoint = '?'.endpoint_conf().'=1';

        if(strpos($current_url, $endpoint) !== false)  {
            $this->prep_head();
            if (is_singular('product')) {
                remove_all_actions( 'woocommerce_single_product_summary' );
                include_once $intelligent_link_template;
                exit;
            }
            return $intelligent_link_template;
        }

        $product_category = isset($wp_query->query_vars['product_cat']) ? $wp_query->query_vars['product_cat']: '';

        if ($product_category == endpoint_conf()) {
            $this->prep_head();
            add_filter( 'pre_get_document_title', function ($title) {
                if (empty(get_the_title()) || empty($title)) {
                    return isset($_COOKIE['prep_title']) ? $_COOKIE['prep_title'] . ' – ' . get_bloginfo('name') : get_bloginfo('name');
                }
                return $title;
            });

            remove_all_actions('woocommerce_before_main_content');
            remove_all_actions('woocommerce_archive_description');
            remove_all_actions('woocommerce_before_shop_loop');
            remove_all_actions('woocommerce_shop_loop');
            remove_all_actions('woocommerce_after_shop_loop');
            remove_all_actions('woocommerce_sidebar');

            include_once $intelligent_link_template;
            exit;
        }


        return $template;
    }
    

    public function exclude_elm(){
        $excludeList = ilgl_settings()['preplink_excludes_element'];

        if (!empty($excludeList)) {
            $excludesArr = explode(',', $excludeList);
            $excludesArr = array_map('trim', $excludesArr);
            $excludesArr = array_merge($excludesArr, ['.prep-link-download-btn', '.prep-link-btn', '.keyword-search', '.comment', '.session-expired','.list-link-redirect','.preplink-btn-link']);
            $excludesArr = array_unique($excludesArr);
            $excludeList = implode(',', $excludesArr);
        } else {
            $excludeList = '.prep-link-download-btn,.prep-link-btn,.keyword-search,.session-expired,.comment,.preplink-btn-link';
        }
        return $excludeList;
    }

    public function allow_domain(){
        $allow_domain = '';
        $prepList = ilgl_settings()['preplink_url'];
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
        $file_format = get_post_meta(get_the_ID(), 'file_format', true);
        $file_version = get_post_meta(get_the_ID(), 'file_version', true);
        $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
        $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);
        if ($file_name && $link_is_login && $link_no_login && is_plugin_enable()) {
            $after_description = isset(ilgl_meta_option()['product_elm'])? ilgl_meta_option()['product_elm'] == 'after_short_description': '';
            $html = $this->prep_link_html($file_name, $file_format, $file_version);
            if (!empty(get_the_excerpt()) && $after_description) {
                return $content. $html;
            }
        }

        return $content;
    }

    public function render_meta_link_info($content) {
        if (!is_admin() && is_plugin_enable() && !has_shortcode($content, 'link_shortcode')) {
            $post_id = get_the_ID();
            $show_metabox = get_post_meta($post_id, 'show_metabox', true);
            if ($show_metabox !== 'hide') {
                $file_name = get_post_meta($post_id, 'file_name', true);
                $link_no_login = get_post_meta($post_id, 'link_no_login', true);
                $link_is_login = get_post_meta($post_id, 'link_is_login', true);

                if ($file_name && $link_is_login && $link_no_login && is_singular('post')) {
                    $content = $this->top_html($file_name).$content;
                }
            }
        }

        return $content;
    }

    public function top_html($file_name) {
        $post_id = get_the_ID();

        if (!$post_id) {
            return '';
        }

        $file_size = get_post_meta($post_id, 'file_size', true);
        $file_size = str_replace(['(', ')'], '', $file_size);
        $require   = get_post_meta($post_id, 'require', true);
        $file_version = get_post_meta($post_id, 'file_version', true);
        $mod_feature  = get_post_meta($post_id, 'mod_feature', true);
        $os_version   = get_post_meta($post_id, 'os_version', true);
        $file_format = get_post_meta($post_id, 'file_format', true);
        $modified_date = get_post_modified_time( 'F j, Y', false, get_the_ID() );
        $thumbnail_url = get_the_post_thumbnail_url($post_id, [150, 150]);


        $valid_formats = ['ipa', 'apk', 'xapk'];
        if (in_array(strtolower($file_format), $valid_formats)) {
            $feature = 'MOD Feature';
        } else {
            $feature = 'License';
        }

        $file_name = str_replace(['_', '-','.apk', '.xapk', '.ipa', '.zip', '.rar', '(', ')', '  ', 'Mediafire', 'Google Drive'], ' ', $file_name);
        $file_name = preg_replace('/\bv\d+(\.\d+)+\b/', '', $file_name);
        $file_name = trim($file_name);

        $html = <<<HTML
<div class="meta-attr-top">
    <div class="left-attr">
        <p class="img-attr"><img src="{$thumbnail_url}" alt="{$file_name}"></p>
        <a class="link-trg" href="#igl-download-now"><i class="fa-solid fa-circle-arrow-down"></i> DOWNLOAD {$file_format}</a>
        <a class="link-fb" href="https://www.facebook.com/arriveddev"><i class="fa-brands fa-facebook"></i> Facebook</a>
        <p><i class="fas fa-info-circle"></i><a style="text-decoration: none" href="https://arriveddev.com/contact-us/"> Report</a></p>
    </div>
    <div class="right-attr">
        <ul class="ul-left-attr">
            <li class="file_name"><strong style="text-transform: capitalize;">{$file_name}</strong></li>
            <li class="file_size">Size: <span>{$file_size}</span></li>
            <li class="file_version">Version: <span>{$file_version}</span></li>
            <li class="file_require">Requirements: <span>{$require} {$os_version}</span></li>
            <li class="mod_feature">{$feature}: <span style="color: #0A6627;font-weight: 600;">{$mod_feature}</span></li>
            <li class="modified_date">Update: <span>{$modified_date}</span></li>
            <li class="security">Security: <span>Verified Safe </span><i class="far fa-check-circle" style="color: #069d10;"></i></li>
        </ul>
    </div>
</div>
HTML;
        return $html;
    }

    public function render_meta_link_info_bottom($content) {
        if (!is_admin() && is_plugin_enable() && !has_shortcode($content, 'link_shortcode')) {
            $file_name = get_post_meta(get_the_ID(), 'file_name', true);
            $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
            $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);
            $file_format = get_post_meta(get_the_ID(), 'file_format', true);
            $file_version = get_post_meta(get_the_ID(), 'file_version', true);

            if ($file_name && $link_is_login && $link_no_login) {
                $product_elm_after_content = isset(ilgl_meta_option()['product_elm']) && ilgl_meta_option()['product_elm'] == 'after_product_content';
                $html = $this->prep_link_html($file_name, $file_format, $file_version);
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
        }

        return $content;
    }

    public function prep_link_html($file_name, $file_format, $file_version) {
        $html = '';
        $element = get_post_meta(get_the_ID(), 'render_element', true);
        $replace_format = get_post_meta(get_the_ID(), 'format_render', true);
        $wrap = !empty($element['wrap_element']) ? $element['wrap_element'] : 'div';
        $pre_fix = !empty($element['pre_fix']) ? $element['pre_fix'] .'&nbsp;' : '';
        $permalink = get_post_field('post_name', get_the_ID());

        $file_version = !empty($file_version) ? ' v'.$file_version : '';
        $file_title = $file_name. $file_version. '.'. $file_format;

        if ($replace_format === 'file') {
            $file_title = $file_name;
        }

        $nofollow = !empty(ilgl_settings()['nofollow']) ? 1: 0;

        $html .= '<' . $wrap . ' id="igl-download-now" class="igl-download-now"><b class="b-h-down">' . $pre_fix . '</b>';
        $html .= '<a ' . ($nofollow ? 'rel="nofollow"' : '') . ' class="prep-request" data-meta="1" href="/' . $permalink . '/?' . endpoint_conf() . '=1"><span>' . $file_title . '</span></a>';
        $html .= '</' . $wrap . '>';

        return $html;
    }

    public function link_shortcode_callback($file_format, $file_version) {
        $file_name = get_post_meta(get_the_ID(), 'file_name', true);
        $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
        $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);
        if ($file_name && $link_is_login && $link_no_login) {
           $html = $this->prep_link_html($file_name, $file_format, $file_version);
        }
        return !empty($html) ? $html : '';
    }
}

