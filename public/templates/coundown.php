<?php
/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */
?>

<?php

$isMeta             = false;
$post_id            = get_the_ID();
$view_link          = get_permalink($post_id);
$settings           = get_option('preplink_setting');
$advertising        = get_option('preplink_advertising');
$faqSetting         = get_option('preplink_faq');
$endpointSetting    = get_option('preplink_endpoint');
$post_title         = get_the_title($post_id) ? get_the_title($post_id) : get_post_field('post_title', $post_id);
$prepLinkText       = isset($_COOKIE['prep_title']) ? $_COOKIE['prep_title'] : '';
$prepLinkURL        = isset($_COOKIE['prep_request']) ? $_COOKIE['prep_request'] : '';
$baseUrl            = str_replace('https://', '', !empty(home_url()) ? home_url() : get_bloginfo('url'));
$file_format        = get_post_meta($post_id, 'file_format', true);
$require            = get_post_meta($post_id, 'require', true);
$os_version         = get_post_meta($post_id, 'os_version', true);
$file_version       = get_post_meta($post_id, 'file_version', true);
$file_name          = get_post_meta($post_id, 'file_name', true);
$download_meta      = base64_encode(get_bloginfo('url'));
$time_conf          = !empty($endpointSetting['countdown_endpoint']) ? (int) $endpointSetting['countdown_endpoint'] : 15;
$post_image         = !empty($endpointSetting['preplink_image'] ? true: false);
$link_no_login  = get_post_meta($post_id, 'link_no_login', true);
$link_is_login  = get_post_meta($post_id, 'link_is_login', true);
$file_size      = get_post_meta($post_id, 'file_size', true);

if (!empty($settings['preplink_custom_style'])) {
    echo "<style>{$settings['preplink_custom_style']}</style>";
}
?>
<?php if (file_exists(get_template_directory() . '/header.php')) get_header(); ?>

