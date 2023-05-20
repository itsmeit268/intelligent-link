<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

$postID             = get_the_ID();
$view_link          = get_permalink($postID);
$settings           = get_option('preplink_setting');
$advertising        = get_option('preplink_advertising');
$faqSetting         = get_option('preplink_faq');
$endpointSetting    = get_option('preplink_endpoint');
$postTitle          = get_the_title($postID) ? get_the_title($postID) : get_post_field('post_title', $postID);
$excerpt            = get_the_excerpt();
$prepLinkText       = isset($_COOKIE['prep_text_link']) ? $_COOKIE['prep_text_link'] : '';
$prepLinkURL        = isset($_COOKIE['prep_link_href']) ? $_COOKIE['prep_link_href'] : '';
$baseUrl            = str_replace('https://', '', !empty(home_url()) ? home_url() : get_bloginfo('url'));
$caption            = get_the_post_thumbnail_caption() ?: get_the_title();

if (stripos($prepLinkText, 'Tải') === 0 ||
    stripos($prepLinkText, 'tải') === 0 ||
    stripos($prepLinkText, 'download') === 0 ||
    stripos($prepLinkText, 'Download') === 0) {
    $prepLinkText = trim(str_ireplace(array('Tải', 'tải', 'download', 'Download'), '', $prepLinkText));
}

add_action('wp_head', function () {
    ?>
    <!-- Ads adsterra itsmeit.co-->
    <script async="async" data-cfasync="false" src="//pl18124507.highrevenuegate.com/22704e0d8a7af0e52d2b68f097fc3419/invoke.js"></script>
    <script async="async" data-cfasync="false" src="//pl18104634.highrevenuegate.com/3c814d791e7eab81bce23860cf13946a/invoke.js"></script>
    <?php
});

add_action('wp_print_scripts', function () {
    wp_dequeue_script('fixedtoc-js');
    wp_dequeue_script('fixedtoc-js-js-extra');
    wp_dequeue_script('rbswiper-js');
    wp_dequeue_script( 'enlighterjs' );
    wp_dequeue_style('enlighterjs');
});

function remove_enlighterjs_script() {
    wp_dequeue_script('fixedtoc-js');
    wp_dequeue_script('fixedtoc-js-js-extra');
    wp_dequeue_script('rbswiper-js');
    wp_dequeue_script( 'enlighterjs' );
    wp_dequeue_style('enlighterjs');
}
add_action( 'wp_enqueue_scripts', 'remove_enlighterjs_script', 10 );

?>
<?php if (!empty($settings['preplink_custom_style'])) {
    echo "<style>{$settings['preplink_custom_style']}</style>";
} ?>

<?php if (file_exists(get_template_directory() . '/header.php')) get_header(); ?>

