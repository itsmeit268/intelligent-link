<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @since      1.0.0
 * @link       https://github.com/itsmeit268/preplink
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

/**
 * Allows filtering of the robots meta data.
 *
 * @param array $robots The meta robots directives.
 */
//add_filter('rank_math/frontend/robots', function ($robots) {
//    $robots = array('index' => 'noindex', 'follow' => 'nofollow', 'archive' => 'noarchive', 'snippet' => 'nosnippet');
//    return $robots;
//});
global $post, $wp_query;
$postID = get_the_ID();
$view_link = get_permalink($postID);
$postTitle = !empty(get_the_title()) ? get_the_title() : $post->post_title;
$downloadURL = !empty($_COOKIE['pre_url_go']) ? $_COOKIE['pre_url_go'] : '';

$excerpt = get_the_excerpt();

add_action('wp_head', 'add_script_ads');
function add_script_ads()
{
    ?><?php

    wp_enqueue_style('prep-link-css', plugin_dir_url(__DIR__) . 'css/prep-link.css', array(), '1.0.0', 'all');
//    wp_enqueue_script('ads-check-js', get_theme_file_uri('/itsmeit/js/ads_itsmeit.js'), array('jquery'), 1.0.0, true);
}

?>

<?php if (file_exists(get_template_directory() . '/header.php')) get_header(); ?>
<div class="site-wrap">
    <div class="s-ct-wrap has-lsl">
        <div class="s-ct-inner">
            <div class="l-shared-sec-outer"></div>
            <div id="container" class="e-ct-outer">
                <header class="single-header">
                    <h1 class="s-title">
                        <a href="<?= $view_link ?>" title="<?= $postTitle ?>" target="_blank"><?= $postTitle ?></a>
                    </h1>
                </header>
                <div class="s-feat-outer">
                    <div class="s-feat">
                        <div class="featured-image">
                            <?php if (has_post_thumbnail($postID)): ?>
                                <img src="<?= get_the_post_thumbnail_url($postID, 'full'); ?>" class=""
                                     alt="<?= $postTitle ?>" title="<?= $postTitle ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="feat-caption meta-text">
                        <span class="caption-text meta-bold">How to download Chinese TikTok (Douyin) v24.6.0 app APK for Android</span>
                    </div>
                </div>

                <div class="ads-itsmeit" style="max-width: 850px;">
                    <div class="aicp">
                        <!--  ads1-->
                    </div>
                </div>
                <span style="display: inline-block; margin-left: 10px;">
                <img style="width: 20px;" draggable="false" role="img" class="emoji entered pmloaded lazyloaded"
                     alt="📌"
                     src="https://s.w.org/images/core/emoji/14.0.0/svg/1f4cc.svg">
                If the download link is not ready after a few seconds, please disable your <strong>Adblock</strong> and try refreshing the page again.
            </span>
                <?php
                if (strpos($excerpt, '<table>') !== false || strpos($excerpt, '<tbody>') !== false) {
                    ?>
                    <div class="post-excerpt">
                        <h3 class="app-title">FEATURES INFORMATION</h3>
                        <?= $excerpt ?>
                    </div>
                    <?php
                }
                ?>
                <div class="ads-itsmeit" style="max-width: 850px;">
                    <div class="aicp">
                        <!--  ads 2-->
                    </div>
                </div>
                <div class="download-list">
                    <div class="download-item-box">
                        <div class="download-item">
                            <div class="left">
                                <a href="<?= $view_link ?>" class="image"><?php the_post_thumbnail('thumbnail'); ?></a>
                                <div class="post-download">
                                    <p class="tittle">
                                        <a href="<?= $view_link ?>" class="clickable"><?= get_the_title($postID); ?></a>
                                    </p>
                                    <p class="post-date">Update: <?= get_the_date('d/m/Y'); ?></p>
                                </div>
                            </div>
                            <div class="right">
                                <div class="download-btn">
                                    <a href="<?= $downloadURL ?>" class="clickable">
                                        <svg class="icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                             width="24"
                                             height="24" viewBox="0 0 24 24">
                                            <path d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ads-itsmeit" style="max-width: 850px;">
                    <div class="aicp">
                        <!--  ads 3-->
                    </div>
                </div>
                <div class="faq-download">
                    <h3 class="faq-title">Notes before continuing</h3>
                    <div id="rank-math-faq" class="rank-math-block">
                        <div class="rank-math-list ">
                            <div class="rank-math-list-item">
                                <h4 class="rank-math-question"><strong>Never mind the Play Protect warning!</strong>
                                </h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>As you know, MOD means editing APK files. As a result, the MOD APK files
                                            will not match the version available on the Google Play Store.
                                        </li>
                                        <li>That's why Play Protect now warns you every time you want to install MOD
                                            APK.
                                        </li>
                                        <li>So the best way is to "turn off Play Protect" completely and never mind it
                                            if you want to install and use the MOD APK.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="rank-math-list-item">
                                <h4 class="rank-math-question"><strong>How to install Application?</strong></h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>Each App/Software/Tool version has a different installation method. So on
                                            the download page of each version, we have specific installation
                                            instructions.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ads-itsmeit" style="max-width: 850px;">
                    <div class="aicp">
                        <!--  ads 3-->
                    </div>
                </div>
                <div class="faq-download"><h3 class="faq-title">Download FAQs</h3>
                    <div id="rank-math-faq" class="rank-math-block">
                        <div class="rank-math-list ">
                            <div class="rank-math-list-item">
                                <h4 class="rank-math-question"><strong>How to download?</strong></h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>Just wait a few seconds and the download button will appear.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="rank-math-list-item"><h4 class="rank-math-question"><strong>Experiencing Game
                                        Download Issues? Try These Solutions.</strong></h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>If the game download link is slow, it may be due to my server being under
                                            maintenance or overloaded. You can try downloading again in a few minutes.
                                        </li>
                                        <li>If the download link does not exist or returns a 404 error, please leave a
                                            comment or send an email to itsmeit.biz@gmail.com for support.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="rank-math-list-item"><h4 class="rank-math-question"><strong>How to update
                                        without data loss?</strong></h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>If you uninstall the old version before installing the new one, you will
                                            lose all your data. To preserve your data, never uninstall old versions;
                                            instead, install the new version and choose to override the old one.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="rank-math-list-item"><h4 class="rank-math-question"><strong>MOD menu not
                                        showing?</strong></h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>Make sure you have manually granted "show over other apps" permission if
                                            Android 6.0 or later, by going to Settings &gt; Apps &gt; Game Name &gt;
                                            Permissions | Display over other apps (Display over other apps).
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="rank-math-list-item"><h4 class="rank-math-question"><strong>The description is
                                        not correct with the downloaded file?</strong></h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>If there is a mistake between the description and the downloaded file,
                                            please report it to us via the contact section at the bottom of the page.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="rank-math-list-item"><h4 class="rank-math-question"><strong>Is the file I
                                        download from ITSMEIT safe?</strong></h4>
                                <div class="rank-math-answer">
                                    <ul>
                                        <li>Of course, every file is checked by antivirus software before being uploaded
                                            to the system.
                                        </li>
                                        <li>Our hosting server is also regularly checked to avoid any threats.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ads-itsmeit" style="max-width: 850px;">
                    <div class="aicp">
                        <!--  ads 4-->
                    </div>
                </div>
                <div class="related_post">
                    <h3 class="suggestions-post">GỢI Ý CHO BẠN</h3>
                    <?php
                    $categories = get_the_category(); // Lấy category của bài viết hiện tại
                    $category_ids = array(); // Tạo mảng rỗng để chứa ID của category

                    foreach ($categories as $category) {
                        $category_ids[] = $category->term_id; // Lấy ID của các category
                    }

                    $args = array(
                        'category__in' => $category_ids, // Lấy các bài viết trong category có ID tương ứng
                        'post__not_in' => array(get_the_ID()), // Loại bỏ bài viết hiện tại
                        'posts_per_page' => 10, // Lấy 5 bài viết
                        'orderby' => 'date', // Sắp xếp theo ngày đăng
                        'order' => 'DESC' // Sắp xếp giảm dần
                    );

                    $related_posts = get_posts($args); // Lấy các bài viết liên quan

                    // Hiển thị các bài viết liên quan
                    if ($related_posts) {
                        echo '<div class="related-posts-grid">';
                        foreach ($related_posts as $post) {
                            setup_postdata($post); ?>
                            <div class="related-post">
                                <a class="related-link" href="<?= get_permalink($post); ?>">
                                    <?php if (has_post_thumbnail()) { echo get_the_post_thumbnail($post, 'thumbnail');} ?>
                                    <div class="related-content">
                                        <h5 class="entry-title">
                                            <a class="dl-p-url" href="<?= get_permalink($post); ?>"
                                               rel="bookmark"><?= get_the_title($post); ?></a></h5>
                                        <div class="p-meta">
                                            <div class="meta-inner is-meta">
                                    <span class="meta-el meta-category meta-bold">
                                        <a class="category-3"
                                           href="https://itsmeit.biz/linux.html">Ubuntu | Linux</a>
                                    </span>
                                            </div>
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
                <div class="ads-itsmeit" style="max-width: 850px;">
                    <div class="aicp">
                        <!--  ads 5-->
                    </div>
                </div>
                <?php
                if (file_exists(get_template_directory() . '/comments.php')) {
                    ?><div class="comment"><?php comments_template(); ?></div><?php
                }
                ?>
                <div class="ads-itsmeit" style="max-width: 850px;">
                    <div class="aicp">
                        <!--  ads 6-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>
