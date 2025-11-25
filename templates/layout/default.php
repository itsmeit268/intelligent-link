<?php

function ilgl_get_cached_options() {
    static $cached_options = null;

    if ($cached_options === null) {
        $option_keys = ['preplink_setting', 'ads_code', 'preplink_endpoint', 'meta_attr', 'preplink_faq'];
        $cached_options = [];

        foreach ($option_keys as $key) {
            $cached_options[$key] = wp_cache_get($key, 'preplink');
            if (false === $cached_options[$key]) {
                $cached_options[$key] = get_option($key, []);
                wp_cache_set($key, $cached_options[$key], 'preplink', 3600);
            }
        }
    }

    return $cached_options;
}

$options = ilgl_get_cached_options();
$settings = $options['preplink_setting'];
$ads = $options['ads_code'];
$param_url = $options['preplink_endpoint'];
$meta_attr = $options['meta_attr'];
$faq_conf = $options['preplink_faq'];

$prep_title = $_COOKIE['prep_title'] ?? '';
$prep_request = $_COOKIE['prep_request'] ?? '';

$post_id = get_the_ID();
$view_link = get_permalink($post_id);
$post_title = get_the_title($post_id) ?: $prep_title;

$post_meta = get_post_meta($post_id);

$file_name = $post_meta['file_name'][0] ?? '';
$file_format = $post_meta['file_format'][0] ?? '';
$require = $post_meta['require'][0] ?? '';
$os_version = $post_meta['os_version'][0] ?? '';
$file_version = $post_meta['file_version'][0] ?? '';
$link_no_login = $post_meta['link_no_login'][0] ?? '';
$link_is_login = $post_meta['link_is_login'][0] ?? '';
$file_size = $post_meta['file_size'][0] ?? '';

$isMeta = !empty($file_name);
$download_meta = base64_encode(get_bloginfo('url'));
$time_conf = !empty($param_url['countdown_endpoint']) ? (int) $param_url['countdown_endpoint'] : 15;
$post_image = !empty($param_url['preplink_image']);

$has_header = ILGL_Helper::file_exists_cached(get_template_directory() . '/header.php');
$has_footer = ILGL_Helper::file_exists_cached(get_template_directory() . '/footer.php');
$has_comments = ILGL_Helper::file_exists_cached(get_template_directory() . '/comments.php');

set_no_index_page();

function render_ad_slot($ads, $slot_name) {
    if (!empty($ads[$slot_name])) {
        echo '<div class="preplink-ads preplink-ads-' . esc_attr($slot_name) . '" style="margin: 0 25px;">' . $ads[$slot_name] . '</div>';
    }
}

?>

