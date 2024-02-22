<?php
/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

$prep_title         = isset($_COOKIE['prep_title']) ? $_COOKIE['prep_title'] : '';
$prep_request       = isset($_COOKIE['prep_request']) ? $_COOKIE['prep_request'] : '';

$settings           = get_option('preplink_setting');
$ads                = get_option('ads_code');
$faq_conf           = get_option('preplink_faq');
$endpoint_conf      = get_option('preplink_endpoint');
$isMeta             = false;
$post_id            = get_the_ID();
$view_link          = get_permalink($post_id);
$post_title         = get_the_title($post_id) ? get_the_title($post_id) : $prep_title;

$baseUrl            = str_replace('https://', '', !empty(home_url()) ? home_url() : get_bloginfo('url'));
$file_format        = get_post_meta($post_id, 'file_format', true);
$require            = get_post_meta($post_id, 'require', true);
$os_version         = get_post_meta($post_id, 'os_version', true);
$file_version       = get_post_meta($post_id, 'file_version', true);
$file_name          = get_post_meta($post_id, 'file_name', true);
$download_meta      = base64_encode(get_bloginfo('url'));
$time_conf          = !empty($endpoint_conf['countdown_endpoint']) ? (int) $endpoint_conf['countdown_endpoint'] : 15;
$post_image         = !empty($endpoint_conf['preplink_image'] ? true: false);
$link_no_login      = get_post_meta($post_id, 'link_no_login', true);
$link_is_login      = get_post_meta($post_id, 'link_is_login', true);
$file_size          = get_post_meta($post_id, 'file_size', true);

if ($download_meta === $prep_request) {
    $isMeta = true;
}
set_no_index_page();
?>
<?php if (!empty($settings['preplink_custom_style'])) {
    echo "<style>{$settings['preplink_custom_style']}</style>";
} ?>

<?php
if (file_exists(get_template_directory() . '/header.php')){
    get_header();
} else {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="'.plugin_dir_url(__DIR__) . 'css/template.css'.'">';
    echo '<meta name="robots" content="nofollow, noindex">';
}
?>



