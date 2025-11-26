<?php

class ILGL_Helper {
    private static $file_exists_cache = [];
    private static $process_link_instance = null;

    public static function get_process_link_instance() {
        if (self::$process_link_instance === null) {
            self::$process_link_instance = Process_Link::get_instance();
        }
        return self::$process_link_instance;
    }

    public static function file_exists_cached($path) {
        if (!isset(self::$file_exists_cache[$path])) {
            self::$file_exists_cache[$path] = file_exists($path);
        }
        return self::$file_exists_cache[$path];
    }
}

function render_back_icon($view_link) { ?>
    <div class="igl-back">
        <a href="<?= esc_url($view_link) ?>">
            <i class="c-svg"><svg width="48" height="20"><use xlink:href="#i__back"></use></svg></i>
            <svg aria-hidden="true" style="display:none;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <symbol id="i__back" viewBox="0 0 48 20">
                        <path fill="currentColor" d="M46.27,9.24a1,1,0,0,0-1.41.1A10.71,10.71,0,0,1,36.78,13h-.16a10.78,10.78,0,0,1-8.1-3.66A12.83,12.83,0,0,0,18.85,5C14.94,5,10.73,7.42,6,12.4V5A1,1,0,0,0,4,5V15a1,1,0,0,0,1,1H15a1,1,0,0,0,0-2H7.24c4.42-4.71,8.22-7,11.62-7A10.71,10.71,0,0,1,27,10.66,12.81,12.81,0,0,0,36.61,15h.18a12.7,12.7,0,0,0,9.58-4.35A1,1,0,0,0,46.27,9.24Z"></path>
                    </symbol>
                </defs>
            </svg>
        </a>
    </div>
<?php }

function get_list_link($post_id, $settings) {
    $list_link = get_post_meta($post_id, 'link-download-metabox', true);


    if (empty($list_link) || !is_array($list_link) || !array_filter($list_link)) {
        return;
    }

    $total = (int) ($settings['field_lists'] ?? 5);
    $process_link = ILGL_Helper::get_process_link_instance();
    $is_logged_in = is_user_logged_in();

    echo '<div class="list-link-redirect">';

    for ($i = 1; $i <= $total; $i++) {
        $file_name = $list_link["file_name-{$i}"] ?? '';
        $link_no_login = $list_link["link_no_login-{$i}"] ?? '';
        $link_is_login = $list_link["link_is_login-{$i}"] ?? '';
        $size = $list_link["size-{$i}"] ?? '';

        if (empty($file_name) || empty($link_no_login) || empty($link_is_login)) {
            continue;
        }

        $link = $is_logged_in ? $link_is_login : $link_no_login;
        $encoded_link = $process_link->modify_list_href(base64_encode($link));
        $display_text = esc_html($file_name . ' ' . $size);

        echo '<a href="javascript:void(0)" data-request="' . esc_attr($encoded_link) . '" class="preplink-btn-link list-preplink-btn-link">' . $display_text . '</a>';
    }

    echo '</div>';
}

function list_link_render($isMeta, $link_is_login, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id, $settings) {
    $is_logged_in = is_user_logged_in();
    $process_link = ILGL_Helper::get_process_link_instance();

    if ($isMeta) {
        $link = $is_logged_in ? $link_is_login : $link_no_login;
        $data_request = esc_attr($process_link->modify_href(base64_encode($link)));
        $display_text = esc_html($file_name . ' ' . $file_size);
    } else {
        $data_request = esc_attr($prepLinkURL);
        $display_text = esc_html($prepLinkText);
    }
    ?>
    <a href="javascript:void(0)" data-request="<?= esc_attr($data_request); ?>" class="preplink-btn-link">
        <?= $display_text ?>
    </a>
    <?php
    if ($isMeta) {
        get_list_link($post_id, $settings);
    }
}