<?php $has_header ? get_header() : wp_head(); ?>
    <div class="igl-single-page" id="prep-request-page" data-request="<?= esc_attr($prep_request) ?>">
        <?php render_ad_slot($ads, 'ads_1'); ?>
        <?php render_back_icon($view_link); ?>

        <header class="igl-header">
            <h1 class="s-title">
                <?php if ($isMeta) : ?>
                    <a class="a-title" href="<?= esc_url($view_link) ?>">
                        <?= esc_html($post_title) ?>
                    </a>
                <?php endif; ?>
            </h1>
        </header>

        <div class="sv-small-container">
            <div class="prep-link-container">
                <div class="prep-content">

                    <?php if (empty($prep_request) || empty($prep_title)) : ?>
                        <!-- SESSION EXPIRED -->
                        <div class="session-expired">
                            <p>
                                <?= __('Your session has ended, please click', 'intelligent-link') ?>
                                <a class="session-end" href="<?= esc_url($view_link) ?>">
                                    <span><?= __('here', 'intelligent-link') ?></span>
                                </a>
                                <?= __('and do it again.', 'intelligent-link') ?>
                            </p>
                            <p><?= __('If the issue persists, please try clearing cookies or attempting with a different browser.', 'intelligent-link') ?></p>
                        </div>
                        <?php render_ad_slot($ads, 'ads_7'); ?>

                    <?php else: ?>
                        <!-- MAIN CONTENT -->

                        <?php if ($post_image && has_post_thumbnail()) : ?>
                            <div class="ilgl-feat-outer">
                                <div class="featured-image">
                                    <img class="prep-thumbnail"
                                         src="<?= esc_url(get_the_post_thumbnail_url($post_id, 'large')) ?>"
                                         alt="<?= esc_attr($post_title) ?>">
                                </div>
                            </div>
                            <?php render_ad_slot($ads, 'ads_2'); ?>
                        <?php endif; ?>

                        <?php if (!empty($param_url['ep_mode']) && $param_url['ep_mode'] == 'default' && $isMeta) : ?>
                            <!-- DEFAULT MODE -->
                            <div class="download-list">
                                <div class="download-item-box">
                                    <div class="download-item">
                                        <div class="left">
                                            <a class="a-title image" href="javascript:void(0)">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <?= get_the_post_thumbnail($post_id, 'thumbnail') ?>
                                                <?php else : ?>
                                                    <img src="<?= esc_url(plugin_dir_url(__DIR__) . 'images/check_icon.png') ?>" alt="check"/>
                                                <?php endif; ?>
                                            </a>
                                            <div class="post-download">
                                                <p class="title prep-title">
                                                    <?= esc_html($isMeta ? $file_name : $prep_title) ?>
                                                </p>
                                                <p class="post-date">
                                                    <?= __('Update:', 'intelligent-link') . ' ' . (get_the_modified_date('d/m/Y') ?: get_the_date('d/m/Y')) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <div class="prep-link-download-btn">
                                                <div class="clickable prep-link-btn">
                                                    <svg class="icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                        <path d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="endpoint-progress" id="endpoint-progress" style="display:none;">
                                <p class="counter">0%</p>
                                <div class="bar"></div>
                                <span class="prep-btn-download" style="display: none">
                                <svg class="icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-143.6-28.9L288 302.6V120c0-13.3-10.7-24-24-24h-16c-13.3 0-24 10.7-24 24v182.6l-72.4-75.5c-9.3-9.7-24.8-9.9-34.3-.4l-10.9 11c-9.4 9.4-9.4 24.6 0 33.9L239 404.3c9.4 9.4 24.6 9.4 33.9 0l132.7-132.7c9.4-9.4 9.4-24.6 0-33.9l-10.9-11c-9.5-9.5-25-9.3-34.3.4z"></path>
                                </svg>
                                <span class="text-down"><?= __('Download', 'intelligent-link') ?></span>
                            </span>
                            </div>

                            <div class="list-file-hide list-server-download" style="display: none">
                                <div class="ilgl-file-timer-btn">
                                    <?php list_link_render($isMeta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $meta_attr); ?>
                                </div>
                            </div>

                        <?php else : ?>
                            <!-- TIMER MODE -->
                            <div class="list-file-hide" id="buttondw">
                                <div class="ilgl-file-timer" style="display:none;">
                                <span class="ilgl-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= esc_attr($time_conf) ?>">
                                    <?= esc_html($time_conf) ?>
                                </span>
                                    <?php svg_render() ?>
                                </div>
                                <div class="ilgl-file-timer-btn" style="opacity:0;pointer-events:none;visibility:hidden;">
                                    <?php list_link_render($isMeta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $meta_attr); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php render_ad_slot($ads, 'ads_3'); ?>

                        <?php if (!empty($faq_conf['faq_enabled']) && $faq_conf['faq_enabled'] == 1) : ?>
                            <?php faq_render(); ?>
                            <?php render_ad_slot($ads, 'ads_4'); ?>
                        <?php endif; ?>

                        <?php render_ad_slot($ads, 'ads_5'); ?>

                        <?php if ($isMeta && !empty($param_url['preplink_related_post']) && $param_url['preplink_related_post'] == 1) : ?>
                            <?php ep_related_post($settings, $post_id) ?>
                            <?php render_ad_slot($ads, 'ads_6'); ?>
                        <?php endif; ?>

                        <?php if ($has_comments && !empty($param_url['preplink_comment']) && (int)$param_url['preplink_comment'] == 1 && $isMeta) : ?>
                            <div class="comment">
                                <?php comments_template(); ?>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php $has_footer ? get_footer() : wp_footer(); ?>