<?php

defined('ABSPATH') || exit;

class ILGL_Helper {

    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function render_ad_slot($ads, $slot_name) {
        if (!empty($ads[$slot_name])) {
            echo '<div class="prep-link-abn prep-link-abn-' . esc_attr($slot_name) . '">' . $ads[$slot_name] . '</div>';
        }
    }

    public function param_url() {
        $endpoint = 'link';
        $ep_settings = ilgl_settings()->ep_settings();

        if (!empty($ep_settings['endpoint'])) {
            $endpoint = preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim($ep_settings['endpoint']));
        }

        return $endpoint;
    }

    public function exclude_elm() {
        return ilgl_settings()->exclude_elm();
    }

    public function allow_domain() {
        return ilgl_settings()->allow_domain();
    }

    public function is_plugin_enable() {
        return ilgl_settings()->is_plugin_enable();
    }

    public function get_list_link($post_id, $ep_settings) {
        $list_link = get_post_meta($post_id, 'link-download-metabox', true);

        if (empty($list_link) || !is_array($list_link) || !array_filter($list_link)) {
            return;
        }

        $total = (int) ($ep_settings['field_lists'] ?? 5);
        $is_logged_in = is_user_logged_in();

        $settings = ilgl_settings()->global_settings();
        $enable_rewrite = isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes';

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

            if ($enable_rewrite) {
                $link = ilgl_settings()->encrypt_url($link);
            }

            $display_text = esc_html($file_name . ' ' . $size);

            echo '<a href="#" rel="nofollow" data-request="' . esc_attr($link) . '" class="preplink-btn-link list-preplink-btn-link">' . $display_text . '</a>';
        }

        echo '</div>';
    }

    public function link_render($isMeta, $link_is_login, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id, $ep_settings) {
        $is_logged_in = is_user_logged_in();
        $settings = ilgl_settings()->global_settings();
        $enable_rewrite = isset($settings['enable_rewrite']) && $settings['enable_rewrite'] === 'yes';

        if ($enable_rewrite) {
            $link_is_login = esc_attr(ilgl_settings()->encrypt_url($link_is_login));
            $link_no_login = esc_attr(ilgl_settings()->encrypt_url($link_no_login));
        }

        if ($isMeta) {
            $link = $is_logged_in ? $link_is_login : $link_no_login;
            $display_text = esc_html($file_name . ' ' . $file_size);
        } else {
            $link = esc_attr($prepLinkURL);
            $display_text = esc_html($prepLinkText);
        }
        ?>
        <a href="#" rel="nofollow" data-request="<?= esc_attr($link); ?>" class="preplink-btn-link"><?= $display_text ?></a>
        <?php
        if ($isMeta) {
            $this->get_list_link($post_id, $ep_settings);
        }
    }

    public function svg_render() { ?>
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

    public function ep_related_post($ep_settings, $post_id) {
        $categories = get_the_category();
        if (empty($categories)) {
            return;
        }

        $category_ids = wp_list_pluck($categories, 'term_id');
        $posts_per_page = !empty($ep_settings['preplink_related_number']) ? (int) $ep_settings['preplink_related_number'] : 4;

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

    public function faq_render() {
        $faq_conf = get_option('preplink_faq', []);

        $title = !empty($faq_conf['faq_title']) ? $faq_conf['faq_title'] : __('Frequently Asked Questions', 'intelligent-link');

        $description = !empty($faq_conf['faq_description'])
                ? $faq_conf['faq_description']
                : file_get_contents(plugin_dir_path(__DIR__) . 'faq.txt');
        ?>
        <div class="faq-download">
            <h3 class="faq-title"><?= esc_html($title) ?></h3>
            <?= $description ?>
        </div>
        <?php
    }

    public function set_no_index_page() {
        $robots_config = [
            'follow' => true,
            'noindex' => true,
        ];

        if (!function_exists('aioseo') && !function_exists('wpseo_init') && !function_exists('rank_math')) {
            add_filter('wp_robots', function() use ($robots_config) {
                return $robots_config;
            });
            return;
        }

        $seo_robots = [
                'follow' => 'follow',
                'index' => 'noindex',
        ];

        if (function_exists('rank_math')) {
            add_filter('rank_math/frontend/robots', function() use ($seo_robots) {
                return $seo_robots;
            });
        }

        if (function_exists('wpseo_init')) {
            add_filter('wpseo_robots', function() use ($seo_robots) {
                return 'noindex, follow';
            });
        }

        if (function_exists('aioseo')) {
            add_filter('aioseo_robots_meta', function() use ($seo_robots) {
                return $seo_robots;
            });
        }
    }
}

ILGL_Helper::get_instance();

function ilgl_helper() {
    return ILGL_Helper::get_instance();
}