function svg_render() { ?>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 72" width="72" height="72">
        <defs>
            <linearGradient id="timer-gradient" x1="-512.07" y1="-2048.22" x2="-511.51" y2="-2048.45" gradientTransform="matrix(64, 0, 0, -64, 32808.44, -131050.69)" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#656ED6" stop-opacity="0"></stop>
                <stop offset="1" stop-color="#39C1E0"></stop>
            </linearGradient>
            <linearGradient id="timer-gradient-2" x1="-511.51" y1="-2048.25" x2="-512.51" y2="-2048.25" gradientTransform="matrix(64, 0, 0, -64, 32804.44, -131055.69)" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#C867F4"></stop>
                <stop offset="1" stop-color="#E53D2D" stop-opacity="0"></stop>
            </linearGradient>
            <linearGradient id="timer-gradient-3" x1="-511.51" y1="-2048.25" x2="-512.51" y2="-2048.25" gradientTransform="matrix(64, 0, 0, -64, 32800.44, -131047.69)" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#FC7352" stop-opacity="0"></stop>
                <stop offset="1" stop-color="#F0B835"></stop>
            </linearGradient>
        </defs>
        <circle fill="url(#timer-gradient)" cx="40" cy="37" r="32"></circle>
        <circle fill="url(#timer-gradient-2)" cx="36" cy="32" r="32"></circle>
        <circle fill="url(#timer-gradient-3)" cx="32" cy="40" r="32"></circle>
    </svg>
<?php }

function ep_related_post($settings, $post_id) {
    $categories = get_the_category();
    if (empty($categories)) {
        return;
    }

    $category_ids = wp_list_pluck($categories, 'term_id');
    $posts_per_page = !empty($settings['preplink_related_number']) ? (int) $settings['preplink_related_number'] : 4;

    $args = array(
            'category__in' => $category_ids,
            'post__not_in' => array($post_id),
            'posts_per_page' => $posts_per_page,
            'orderby' => 'rand',
            'order' => 'DESC',
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
            'no_found_rows' => true,
    );

    $related_posts = get_posts($args);

    if (empty($related_posts)) {
        return;
    }
    ?>
    <div class="related_post">
        <h3 class="suggestions-post"><?= __('Related Posts', 'intelligent-link') ?></h3>
        <div class="related-posts-grid">
            <?php

            foreach ($related_posts as $post) :
                setup_postdata($post);
                $permalink = get_permalink($post);
                $title = get_the_title($post);
                $thumbnail = has_post_thumbnail($post) ? get_the_post_thumbnail($post, 'thumbnail') : '';
                $post_categories = get_the_category($post->ID);
                ?>
                <div class="related-post">
                    <a class="related-link" href="<?= esc_url($permalink) ?>">
                        <?php if ($thumbnail) : ?>
                            <div class="page_file-img">
                                <?= $thumbnail ?>
                            </div>
                        <?php endif; ?>
                        <div class="related-content">
                            <h5 class="entry-title">
                                <a class="dl-p-url" href="<?= esc_url($permalink) ?>">
                                    <?= esc_html($title) ?>
                                </a>
                            </h5>
                            <?php if (!empty($post_categories)) : ?>
                                <div class="prep-meta">
                                    <span class="prep-category">
                                        <?php
                                        $category_links = array();
                                        foreach ($post_categories as $category) {
                                            $category_links[] = '<a class="category-link" href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                        }
                                        echo implode(' | ', $category_links);
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}

function faq_render() {
    static $faq_content = null;

    if ($faq_content === null) {
        $faq_conf = wp_cache_get('preplink_faq', 'preplink');
        if (false === $faq_conf) {
            $faq_conf = get_option('preplink_faq', []);
            wp_cache_set('preplink_faq', $faq_conf, 'preplink', 3600);
        }

        $title = !empty($faq_conf['faq_title']) ? $faq_conf['faq_title'] : 'Frequently Asked Questions';
        $description = !empty($faq_conf['faq_description']) ? $faq_conf['faq_description'] : file_get_contents(plugin_dir_path(__DIR__) . 'faq.txt');

        $faq_content = array('title' => $title, 'description' => $description);
    }
    ?>
    <div class="faq-download">
        <h3 class="faq-title"><?= esc_html($faq_content['title']) ?></h3>
        <?= $faq_content['description'] ?>
    </div>
    <?php
}

function set_no_index_page() {
    $robots_config = array(
        'nofollow' => true,
         'noindex' => true,
    );

    if (!function_exists('aioseo') && !function_exists('wpseo_init') && !function_exists('rank_math')) {
        add_filter('wp_robots', function() use ($robots_config) {
            return $robots_config;
        });
        return;
    }

    $seo_robots = array(
        'follow' => 'nofollow',
        'index' => 'noindex',
    );

    if (function_exists('rank_math')) {
        add_filter('rank_math/frontend/robots', function() use ($seo_robots) {
            return $seo_robots;
        });
    }

    if (function_exists('wpseo_init')) {
        add_filter('wpseo_robots', function() use ($seo_robots) {
            return $seo_robots;
        });
    }

    if (function_exists('aioseo')) {
        add_filter('aioseo_robots_meta', function() use ($seo_robots) {
            return $seo_robots;
        });
    }
}