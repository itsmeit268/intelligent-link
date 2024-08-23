<?php
/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

$ads                = ilgl_ads_option();
$meta_attr          = ilgl_meta_option();
$post_id            = get_the_ID();
$view_link          = get_permalink($post_id);
$baseUrl            = str_replace(array('https://', 'http://'), '', get_bloginfo('url'));
$file_format        = get_post_meta($post_id, 'file_format', true);
$require            = get_post_meta($post_id, 'require', true);
$os_version         = get_post_meta($post_id, 'os_version', true);
$file_version       = get_post_meta($post_id, 'file_version', true);
$file_name          = get_post_meta($post_id, 'file_name', true);
$link_no_login      = get_post_meta($post_id, 'link_no_login', true);
$link_is_login      = get_post_meta($post_id, 'link_is_login', true);
$file_size          = get_post_meta($post_id, 'file_size', true);
$replace_format     = get_post_meta(get_the_ID(), 'format_render', true);
$prep_title         = isset($_COOKIE['prep_title']) ? $_COOKIE['prep_title'] : '';
$prep_request       = isset($_COOKIE['prep_request']) ? $_COOKIE['prep_request'] : '';
$is_meta            = isset($_COOKIE['prep_meta']) ? $_COOKIE['prep_meta'] : '';
$time_conf          = !empty(ep_settings()['countdown_endpoint']) ? (int) ep_settings()['countdown_endpoint'] : 15;
$post_image         = !empty(ep_settings()['preplink_image'] ? true: false);
$post_title         = get_the_title($post_id) ? : $prep_title;
$version            = !empty($file_version) ? ' v'.$file_version : '';
$file_title         = $file_name. $version. '.'. $file_format;

if ($replace_format === 'file') {
    $file_title = $file_name;
}

?>
<?php meta_robots(); ?>
<?php file_exists(get_template_directory() . '/header.php') ? get_header() : wp_head(); ?>
<?php
wp_dequeue_script('fixedtoc-js');
wp_deregister_script('fixedtoc-js');

wp_dequeue_script('fixedtoc-js-js-extra');
wp_deregister_script('fixedtoc-js-js-extra');

wp_dequeue_script('enlighterjs');
wp_deregister_script('enlighterjs');

wp_dequeue_style('enlighterjs');

function add_custom_canonical_tag() {
    echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '" />';
}
add_action('wp_head', 'add_canonical_tag');
?>