<div class="single-page without-sidebar sticky-sidebar" id="prep-link-single-page" data-request="<?= $prepLinkURL ?>"  style="max-width: 890px; margin: 0 auto;">
    <div class="p-file">
        <div class="section">
            <div class="p-file-back">
                <a href="<?= esc_attr($view_link)?>"><svg width="48" height="20"><use xlink:href="#i__back"></use></svg></a>
            </div>
            <h1 class="p-file-title title"><?= esc_html($post_title);?></h1>

            <div class="p-file-cont section">
                <?php if (empty($prepLinkURL) || empty($prepLinkText)) : ?>
                    <?php if (isset($advertising['pr_ad_6']) && (int)$advertising['pr_ad_6'] == 1 && !empty($advertising['pr_ad_code_6'])): ?>
                        <div class="preplink-ads preplink-ads-6" style="margin: 0 25px;">
                            <?= $advertising['pr_ad_code_6'] ?>
                        </div>
                    <?php endif; ?>
                    <div class="session-expired">
                        <p><?= __('Your session has ended, please click', 'prep-link')?>&nbsp;<a href="<?= $view_link ?>"><span style="color: #0a4ad0;"><?= __('here', 'prep-link')?></span></a>&nbsp;<?= __('and do it again.', 'prep-link')?></p>
                        <p><?= __('If the issue persists, try clearing your cookies or browser history and attempt again.', 'prep-link') ?></p>
                    </div>
                    <?php if ( isset($advertising['pr_ad_7']) && (int)$advertising['pr_ad_7'] == 1 && !empty($advertising['pr_ad_code_7'])): ?>
                        <div class="preplink-ads preplink-ads-7">
                            <?= $advertising['pr_ad_code_7'] ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>

                    <?php if ( isset($advertising['preplink_advertising_1']) && (int)$advertising['preplink_advertising_1'] == 1 && !empty($advertising['preplink_advertising_code_1'])): ?>
                        <div class="preplink-ads preplink-ads-1">
                            <?= $advertising['preplink_advertising_code_1'] ?>
                        </div>
                    <?php endif; ?>

                    <?php if (has_post_thumbnail()):?>
                        <div class="s-feat-outer">
                            <div class="featured-image">
                                <img src="<?= get_the_post_thumbnail_url($post_id, 'full'); ?>" class="prep-thumbnail" alt="<?= $post_title ?>" title="<?= $post_title ?>">
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if ( isset($advertising['preplink_advertising_2']) && (int)$advertising['preplink_advertising_2'] == 1 && !empty($advertising['preplink_advertising_code_2'])): ?>
                        <div class="preplink-ads preplink-ads-2">
                            <?= $advertising['preplink_advertising_code_2'] ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-file-hide" id="buttondw">
                        <div class="p-file-timer" style="display:none;">
                            <span class="p-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= $time_conf ?>"><?= $time_conf ?></span>
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
                        </div>
                        <div class="p-file-timer-btn" style="opacity:0;pointer-events:none;visibility:hidden;">
                            <?php

                            if (is_user_logged_in()): ?>
                                <a href="javascript:void(0)" data-request="<?php echo $isMeta ? esc_html(base64_encode($link_is_login)) : esc_html($prepLinkURL); ?>" class="btn blue-style preplink-btn-link" >
                                    <?php echo $isMeta ? ($file_name.' '.$file_size) : $prepLinkText; ?>
                                </a>
                                <?php if ($isMeta) get_list_link_download($post_id, $settings); ?>
                            <?php else: ?>
                                <a href="javascript:void(0)" data-request="<?php echo $isMeta ? esc_html(base64_encode($link_no_login)) : esc_html($prepLinkURL); ?>" class="btn blue-style preplink-btn-link" >
                                    <?php echo $isMeta ? ($file_name.' '.$file_size) : $prepLinkText; ?>
                                </a>
                                <?php if ($isMeta) get_list_link_download($post_id, $settings); ?>
                            <?php endif;
                            ?>
                        </div>
                    </div>

                    <?php if ( isset($advertising['preplink_advertising_3']) && (int)$advertising['preplink_advertising_3'] == 1 && !empty($advertising['preplink_advertising_code_3'])): ?>
                        <div class="preplink-ads preplink-ads-3">
                            <?= $advertising['preplink_advertising_code_3'] ?>
                        </div>
                    <?php endif; ?>
                <?php endif;?>
            </div>

            <?php if (!empty($prepLinkURL) || !empty($prepLinkText) && isset($faqSetting['faq_enabled']) &&
                $faqSetting['faq_enabled'] == 1 && isset($faqSetting['faq_description'])) : ?>
                <div class="faq-download">
                    <h3 class="faq-title"><?= !empty($faqSetting['faq_title']) ? $faqSetting['faq_title'] : '' ?></h3>
                    <?= $faqSetting['faq_description']; ?>
                </div>
                <?php if ( isset($advertising['preplink_advertising_4']) && (int)$advertising['preplink_advertising_4'] == 1 && !empty($advertising['preplink_advertising_code_4'])): ?>
                    <div class="preplink-ads preplink-ads-4">
                        <?= $advertising['preplink_advertising_code_4'] ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($prepLinkURL) || !empty($prepLinkText)) : ?>
            <div class="section section-newgames">
                <h3 class="section-title"><?= __('Recommended for you', 'prep-link')?></h3>
                <div class="list-app one-row">
                    <?php
                    $categories = get_the_category();
                    $category_ids = array();
                    foreach ($categories as $category) {
                        $category_ids[] = $category->term_id;
                    }

                    $args = array(
                        'category__in' => $category_ids,
                        'post__not_in' => array(get_the_ID()),
                        'posts_per_page' => !empty($settings['preplink_related_number']) ? $settings['preplink_related_number'] : 4, // Lấy 10 bài viết
                        'orderby' => 'rand',
                        'order' => 'DESC'
                    );

                    $related_posts = get_posts($args);
                    if ($related_posts) {
                        foreach ($related_posts as $post) {
                            setup_postdata($post); ?>
                            <div class="app">
                                <a class="app-cont" href="<?= get_permalink($post); ?>">
                                    <figure class="app-img">
                                        <?php
                                        if (function_exists('savvymobi_get_app_image')) {
                                            savvymobi_get_app_image($post_id, 116, 116);
                                        } else {
                                            if (has_post_thumbnail()){
                                                echo get_the_post_thumbnail($post, 'thumbnail');
                                            }
                                        }
                                        ?>
                                    </figure>
                                    <span class="app-title"><?= $post->post_title?></span>
                                </a>
                            </div>
                            <?php } ?>
                        <?php
                        wp_reset_postdata();
                    } ?>
                </div>
            </div>
            <?php if ( isset($advertising['pr_ad_5']) && (int)$advertising['pr_ad_5'] == 1 && !empty($advertising['pr_ad_code_5'])): ?>
                <div class="preplink-ads preplink-ads-5">
                    <?= $advertising['pr_ad_code_5'] ?>
                </div>
            <?php endif; ?>
        <?php
        if (file_exists(get_template_directory() . '/comments.php') && (int)$endpointSetting['preplink_comment'] == 1) { ?>
            <div class="comment"><?php comments_template(); ?></div>
        <?php } ?>
        <?php endif;?>
    </div>
    <svg aria-hidden="true" style="display:none;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs>
            <symbol id="i__back" viewBox="0 0 48 20">
                <path fill="currentColor" d="M46.27,9.24a1,1,0,0,0-1.41.1A10.71,10.71,0,0,1,36.78,13h-.16a10.78,10.78,0,0,1-8.1-3.66A12.83,12.83,0,0,0,18.85,5C14.94,5,10.73,7.42,6,12.4V5A1,1,0,0,0,4,5V15a1,1,0,0,0,1,1H15a1,1,0,0,0,0-2H7.24c4.42-4.71,8.22-7,11.62-7A10.71,10.71,0,0,1,27,10.66,12.81,12.81,0,0,0,36.61,15h.18a12.7,12.7,0,0,0,9.58-4.35A1,1,0,0,0,46.27,9.24Z"></path>
            </symbol>
        </defs>
    </svg>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>