<div class="single-page without-sidebar sticky-sidebar" id="prep-link-single-page" data-url="<?= $prepLinkURL ?>">
    <header class="single-header">
        <h1 class="s-title">
            <a class="adsterra" href="javascript:void(0)"><?= !empty($prepLinkText) ? __('Download','prep-link') .' '. $prepLinkText: $postTitle; ?></a>
        </h1>
        <div class="related_post" style="margin: 0 25px;">
            <div id="container-3c814d791e7eab81bce23860cf13946a"></div>
        </div>
    </header>
    <div class="rb-small-container preplink-padding">
        <div class="grid-container">
            <div class="s-ct">
                <div class="s-ct-inner">
                    <div class="e-ct-outer" id="container">
                        <div class="entry-content rbct">
                            <?php if (empty($prepLinkURL) || empty($prepLinkText)) : ?>
                                <div class="session-expired">
                                    <p><?= __('Your session has ended, please click ', 'prep-link')?><a href="<?= $view_link ?>"><span><?= __('here', 'prep-link')?></span></a><?= __(' and do it again.', 'prep-link')?></p>
                                    <p><?= __('If the issue persists, try clearing your cookies or browser history and attempt again.', 'prep-link') ?></p>
                                </div>
                            <?php else: ?>
                                <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_1']) && (int)$advertising['preplink_advertising_1'] == 1 && !empty($advertising['preplink_advertising_code_1'])): ?>
                                    <div class="preplink-ads preplink-ads-1">
                                        <?= $advertising['preplink_advertising_code_1'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($endpointSetting['preplink_image']) && $endpointSetting['preplink_image'] && has_post_thumbnail($postID)) : ?>
                                    <div class="s-feat-outer">
                                        <div class="s-feat">
                                            <div class="featured-image">
                                                <img src="<?= get_the_post_thumbnail_url($postID, 'full'); ?>"
                                                     class="prep-thumbnail" alt="<?= $postTitle ?>" title="<?= $postTitle ?>">
                                            </div>
                                        </div>
                                        <div class="feat-caption meta-text">
                                            <span class="caption-text meta-bold">
                                                <a class="caption-textlnk adsterra"
                                                   style="color: #282828; font-weight: normal; margin-left: -10px; font-size: 14px;" href="javascript:void(0)">
                                                    <?= substr($caption, -14) === ' (illustration)' ? $caption : $caption . ' (illustration)'; ?>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="preplink-ads preplink-ads-2">
                                    <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_2']) && (int)$advertising['preplink_advertising_2'] == 1 && !empty($advertising['preplink_advertising_code_2'])): ?>
                                        <?= $advertising['preplink_advertising_code_2'] ?>
                                    <?php endif; ?>
                                </div>

                                <?php
                                if (!empty($endpointSetting['preplink_excerpt']) && $endpointSetting['preplink_excerpt'] && (strpos($excerpt, '<table>') !== false || strpos($excerpt, '<tbody>') !== false)) {
                                    ?>
                                    <div class="post-excerpt">
                                        <h3 class="app-title"><?= __('Information about the Features','prep-link')?></h3>
                                        <?= $excerpt ?>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="post-excerpt">
                                        <h3 class="app-title"><?= __('Description', 'prep-link')?></h3>
                                        <?= $excerpt ?>
                                    </div>
                                    <?php
                                }
                                ?>

                                <div class="preplink-ads preplink-ads-3">
                                    <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_3']) && (int)$advertising['preplink_advertising_3'] == 1 && !empty($advertising['preplink_advertising_code_3'])): ?>
                                        <?= $advertising['preplink_advertising_code_3'] ?>
                                    <?php endif; ?>
                                </div>

                                <div class="preplink-gg-s">
                                    <ul style="padding: 5px;">
                                        <li>
                                            <?= __('If the file name does not match the article content, please click', 'prep-link')?>
                                            <a href="<?= $view_link ?>"><span><?= __('here', 'prep-link')?></span></a><?= __(' and do it again.', 'prep-link')?>
                                        </li>
                                        <li>
                                            <?= __('The extract password has been attached, please check it in the zip file.', 'prep-link')?>
                                        </li>
                                        <li>
                                            <?= __('To search for a specific resource or content on the internet, you can visit', 'prep-link')?>
                                            <a href="https://www.google.com/search?q=<?=$prepLinkText.' '.$baseUrl?>"><?= __('https://google.com', 'prep-link')?></a>
                                            <?= __('and enter your search query as:', 'prep-link')?>
                                            <a href="https://www.google.com/search?q=<?=$prepLinkText.' '.$baseUrl?>"><?= __('keyword +', 'prep-link') . ' '. $baseUrl?></a>
                                        </li>

                                    </ul>
                                </div>

                                <div class="download-list">
                                    <div class="download-item-box">
                                        <div class="download-item">
                                            <div class="left">
                                                <a class="adsterra image" href="javascript:void(0)"><?php the_post_thumbnail('thumbnail'); ?></a>
                                                <div class="post-download">
                                                    <p class="tittle">
                                                        <a class="adsterra p-tittle" href="javascript:void(0)"><?= __('Download', 'prep-link') .' '. $prepLinkText ?></a>
                                                    </p>
                                                    <p class="post-date"><?= __('Update:', 'prep-link') . ' ' . get_the_date('d/m/Y')?></p>
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

                                <div class="preplink-ads preplink-ads-4">
                                    <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_4']) && (int)$advertising['preplink_advertising_4'] == 1 && !empty($advertising['preplink_advertising_code_4'])): ?>
                                        <?= $advertising['preplink_advertising_code_4'] ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($faqSetting['preplink_faq1_enabled']) && $faqSetting['preplink_faq1_enabled'] == 1 && !empty($faqSetting['preplink_faq1_description'])) : ?>
                                    <div class="faq-download">
                                        <h3 class="faq-title"><?= !empty($faqSetting['preplink_faq1_title']) ? $faqSetting['preplink_faq1_title'] : 'FAQ' ?></h3>
                                        <?= $faqSetting['preplink_faq1_description'] ?>
                                    </div>
                                <?php endif; ?>

                                <div class="preplink-ads preplink-ads-5">
                                    <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_5']) && (int)$advertising['preplink_advertising_5'] == 1 && !empty($advertising['preplink_advertising_code_5'])): ?>
                                        <?= $advertising['preplink_advertising_code_5'] ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($faqSetting['preplink_faq2_enabled']) && $faqSetting['preplink_faq2_enabled'] == 1 && !empty($faqSetting['preplink_faq2_description'])) : ?>
                                    <div class="faq-download">
                                        <h3 class="faq-title"><?= !empty($faqSetting['preplink_faq2_title']) ? $faqSetting['preplink_faq2_title'] : 'FAQ' ?></h3>
                                        <?= $faqSetting['preplink_faq2_description'] ?>
                                    </div>
                                <?php endif; ?>

                                <div class="preplink-ads preplink-ads-6">
                                    <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_6']) && (int)$advertising['preplink_advertising_6'] == 1 && !empty($advertising['preplink_advertising_code_6'])): ?>
                                        <?= $advertising['preplink_advertising_code_6'] ?>
                                    <?php endif; ?>
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

                                <div class="related_post">
                                    <!-- Ads adsterra itsmeit.co-->
                                    <div id="container-22704e0d8a7af0e52d2b68f097fc3419"></div>
                                </div>

                                <?php if (!empty($endpointSetting['preplink_related_post']) && $endpointSetting['preplink_related_post'] == 1): ?>
                                    <div class="related_post">
                                        <h3 class="suggestions-post"><?= __('Related Posts','prep-link') ?></h3>
                                        <?php
                                        $categories = get_the_category();
                                        $category_ids = array();
                                        foreach ($categories as $category) {
                                            $category_ids[] = $category->term_id; // Lấy ID của các category
                                        }

                                        $args = array(
                                            'category__in' => $category_ids, // Lấy các bài viết trong category có ID tương ứng
                                            'post__not_in' => array(get_the_ID()), // Loại bỏ bài viết hiện tại
                                            'posts_per_page' => !empty($settings['preplink_related_number']) ? $settings['preplink_related_number'] : 4, // Lấy 10 bài viết
                                            'orderby' => 'rand',
                                            'order' => 'DESC'
                                        );

                                        $related_posts = get_posts($args); // Lấy các bài viết liên quan

                                        // Hiển thị các bài viết liên quan
                                        if ($related_posts) {
                                            echo '<div class="related-posts-grid">';
                                            foreach ($related_posts as $post) {
                                                setup_postdata($post);
                                                $post_categories = get_the_category($post->ID);
                                                ?>
                                                <div class="related-post">
                                                    <a class="related-link" href="<?= get_permalink($post); ?>">
                                                        <?php if (has_post_thumbnail()) {
                                                            echo get_the_post_thumbnail($post, 'thumbnail');
                                                        } ?>
                                                        <div class="related-content">
                                                            <h5 class="entry-title">
                                                                <a class="dl-p-url"
                                                                   href="<?= get_permalink($post); ?>"><?= get_the_title($post); ?></a>
                                                            </h5>
                                                            <div class="prep-meta">
                                                                <span class="prep-category">
                                                                    <?php foreach ($post_categories as $i => $category) {
                                                                        echo '<a class="category-link" href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                                                        if ($i < count($post_categories) - 1) {
                                                                            echo ' | ';
                                                                        }
                                                                    } ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                            echo '</div>';
                                            ?>
                                            <?php
                                            wp_reset_postdata();
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <div class="preplink-ads preplink-ads-7">
                                    <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_7']) && (int)$advertising['preplink_advertising_7'] == 1 && !empty($advertising['preplink_advertising_code_7'])): ?>
                                        <?= $advertising['preplink_advertising_code_7'] ?>
                                    <?php endif; ?>
                                </div>

                                <?php
                                if (file_exists(get_template_directory() . '/comments.php') && (int)$endpointSetting['preplink_comment'] == 1) {
                                    ?>
                                    <div class="comment"><?php comments_template(); ?></div><?php
                                }
                                ?>
                                <div class="preplink-ads preplink-ads-8">
                                    <?php if (aicp_can_see_ads() && !empty($advertising['preplink_advertising_8']) && (int)$advertising['preplink_advertising_8'] == 1 && !empty($advertising['preplink_advertising_code_8'])): ?>
                                        <?= $advertising['preplink_advertising_code_8'] ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        var $adsterra = $('.adsterra');
        if ($(window).width() > 700) {
            $adsterra.attr({
                href: 'https://www.highrevenuegate.com/nt9jff03h?key=f5215f21822ff7e20dcf20cdb60f73be',
                target: '_blank'
            });
            (function(s,u,z,p){s.src=u,s.setAttribute('data-zone',z),p.appendChild(s);})(document.createElement('script'),'https://inklinkor.com/tag.min.js',5602403,document.body||document.documentElement);
        }
        $adsterra.on('click', function () {
            $('#enpoint-progress').trigger('click');
        });
    });
</script>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>