<div class="igl-single-page" id="prep-request-page" data-request="<?= esc_attr($prep_request) ?>">
    <?= !empty($ads['ads_1']) ? '<div class="preplink-ads preplink-ads-1" style="margin: 0 25px;">' . $ads['ads_1'] . '</div>' : '' ?>
    <?php render_back_icon($view_link); ?>
    <header class="igl-header">
        <h1 class="s-title">
            <?php if ($isMeta) echo '<a class="a-title" href="'.esc_url($view_link).'">'.esc_html($post_title).'</a>' ?>
        </h1>
    </header>
    <div class="sv-small-container">
        <div class="grid-container">
            <div class="entry-content prep-content">
                <?php if (empty($prep_request) || empty($prep_title)) : ?>
                    <div class="session-expired">
                        <p><?= __('Your session has ended, please click', 'prep-link')?>&nbsp;<a href="<?= $view_link ?>"><span style="color: #0a4ad0;"><?= __('here', 'prep-link')?></span></a>&nbsp;<?= __('and do it again.', 'prep-link')?></p>
                        <p><?= __('If the issue persists, please try clearing cookies or attempting with a different browser.', 'prep-link') ?></p>
                    </div>
                    <?= !empty($ads['ads_7']) ? '<div class="preplink-ads preplink-ads-2" style="margin: 0 25px;">' . $ads['ads_7'] . '</div>' : '' ?>
                <?php else: ?>
                    <?php if ($post_image && $isMeta && has_post_thumbnail()) : ?>
                        <div class="s-feat-outer">
                            <div class="featured-image">
                                <img src="<?= get_the_post_thumbnail_url($post_id, 'full'); ?>" class="prep-thumbnail" alt="<?= $post_title ?>" title="<?= $post_title ?>">
                            </div>
                        </div>
                        <?= !empty($ads['ads_2']) ? '<div class="preplink-ads preplink-ads-2" style="margin: 0 25px;">' . $ads['ads_2'] . '</div>' : '' ?>
                    <?php endif;?>
                    <?php if ($endpoint_conf['ep_mode'] == 'default'): ?>
                        <div class="download-list">
                            <div class="download-item-box">
                                <div class="download-item">
                                    <div class="left">
                                        <a class="a-title image" href="javascript:void(0)">
                                            <?php
                                            $image_src = plugin_dir_url(__DIR__) . 'images/11820285.png';
                                            $img = '<img src="'. esc_url($image_src).'"/>';
                                            echo has_post_thumbnail() ? get_the_post_thumbnail($post_id, 'thumbnail') : $img; ?>
                                        </a>
                                        <div class="post-download">
                                            <p class="title prep-title"><?= $isMeta ? ($file_name) : $prep_title; ?></p>
                                            <p class="post-date"><?= __('Update:', 'prep-link') . ' ' . get_the_modified_date('d/m/Y') ?: get_the_date('d/m/Y'); ?></p>
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
                            <span class="text-down"><?= __('Download', 'prep-link');?></span>
                        </span>
                        </div>

                        <div class="p-file-hide list-server-download" style="display: none">
                            <div class="p-file-timer-btn">
                                <?php link_render($isMeta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $settings); ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="p-file-hide" id="buttondw">
                            <div class="p-file-timer" style="display:none;">
                                <span class="p-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= $time_conf ?>"><?= $time_conf ?></span>
                                <?php svg_render() ?>
                            </div>
                            <div class="p-file-timer-btn" style="opacity:0;pointer-events:none;visibility:hidden;">
                                <?php link_render($isMeta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $settings); ?>
                            </div>
                        </div>
                    <?php endif;?>

                    <?= !empty($ads['ads_3']) ? '<div class="preplink-ads preplink-ads-3" style="margin: 0 25px;">' . $ads['ads_3'] . '</div>' : '' ?>

                    <?php if (!empty($faq_conf['faq_enabled']) && $faq_conf['faq_enabled'] == 1 && !empty($faq_conf['faq_description'])) : ?>
                        <?php faq_render($faq_conf['faq_enabled'], $faq_conf['faq_description'] ); ?>
                        <?= !empty($ads['ads_4']) ? '<div class="preplink-ads preplink-ads-4" style="margin: 0 25px;">' . $ads['ads_4'] . '</div>' : '' ?>
                    <?php endif; ?>

                    <div class="keyword-search">
                        <p>
                            <?= __('To search for a specific resource or content on the internet, you can visit', 'prep-link')?>
                            <a target="_blank" href="//www.google.com/search?q=<?=$prep_title.' '.$baseUrl?>"><?= __('google.com', 'prep-link')?></a>
                            <?= __('and enter your search query as:', 'prep-link')?>
                            <a target="_blank" href="//www.google.com/search?q=<?=$prep_title.' '.$baseUrl?>"><?= __('keyword +', 'prep-link') . ' '. $baseUrl?></a>
                        </p>
                    </div>

                    <?= !empty($ads['ads_5']) ? '<div class="preplink-ads preplink-ads-5" style="margin: 0 25px;">' . $ads['ads_5'] . '</div>' : '' ?>

                    <?php if ($isMeta && !empty($endpoint_conf['preplink_related_post']) && $endpoint_conf['preplink_related_post'] == 1): ?>
                        <?php ep_related_post($settings, $post_id) ?>
                        <?= !empty($ads['ads_6']) ? '<div class="preplink-ads preplink-ads-6" style="margin: 0 25px;">' . $ads['ads_6'] . '</div>' : '' ?>
                    <?php endif; ?>

                    <?php if (file_exists(get_template_directory() . '/comments.php') && (int)$endpoint_conf['preplink_comment'] == 1) { ?>
                        <div class="comment"><?php comments_template(); ?></div>
                    <?php } ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>
