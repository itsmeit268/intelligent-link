<?php
/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

$isMeta             = false;
$post_id            = get_the_ID();
$view_link          = get_permalink($post_id);
$settings           = get_option('preplink_setting');
$advertising        = get_option('preplink_advertising');
$faqSetting         = get_option('preplink_faq');
$endpoint_conf    = get_option('preplink_endpoint');
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
$time_conf          = !empty($endpoint_conf['countdown_endpoint']) ? (int) $endpoint_conf['countdown_endpoint'] : 15;
$post_image         = !empty($endpoint_conf['preplink_image'] ? true: false);
$link_no_login  = get_post_meta($post_id, 'link_no_login', true);
$link_is_login  = get_post_meta($post_id, 'link_is_login', true);
$file_size      = get_post_meta($post_id, 'file_size', true);

if ($download_meta === $prepLinkURL) {
    $isMeta = true;
}

?>
<?php if (!empty($settings['preplink_custom_style'])) {
    echo "<style>{$settings['preplink_custom_style']}</style>";
} ?>

<?php if (file_exists(get_template_directory() . '/header.php')) get_header();
?>

<div class="single-page without-sidebar" id="prep-link-single-page" data-request="<?= esc_attr($prepLinkURL) ?>" style="max-width: 890px; margin: 0 auto;">
    <div class="p-file-back">
        <a href="<?= esc_url($view_link)?>">
            <i class="c-svg"><svg width="48" height="20"><use xlink:href="#i__back"></use></svg></i>
        </a>
    </div>
    <header class="single-header">
        <h1 class="s-title">
            <a class="adsterra" href="javascript:void(0)"><?= $post_title; ?></a>
        </h1>
    </header>
    <div class="sv-small-container">
        <div class="grid-container">
            <div class="entry-content prep-content">
                <?php if (empty($prepLinkURL) || empty($prepLinkText)) : ?>
                    <?php if (isset($advertising['pr_ad_6']) && (int)$advertising['pr_ad_6'] == 1 && !empty($advertising['pr_ad_code_6'])): ?>
                        <div class="preplink-ads preplink-ads-6" style="margin: 0 25px;">
                            <?= $advertising['pr_ad_code_6'] ?>
                        </div>
                    <?php endif; ?>
                    <div class="session-expired">
                        <p><?= __('Your session has ended, please click', 'prep-link')?>&nbsp;<a href="<?= $view_link ?>"><span style="color: #0a4ad0;"><?= __('here', 'prep-link')?></span></a>&nbsp;<?= __('and do it again.', 'prep-link')?></p>
                        <p><?= __('If the issue persists, please try clearing cookies or attempting with a different browser.', 'prep-link') ?></p>
                    </div>
                    <?php if (isset($advertising['pr_ad_7']) && (int)$advertising['pr_ad_7'] == 1 && !empty($advertising['pr_ad_code_7'])): ?>
                        <div class="preplink-ads preplink-ads-7">
                            <?= $advertising['pr_ad_code_7'] ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>

                    <?php if (isset($endpoint_conf['preplink_image']) && $endpoint_conf['preplink_image']) : ?>

                        <?php if (isset($advertising['preplink_advertising_1']) && (int)$advertising['preplink_advertising_1'] == 1 && !empty($advertising['preplink_advertising_code_1'])): ?>
                            <div class="preplink-ads preplink-ads-1">
                                <?= $advertising['preplink_advertising_code_1'] ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($os_version && $require && function_exists('savvymobi_get_app_image')) : ?>
                            <div class="p-file-data section">
                                <div class="p-file-data-l">
                                    <i class="c-svg"><svg width="24" height="24"><use xlink:href="#i__android"></use></svg></i>
                                    <span class="c-blue fw-b"><?= esc_html__($require); ?></span>
                                    <span> <?= esc_html($os_version)?></span>
                                </div>
                                <div class="page_file-img">
                                    <?= savvymobi_get_app_image($post_id, 116, 116); ?>
                                </div>
                                <div class="p-file-data-r">
                                    <i class="c-svg">
                                        <svg class="bi bi-phone-flip" fill="currentColor" width="20" height="20" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11 1H5a1 1 0 0 0-1 1v6a.5.5 0 0 1-1 0V2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6a.5.5 0 0 1-1 0V2a1 1 0 0 0-1-1Zm1 13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-2a.5.5 0 0 0-1 0v2a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-2a.5.5 0 0 0-1 0v2ZM1.713 7.954a.5.5 0 1 0-.419-.908c-.347.16-.654.348-.882.57C.184 7.842 0 8.139 0 8.5c0 .546.408.94.823 1.201.44.278 1.043.51 1.745.696C3.978 10.773 5.898 11 8 11c.099 0 .197 0 .294-.002l-1.148 1.148a.5.5 0 0 0 .708.708l2-2a.5.5 0 0 0 0-.708l-2-2a.5.5 0 1 0-.708.708l1.145 1.144L8 10c-2.04 0-3.87-.221-5.174-.569-.656-.175-1.151-.374-1.47-.575C1.012 8.639 1 8.506 1 8.5c0-.003 0-.059.112-.17.115-.112.31-.242.6-.376Zm12.993-.908a.5.5 0 0 0-.419.908c.292.134.486.264.6.377.113.11.113.166.113.169 0 .003 0 .065-.13.187-.132.122-.352.26-.677.4-.645.28-1.596.523-2.763.687a.5.5 0 0 0 .14.99c1.212-.17 2.26-.43 3.02-.758.38-.164.713-.357.96-.587.246-.229.45-.537.45-.919 0-.362-.184-.66-.412-.883-.228-.223-.535-.411-.882-.571ZM7.5 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1Z" fill-rule="evenodd"/>
                                        </svg>
                                    </i>
                                    <span class="c-blue fw-b">Version</span>
                                    <span><?= esc_html($file_version)?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="s-feat-outer">
                                <div class="featured-image">
                                    <img src="<?= get_the_post_thumbnail_url($post_id, 'full'); ?>" class="prep-thumbnail" alt="<?= $post_title ?>" title="<?= $post_title ?>">
                                </div>
                            </div>
                        <?php endif;?>

                        <?php if (isset($advertising['preplink_advertising_2']) && (int)$advertising['preplink_advertising_2'] == 1 && !empty($advertising['preplink_advertising_code_2'])): ?>
                            <div class="preplink-ads preplink-ads-2">
                                <?= $advertising['preplink_advertising_code_2'] ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($endpoint_conf['ep_mode'] == 'default'): ?>
                        <div class="download-list">
                            <div class="download-item-box">
                                <div class="download-item">
                                    <div class="left">
                                        <a class="adsterra image" href="javascript:void(0)">
                                            <?php
                                            $app_image = get_post_meta($post_id, 'app-image-metabox', true);
                                            if ($app_image) {
                                                $src = $app_image ? : get_theme_file_uri('/assets/image/savvymobi.jpg');
                                                echo '<img src="'.esc_url($src).'" class="attachment-thumbnail size-thumbnail wp-post-image prep-app-image" alt="'.esc_html($post_title).'">';
                                            } else {
                                                if (has_post_thumbnail()) {
                                                    the_post_thumbnail('thumbnail');
                                                }
                                            }
                                            ?>

                                        </a>
                                        <div class="post-download">
                                            <p class="tittle"><?= $isMeta ? ($file_name) : $prepLinkText; ?></p>
                                            <p class="post-date"><?= __('Update:', 'prep-link') . ' ' . get_the_modified_date('d/m/Y') ?: get_the_date('d/m/Y')?></p>
                                        </div>
                                    </div>
                                    <div class="right">
                                        <div class="prep-link-download-btn">
                                            <div class="clickable prep-link-btn">
                                                <svg class="icon" fill="currentColor"
                                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24">
                                                    <path d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="enpoint-progress" id="enpoint-progress" style="display:none;">
                            <p class="counter">0%</p>
                            <div class="bar"></div>
                            <span class="prep-btn-download" style="display: none">
                            <svg class="icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path d="M504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-143.6-28.9L288 302.6V120c0-13.3-10.7-24-24-24h-16c-13.3 0-24 10.7-24 24v182.6l-72.4-75.5c-9.3-9.7-24.8-9.9-34.3-.4l-10.9 11c-9.4 9.4-9.4 24.6 0 33.9L239 404.3c9.4 9.4 24.6 9.4 33.9 0l132.7-132.7c9.4-9.4 9.4-24.6 0-33.9l-10.9-11c-9.5-9.5-25-9.3-34.3.4z"></path>
                            </svg>
                            <span class="text-down"><?= __('Download');?></span>
                        </span>
                        </div>

                        <div class="p-file-hide list-server-download" style="display: none">
                            <div class="p-file-timer-btn">
                                <?php link_render($isMeta, $link_is_login, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id, $settings); ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="p-file-hide" id="buttondw">
                            <div class="p-file-timer" style="display:none;">
                                <span class="p-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= $time_conf ?>"><?= $time_conf ?></span>
                                <?= svg_render() ?>
                            </div>
                            <div class="p-file-timer-btn" style="opacity:0;pointer-events:none;visibility:hidden;">
                                <?php link_render($isMeta, $link_is_login, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id, $settings); ?>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (isset($advertising['preplink_advertising_3']) && (int)$advertising['preplink_advertising_3'] == 1 && !empty($advertising['preplink_advertising_code_3'])): ?>
                        <div class="preplink-ads preplink-ads-3">
                            <?= $advertising['preplink_advertising_code_3'] ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($prepLinkURL) || !empty($prepLinkText)) :
                        ?>
                        <?php if (!empty($faqSetting['faq_enabled']) && $faqSetting['faq_enabled'] == 1 && !empty($faqSetting['faq_description'])) : ?>
                        <div class="faq-download">
                            <h3 class="faq-title"><?= !empty($faqSetting['faq_title']) ? $faqSetting['faq_title'] : 'FAQ' ?></h3>
                            <?= $faqSetting['faq_description']; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($advertising['preplink_advertising_4']) && (int)$advertising['preplink_advertising_4'] == 1 && !empty($advertising['preplink_advertising_code_4'])): ?>
                            <div class="preplink-ads preplink-ads-4">
                                <h3 style="text-align: left;margin-bottom: 15px;text-transform: uppercase; font-size: 18px;">Advertising</h3>
                                <?= $advertising['preplink_advertising_code_4'] ?>
                            </div>
                        <?php endif; ?>
                    <?php endif;?>

                    <div class="preplink-gg-s">
                        <p>
                            <?= __('To search for a specific resource or content on the internet, you can visit', 'prep-link')?>
                            <a target="_blank" href="//www.google.com/search?q=<?=$prepLinkText.' '.$baseUrl?>"><?= __('https://google.com', 'prep-link')?></a>
                            <?= __('and enter your search query as:', 'prep-link')?>
                            <a target="_blank" href="//www.google.com/search?q=<?=$prepLinkText.' '.$baseUrl?>"><?= __('keyword +', 'prep-link') . ' '. $baseUrl?></a>
                        </p>
                    </div>

                    <?php if (!empty($endpoint_conf['preplink_related_post']) && $endpoint_conf['preplink_related_post'] == 1): ?>
                        <?php ep_related_post($settings, $post_id) ?>
                        <?php if (isset($advertising['pr_ad_5']) && (int)$advertising['pr_ad_5'] == 1 && !empty($advertising['pr_ad_code_5'])) : ?>
                            <div class="preplink-ads preplink-ads-5">
                                <?= $advertising['pr_ad_code_5'] ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (file_exists(get_template_directory() . '/comments.php') && (int)$endpoint_conf['preplink_comment'] == 1) { ?>
                        <div class="comment"><?php comments_template(); ?></div>
                    <?php } ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
    <svg aria-hidden="true" style="display:none;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs>
            <symbol id="i__back" viewBox="0 0 48 20">
                <path fill="currentColor" d="M46.27,9.24a1,1,0,0,0-1.41.1A10.71,10.71,0,0,1,36.78,13h-.16a10.78,10.78,0,0,1-8.1-3.66A12.83,12.83,0,0,0,18.85,5C14.94,5,10.73,7.42,6,12.4V5A1,1,0,0,0,4,5V15a1,1,0,0,0,1,1H15a1,1,0,0,0,0-2H7.24c4.42-4.71,8.22-7,11.62-7A10.71,10.71,0,0,1,27,10.66,12.81,12.81,0,0,0,36.61,15h.18a12.7,12.7,0,0,0,9.58-4.35A1,1,0,0,0,46.27,9.24Z"></path>
            </symbol>
        </defs>
    </svg>
</div>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>