<div class="igl-single-page">
    <?= !empty($ads['ads_1']) ? '<div class="preplink-ads preplink-ads-1">' . $ads['ads_1'] . '</div>' : '' ?>
    <?php render_back_icon($view_link); ?>
    <header class="igl-header">
        <h1 class="s-title">
            <?php if ($is_meta) echo '<a class="a-title" href="'.esc_url($view_link).'">'.esc_html($post_title).'</a>' ?>
        </h1>
    </header>
    <div class="sv-small-container">
        <div class="prep-link-container">
            <div class="prep-content">
                <?php if (!$is_meta) : ?>
                    <?php if (empty($prep_title) && empty($prep_request)) :?>
                        <div class="session-expired">
                            <p><?= __('Your session has ended, please click', 'intelligent-link')?>&nbsp;<a class="session-end" href="<?= $view_link ?>"><span><?= __('here', 'intelligent-link')?></span></a>&nbsp;<?= __('and do it again.', 'intelligent-link')?></p>
                            <p><?= __('If the issue persists, please try clearing cookies or attempting with a different browser.', 'intelligent-link') ?></p>
                            <p><?= __('We use cookies to send requests with redirected links. Please enable cookies or the Prevent Cross-Site Tracking feature to ensure it works properly.', 'intelligent-link') ?></p>
                        </div>
                        <?= !empty($ads['ads_7']) ? '<div class="preplink-ads preplink-ads-2">' . $ads['ads_7'] . '</div>' : '' ?>
                    <?php else:?>
                        <div class="keyword-search">
                            <p><?= !empty(ep_settings()['redirect_notice']) ? ep_settings()['redirect_notice']: 'You are being redirected to an external link, please click the button below or go back to the previous page.'; ?></p>
                        </div>
                        <div class="preplink-ads preplink-ads-2">
                            <?= !empty($ads['ads_2']) ? '<div class="preplink-ads preplink-ads-2">' . $ads['ads_2'] . '</div>' : '' ?>
                        </div>
                        <div class="list-file-hide" id="buttondw">
                            <div class="ilgl-file-timer" style="display:none;">
                                <span class="ilgl-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= $time_conf ?>"><?= $time_conf ?></span>
                                <?php svg_render() ?>
                            </div>
                            <div class="ilgl-file-timer-btn <?php echo $prep_title ? 'cok-btn': '' ?>" style="opacity:0;pointer-events:none;visibility:hidden;">
                                <?php link_render($is_meta, $link_is_login, $link_no_login, $file_name, $file_size, $post_id, $meta_attr, $prep_title, $prep_request); ?>
                            </div>
                        </div>

                        <?php $faq_conf = get_option('preplink_faq', []);
                        if (!empty($faq_conf['faq_enabled']) && $faq_conf['faq_enabled'] == 1) : ?>
                            <?php faq_render(); ?>
                            <?= !empty($ads['ads_4']) ? '<div class="preplink-ads preplink-ads-4">' . $ads['ads_4'] . '</div>' : '' ?>
                        <?php endif; ?>

                        <?= !empty($ads['ads_5']) ? '<div class="preplink-ads preplink-ads-5">' . $ads['ads_5'] . '</div>' : '' ?>

                        <?php if (!empty(ep_settings()['preplink_related_post']) && ep_settings()['preplink_related_post'] == 1): ?>
                            <?php ep_related_post(ep_settings(), $post_id) ?>
                            <?= !empty($ads['ads_6']) ? '<div class="preplink-ads preplink-ads-6">' . $ads['ads_6'] . '</div>' : '' ?>
                        <?php endif; ?>
                    <?php endif;?>
                <?php else: ?>
                    <?php if ($post_image && $is_meta && has_post_thumbnail()) : ?>
                        <div class="ilgl-feat-outer">
                            <div class="featured-image">
                                <img src="<?= get_the_post_thumbnail_url($post_id, 'full'); ?>" class="prep-thumbnail">
                            </div>
                        </div>
                    <?php endif;?>
                    <div class="keyword-search">
                        <p><?= !empty(ep_settings()['redirect_notice']) ? ep_settings()['redirect_notice']: 'You are being redirected to an external link, please click the button below or go back to the previous page.'; ?></p>
                    </div>
                    <?php if (!empty(ep_settings()['ep_mode'])&& ep_settings()['ep_mode'] == 'default'): ?>
                        <div class="download-list">
                            <div class="download-item-box">
                                <div class="download-item">
                                    <div class="left">
                                        <a class="a-title image" href="javascript:void(0)">
                                            <?= has_post_thumbnail() ? get_the_post_thumbnail($post_id, 'thumbnail') : '<img src="'. esc_url(plugin_dir_url(__DIR__) . 'images/check_icon.png').'"/>'; ?>
                                        </a>
                                        <div class="post-download">
                                            <p class="title prep-title"><?= $file_title ?: $prep_title; ?></p>
                                            <p class="post-date"><?= __('Update:', 'intelligent-link') . ' ' . get_the_modified_date('d/m/Y') ?: get_the_date('d/m/Y'); ?></p>
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
                        <?= !empty($ads['ads_2']) ? '<div class="preplink-ads preplink-ads-2">' . $ads['ads_2'] . '</div>' : '' ?>
                        <div class="endpoint-progress" id="endpoint-progress" style="display:none;">
                            <p class="counter">0%</p>
                            <div class="bar"></div>
                            <span class="prep-btn-download" style="display: none">
                            <svg class="icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path d="M504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-143.6-28.9L288 302.6V120c0-13.3-10.7-24-24-24h-16c-13.3 0-24 10.7-24 24v182.6l-72.4-75.5c-9.3-9.7-24.8-9.9-34.3-.4l-10.9 11c-9.4 9.4-9.4 24.6 0 33.9L239 404.3c9.4 9.4 24.6 9.4 33.9 0l132.7-132.7c9.4-9.4 9.4-24.6 0-33.9l-10.9-11c-9.5-9.5-25-9.3-34.3.4z"></path>
                            </svg>
                            <span class="text-down"><?= __('Download', 'intelligent-link');?></span>
                        </span>
                        </div>

                        <div class="list-file-hide list-server-download" style="display: none">
                            <div class="ilgl-file-timer-btn">
                                <?php link_render($is_meta, $link_is_login, $link_no_login, $file_name, $file_size, $post_id, $meta_attr, $prep_title, $prep_request); ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="list-file-hide" id="buttondw">
                            <div class="ilgl-file-timer" style="display:none;">
                                <span class="ilgl-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= $time_conf ?>"><?= $time_conf ?></span>
                                <?php svg_render() ?>
                            </div>

                            <div class="preplink-ads preplink-ads-2">
                                <?= !empty($ads['ads_2']) ? '<div class="preplink-ads preplink-ads-2">' . $ads['ads_2'] . '</div>' : '' ?>
                            </div>

                            <div class="ilgl-file-timer-btn <?php echo $prep_title ? 'cok-btn': '' ?>" style="opacity:0;pointer-events:none;visibility:hidden;">
                                <?php link_render($is_meta, $link_is_login, $link_no_login, $file_name, $file_size, $post_id, $meta_attr, $prep_title, $prep_request); ?>
                            </div>
                        </div>
                    <?php endif;?>

                    <?= !empty($ads['ads_3']) ? '<div class="preplink-ads preplink-ads-3">' . $ads['ads_3'] . '</div>' : '' ?>

                    <?php $faq_conf = get_option('preplink_faq', []);
                    if (!empty($faq_conf['faq_enabled']) && $faq_conf['faq_enabled'] == 1) : ?>
                        <?php faq_render(); ?>
                        <?= !empty($ads['ads_4']) ? '<div class="preplink-ads preplink-ads-4">' . $ads['ads_4'] . '</div>' : '' ?>
                    <?php endif; ?>

                    <?= !empty($ads['ads_5']) ? '<div class="preplink-ads preplink-ads-5">' . $ads['ads_5'] . '</div>' : '' ?>

                    <?php if (!empty(ep_settings()['preplink_related_post']) && ep_settings()['preplink_related_post'] == 1): ?>
                        <?php ep_related_post(ep_settings(), $post_id) ?>
                        <?= !empty($ads['ads_6']) ? '<div class="preplink-ads preplink-ads-6">' . $ads['ads_6'] . '</div>' : '' ?>
                    <?php endif; ?>

                    <?php if (file_exists(get_template_directory() . '/comments.php') && !empty(ep_settings()['preplink_comment']) && (int)ep_settings()['preplink_comment'] == 1 && $is_meta) { ?>
                        <div class="comment"><?php comments_template(); ?></div>
                    <?php } ?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>
