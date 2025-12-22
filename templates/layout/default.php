<?php

$settings       = ilgl_settings()->global_settings();
$ads_settings   = ilgl_settings()->ads_settings();
$ep_settings    = ilgl_settings()->ep_settings();
$meta_option    = ilgl_settings()->meta_option();
$faq_settings   = ilgl_settings()->faq_settings();

$prep_title     = $_COOKIE['prep_title'] ?? ($_GET['pt'] ?? '');
$prep_request   = $_COOKIE['prep_request'] ?? ($_GET['pr'] ?? '');
$is_meta        = $_COOKIE['prep_meta'] ?? ($_GET['pm'] ?? 0);
$prep_title     = htmlspecialchars($prep_title, ENT_QUOTES, 'UTF-8');

$post_id        = get_the_ID();
$view_link      = get_permalink($post_id);
$post_title     = get_the_title($post_id) ?: $prep_title;
$post_meta      = get_post_meta($post_id);
$file_name      = $post_meta['file_name'][0] ?? '';
$file_format    = $post_meta['file_format'][0] ?? '';
$require        = $post_meta['require'][0] ?? '';
$os_version     = $post_meta['os_version'][0] ?? '';
$file_version   = $post_meta['file_version'][0] ?? '';
$link_no_login  = $post_meta['link_no_login'][0] ?? '';
$link_is_login  = $post_meta['link_is_login'][0] ?? '';
$file_size      = $post_meta['file_size'][0] ?? '';

$post_image     = !empty($ep_settings['preplink_image']);
$time_conf      = !empty($ep_settings['countdown_endpoint']) ? (int) $ep_settings['countdown_endpoint'] : 15;

$has_header = file_exists(get_template_directory() . '/header.php');
$has_footer = file_exists(get_template_directory() . '/footer.php');
$has_comments = file_exists(get_template_directory() . '/comments.php');
?>

<?php $has_header ? get_header() : wp_head(); ?>
<div class="ilgl-page">
    <?php ilgl_helper()->render_ad_slot($ads_settings, 'ads_1'); ?>
    <div class="igl-back">
        <a href="<?= esc_url($view_link) ?>">
            <i class="c-svg"><svg width="48" height="20"><use xlink:href="#i__back"></use></svg></i>
            <svg aria-hidden="true" style="display:none;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <symbol id="i__back" viewBox="0 0 48 20">
                        <path fill="currentColor" d="M46.27,9.24a1,1,0,0,0-1.41.1A10.71,10.71,0,0,1,36.78,13h-.16a10.78,10.78,0,0,1-8.1-3.66A12.83,12.83,0,0,0,18.85,5C14.94,5,10.73,7.42,6,12.4V5A1,1,0,0,0,4,5V15a1,1,0,0,0,1,1H15a1,1,0,0,0,0-2H7.24c4.42-4.71,8.22-7,11.62-7A10.71,10.71,0,0,1,27,10.66,12.81,12.81,0,0,0,36.61,15h.18a12.7,12.7,0,0,0,9.58-4.35A1,1,0,0,0,46.27,9.24Z"></path>
                    </symbol>
                </defs>
            </svg>
        </a>
    </div>
    <header class="igl-header">
        <h1 class="s-title">
            <?php if ($is_meta) : ?>
                <a class="a-title" href="<?= esc_url($view_link) ?>">
                    <?= esc_html($post_title) ?>
                </a>
            <?php endif; ?>
        </h1>
    </header>
    <div class="ilgl-content">
        <?php if (empty($prep_request) || empty($prep_title)) : ?>
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
            <?php ilgl_helper()->render_ad_slot($ads_settings, 'ads_5'); ?>
        <?php else: ?>
            <?php if ($post_image && has_post_thumbnail()) : ?>
                <div class="ilgl-feat-outer">
                    <div class="featured-image">
                        <img class="prep-thumbnail"
                             src="<?= esc_url(get_the_post_thumbnail_url($post_id, 'large')) ?>"
                             alt="<?= esc_attr($post_title) ?>">
                    </div>
                </div>
                <?php ilgl_helper()->render_ad_slot($ads_settings, 'ads_2'); ?>
            <?php endif; ?>

            <?php if (!empty($ep_settings['ep_mode']) && $ep_settings['ep_mode'] == 'default' && $is_meta) : ?>
                <div class="download-list">
                    <div class="download-item-box">
                        <div class="download-item">
                            <div class="left">
                                <a class="a-title image" href="javascript:void(0)">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?= get_the_post_thumbnail($post_id, 'thumbnail') ?>
                                    <?php endif; ?>
                                </a>
                                <div class="post-download">
                                    <p class="title prep-title">
                                        <?= esc_html($is_meta ? $file_name : $prep_title) ?>
                                        <img class="ilgl-verified" src="<?= esc_url(plugin_dir_url(__DIR__) . 'images/verified.svg') ?>" alt="verified"/>
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
                        <?php ilgl_helper()->link_render($is_meta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $meta_option); ?>
                    </div>
                </div>

            <?php else : ?>
                <div class="list-file-hide" id="buttondw">
                    <div class="ilgl-file-timer" style="display:none;">
                            <span class="ilgl-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= esc_attr($time_conf) ?>">
                                <?= esc_html($time_conf) ?>
                            </span>
                        <?php ilgl_helper()->svg_render() ?>
                    </div>
                    <div class="ilgl-file-timer-btn" style="opacity:0;pointer-events:none;visibility:hidden;">
                        <?php ilgl_helper()->link_render($is_meta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $meta_option); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php ilgl_helper()->render_ad_slot($ads_settings, 'ads_3'); ?>

            <?php if (!empty($faq_settings['faq_enabled']) && $faq_settings['faq_enabled'] == 1) : ?>
                <?php ilgl_helper()->faq_render(); ?>
                <?php ilgl_helper()->render_ad_slot($ads_settings, 'ads_4'); ?>
            <?php endif; ?>

            <?php if ($is_meta && !empty($ep_settings['preplink_related_post']) && $ep_settings['preplink_related_post'] == 1) : ?>
                <?php ilgl_helper()->ep_related_post($settings, $post_id) ?>
            <?php endif; ?>

            <?php if ($has_comments && !empty($ep_settings['preplink_comment']) && (int)$ep_settings['preplink_comment'] == 1 && $is_meta) : ?>
                <div class="ilgl-comment">
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
<?php $has_footer ? get_footer() : wp_footer(); ?>