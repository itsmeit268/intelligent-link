<?php
/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
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

<style>
    .faq-title,.suggestions-post,.td-file,.td-size,.tr-thd>td{text-transform:uppercase;text-transform:uppercase}.single-header,.single-page{max-width:890px;margin:0 auto}.site-wrap{z-index:1;min-height:45vh}.site-content,.site-wrap{position:relative;display:block;margin:0;padding:0}.edge-padding,.grid-container>*{padding-right:20px;padding-left:20px}.single-header{position:static;width:100%;margin:0 auto}.rb-small-container{position:static;display:block;width:100%;margin-right:auto;margin-left:auto}.grid-container{flex-flow:row nowrap;position:relative;display:flex;flex-flow:row wrap;flex-basis:100%;margin-right:-30px;margin-left:-30px}.grid-container>*,body .without-sidebar .grid-container>*{flex:0 0 100%;width:100%}.session-expired{text-align:center;color:#051d24;font-size:16px;margin:0 20px}.single .without-sidebar .s-ct{flex-basis:100%;width:100%;max-width:890px!important;margin-right:auto;margin-left:auto}.single-page .s-ct{min-height:45vh;padding-bottom:40px}.rbct{position:relative;display:block}.feat-caption,.td-link,.title,td,th,tr td:nth-child(2){text-align:left}.dl-p-url,.prep-category{-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}#container a,#download-button{text-decoration:none}h1.s-title{margin:20px 0;font-size:28px;text-align:center}.related-posts-grid{display:grid;grid-template-columns:repeat(2,1fr);grid-gap:20px}.related-content>.entry-title{margin:0 0 0 10px;display:block}.related-content{width:100%}.prep-meta{margin:0 0 0 10px}.related-post{border:1px solid #ccc;padding:10px;display:flex;width:100%}.prep-category{display:block;font-size:14px;display:-webkit-box}.prep-category a{color:#333!important;font-weight:400!important}.prep-category a:hover{color:#f5023b!important}.related-link{position:relative;display:flex;color:inherit;max-width:100px;max-height:100px}.related-link>img,.related-link>picture{position:relative;border-radius:15px;aspect-ratio:1/1}.related-link>img,.related-link>picture>img{max-width:100%;height:auto;vertical-align:middle;border-radius:5px;aspect-ratio:1/1;transition:opacity 1s;opacity:1;display:inline-block}.download-item-box{line-height:30px;background:#f8f8f8;max-height:120px;min-width:1px;padding:15px 10px;color:#1a1a1a}.download-item{display:flex;flex-wrap:wrap;justify-content:space-between}.download-item .left{flex:0 0 calc(60% - 10px)}.download-item .right{flex:0 0 calc(40% - 10px);display:flex;align-items:center;justify-content:flex-end}.download-item .left .image{overflow:hidden;border-radius:5px;float:left;margin-right:10px;background-size:cover;max-width:80px;position:relative}.title>a{color:#334862!important}.link-session-expired:hover{color:#ff0059!important}.download-item .prep-link-download-btn .clickable{display:inline-block;background-color:#0693e3;color:#fff;padding:2px 15px;border-radius:3px;line-height:1;cursor:pointer}.download-item .prep-link-download-btn .clickable:hover{background:#026eab;opacity:1}.tittle{margin-bottom:-5px}.e-ct-outer>*,h3.prep-link-title{margin-bottom:10px!important}.post-date{font-size:15px;color:#071d46;font-weight:500;white-space:nowrap;overflow:hidden}.app-title,.suggestions-post{font-size:20px;text-transform:uppercase;border-bottom:2px solid #000;width:100%;margin-bottom:10px}tr{background:#f4f7f8;border-bottom:1px solid #fff;margin-bottom:5px}tr:nth-child(2n){background:#e8eeef}td,th{padding:8px 10px;font-weight:300}.wp-block-table td,.wp-block-table th{border:none!important}td:nth-of-type(2){text-align:right}.wp-block-table>table>tbody>tr>td,table>tbody>tr>td{padding:5px}tr:hover{background-color:#d5d5d5;color:#ff0048}.s-feat img,.s-feat-holder img{display:block;width:100%;object-fit:cover}img{-webkit-object-fit:cover;object-fit:cover}.featured-image>picture>img{border:1px solid #dfdfdf!important}.caption-text{position:relative;display:block;font-size:15px;color:#282828}.feat-caption .caption-text:before{position:relative;display:inline-flex;width:50px;height:1px;margin-right:12px;padding-top:.75em;content:'';vertical-align:top;border-bottom:1px solid #0b6cea}.image>picture>img{border-radius:5px}.dl-p-url{color:#06222e!important;font-size:16px!important;display:-webkit-box}.category-3:hover,.dl-p-url:hover{color:#ff0048!important;-webkit-text-decoration-color:var(--g-color);text-decoration-color:var(--g-color);text-decoration:underline!important}.preplink-ads{overflow:hidden;text-align:center;display:block}ul li{list-style:circle;color:#1e3b50}.faq-active>strong,ul li>a{color:var(--g-color)}ul li>a:hover{color:#e30b2c}#prep-link-faq,.feat-caption{margin-top:10px;position:relative}#prep-link-faq,#prep-link-faq .prep-link-answer,#prep-link-faq .prep-link-answer p{background:#fff}#prep-link-faq{margin-bottom:19px;border:1px solid #e0e0e0;border-top:4px solid #22a8e2;padding:10px}h3.prep-link-title{font-size:18px;margin-top:5px;font-weight:500;border-bottom:1px solid #efe9f5}#prep-link-faq .prep-link-question{font-style:normal;font-weight:500;font-size:18px;line-height:28px;padding:5px 0;margin-top:0;margin-bottom:0;position:relative;cursor:pointer}#prep-link-faq .prep-link-question:after{content:"";background:url(//itsmeit.biz/wp-content/uploads/2022/04/chevron-down-black.svg) right center no-repeat;position:absolute;right:-5px;top:40%;z-index:1;width:15px;height:15px;-webkit-transform:rotate(0);-moz-transform:rotate(0);-ms-transform:rotate(0);-o-transform:rotate(0);transform:rotate(0);transition:.3s}#prep-link-faq .prep-link-question.faq-active:after{-webkit-transform:rotate(180deg);-moz-transform:rotate(180deg);-ms-transform:rotate(180deg);-o-transform:rotate(180deg);transform:rotate(180deg)}#prep-link-faq .prep-link-list-item:not(first-child) .prep-link-answer,.grecaptcha-badge{display:none}#prep-link-faq .prep-link-list-item:not(:last-child){border-bottom:1px solid #d7cfcf}.prep-link-question>strong{font-size:18px;color:#06222e}.prep-link-answer p{color:#06222e}.prep-link-answer>ul{margin-bottom:10px}#wpdcom{max-width:100%!important}.category-link{font-size:14px;font-weight:400;color:#333}.clickable:hover{background:#2488c1}.enpoint-progress{position:relative;max-width:100%;height:35px;margin-bottom:20px;background-color:#e2e2e2;border-radius:40px}.counter{position:absolute;left:50%;transform:translate(-50%,0);z-index:1}.enpoint-progress .bar{position:absolute;border-radius:40px;height:100%;width:0%;background-color:#0693e3}.prep-btn-download{position:relative;height:100%;width:100%;display:block}.prep-btn-download>.icon{width:18px;color:#fff;position:absolute;top:2px;right:25px}.text-down{color:#fff;cursor:pointer;position:absolute;left:-20px;top:-2px;font-weight:700}.tr-thd>td{font-weight:600}.tr-tbd>td,.tr-thd>td{padding-left:10px!important;text-align:center}@media only screen and (max-device-width:480px){#container,.session-expired{padding:0}h1.s-title{margin:15px 0;font-size:20px}.related-posts-grid{display:block}.related-post{width:100%;margin-bottom:5px}.download-item .left .image{max-width:65px}.download-item .left{flex:0 0 calc(80% - 10px);margin-left:10px}.download-item .right{flex:0 0 calc(20% - 10px);margin-right:5px}.prep-link-download-btn{margin-right:5px;margin-bottom:5px}.tittle{line-height:22px}.related-content>.entry-title>.dl-p-url,.tittle{overflow:hidden;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;text-overflow:ellipsis;display:-moz-box;-moz-box-orient:vertical;-moz-line-clamp:2;display:-ms-box;-ms-box-orient:vertical;-ms-line-clamp:2;display:box;box-orient:vertical;line-clamp:2}.related-content>.entry-title>.dl-p-url{font-size:18px!important;line-height:22px}.related-link{display:initial}.preplink-ads{padding:0!important;margin:0}.comment,.e-shared-sec,.faq-download,.post-excerpt,.related_post,.s-feat-outer,.single-header{padding:10px 15px}.preplink-gg-s p{margin:0 20px;text-align:justify}.enpoint-progress{margin:0 10px 20px}.list-server-download{margin:0 10px 0 5px}}
</style>

<div class="single-page without-sidebar sticky-sidebar" id="prep-link-single-page" data-url="<?= $prepLinkURL ?>" style="max-width: 890px; margin: 0 auto;">
    <header class="single-header">
        <h1 class="s-title">
            <a class="adsterra" href="javascript:void(0)"><?= $postTitle; ?></a>
        </h1>
        <?php if (isset($advertising['preplink_advertising_3']) && (int)$advertising['preplink_advertising_3'] == 1 && !empty($advertising['preplink_advertising_code_3'])): ?>
            <div class="preplink-ads preplink-ads-3" style="margin: 0 25px;">
                <?= $advertising['preplink_advertising_code_3'] ?>
            </div>
        <?php endif; ?>
    </header>
    <div class="rb-small-container preplink-padding">
        <div class="grid-container">
            <div class="s-ct">
                <div class="s-ct-inner">
                    <div class="e-ct-outer" id="container">
                        <div class="entry-content rbct">
                            <?php if (empty($prepLinkURL) || empty($prepLinkText)) : ?>
                                <div class="session-expired">
                                    <p><?= __('Your session has ended, please click', 'prep-link')?>&nbsp;<a href="<?= $view_link ?>"><span style="color: #0a4ad0;"><?= __('here', 'prep-link')?></span></a>&nbsp;<?= __('and do it again.', 'prep-link')?></p>
                                    <p><?= __('If the issue persists, try clearing your cookies or browser history and attempt again.', 'prep-link') ?></p>
                                </div>
                                <?php if (isset($advertising['preplink_advertising_4']) && (int)$advertising['preplink_advertising_4'] == 1 && !empty($advertising['preplink_advertising_code_4'])): ?>
                                    <div class="preplink-ads preplink-ads-4" style="margin: 0 25px;">
                                        <?= $advertising['preplink_advertising_code_4'] ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ( isset($advertising['preplink_advertising_1']) && (int)$advertising['preplink_advertising_1'] == 1 && !empty($advertising['preplink_advertising_code_1'])): ?>
                                    <div class="preplink-ads preplink-ads-1">
                                        <?= $advertising['preplink_advertising_code_1'] ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
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
                                                <a class="caption-textlnk adsterra" style="color: #282828; font-weight: normal; margin-left: -10px; font-size: 14px;" href="javascript:void(0)">
                                                    <?= substr($caption, -14) === ' (illustration)' ? $caption : $caption . ' (illustration)'; ?>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ( isset($advertising['preplink_advertising_2']) && (int)$advertising['preplink_advertising_2'] == 1 && !empty($advertising['preplink_advertising_code_2'])): ?>
                                    <div class="preplink-ads preplink-ads-2">
                                        <?= $advertising['preplink_advertising_code_2'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php
                                if (!empty($endpointSetting['preplink_excerpt']) && (int) $endpointSetting['preplink_excerpt'] == 1) {
                                    if ((strpos($excerpt, '<table>') !== false || strpos($excerpt, '<tbody>') !== false)) {
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
                                }
                                ?>

                                <?php if (!empty($faqSetting['preplink_faq1_enabled']) && $faqSetting['preplink_faq1_enabled'] == 1 && !empty($faqSetting['preplink_faq1_description'])) : ?>
                                    <div class="faq-download">
                                        <h3 class="faq-title"><?= !empty($faqSetting['preplink_faq1_title']) ? $faqSetting['preplink_faq1_title'] : 'FAQ' ?></h3>
                                        <?= $faqSetting['preplink_faq1_description'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($faqSetting['preplink_faq2_enabled']) && $faqSetting['preplink_faq2_enabled'] == 1 && !empty($faqSetting['preplink_faq2_description'])) : ?>
                                    <div class="faq-download">
                                        <h3 class="faq-title"><?= !empty($faqSetting['preplink_faq2_title']) ? $faqSetting['preplink_faq2_title'] : 'FAQ' ?></h3>
                                        <?= $faqSetting['preplink_faq2_description'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($advertising['preplink_advertising_5']) && (int)$advertising['preplink_advertising_5'] == 1 && !empty($advertising['preplink_advertising_code_5'])): ?>
                                    <div class="preplink-ads preplink-ads-5">
                                        <?= $advertising['preplink_advertising_code_5'] ?>
                                    </div>
                                <?php endif; ?>

                                <div class="download-list">
                                    <div class="download-item-box">
                                        <div class="download-item">
                                            <div class="left">
                                                <a class="adsterra image" href="javascript:void(0)"><?php the_post_thumbnail('thumbnail'); ?></a>
                                                <div class="post-download">
                                                    <p class="tittle"><?= __('Download', 'prep-link') .' '. $prepLinkText ?></p>
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

                                <?php if (isset($advertising['preplink_advertising_6']) && (int)$advertising['preplink_advertising_6'] == 1 && !empty($advertising['preplink_advertising_code_6'])): ?>
                                    <div class="preplink-ads preplink-ads-6">
                                        <?= $advertising['preplink_advertising_code_6'] ?>
                                    </div>
                                <?php endif; ?>

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

                                <?php
                                $link_download_data = get_post_meta(get_the_ID(), 'link-download-metabox', true);
                                ?>
                                <div class="list-server-download" style="display: none">
                                    <figure class='wp-block-table'>
                                        <table>
                                            <tbody>
                                            <tr class="tr-thd">
                                                <td style="text-align: left"><?= __('Alternative Link', 'prep-link')?></td>
                                                <td><?= __('Size', 'prep-link')?></td>
                                                <td><?= __('File', 'prep-link')?></td>
                                                <td><?= __('Update', 'prep-link' )?></td>
                                            </tr>
                                            <?php
                                            for ($i = 1; $i <= 6; $i++) {
                                                $title = !empty($link_download_data["title-$i"]) ? esc_html($link_download_data["title-$i"]) : 'Link - Server '. ($i + 1);
                                                $link  = !empty($link_download_data["link-$i"]) ? esc_url($link_download_data["link-$i"]): '';
                                                $size  = !empty($link_download_data["size-$i"]) ? esc_html($link_download_data["size-$i"]): '';
                                                $file  = !empty($link_download_data["file-$i"]) ? esc_html($link_download_data["file-$i"]): '';
                                                $date = !empty($link_download_data["date-$i"]) ? date('d-m-Y', strtotime($link_download_data["date-$i"])) : '';
                                                ?>
                                                <?php
                                                if ($link) {
                                                    ?>
                                                    <tr class="tr-tbd">
                                                        <td class="td-link" style="text-align: left">
                                                            <a class="gogo-link" href="javascript:void(0);"
                                                               rel="nofollow noopener noreferrer"
                                                               data-url="<?= base64_encode($link); ?>"><?= $title; ?></a>
                                                        </td>
                                                        <td class="td-size"><?= $size; ?></td>
                                                        <td class="td-file"><?= $file; ?></td>
                                                        <td class="td-date"><?= $date; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </figure>
                                </div>
                                <?php
                                ?>

                                <div class="preplink-gg-s">
                                    <p>
                                        <?= __('To search for a specific resource or content on the internet, you can visit', 'prep-link')?>
                                        <a target="_blank" href="https://www.google.com/search?q=<?=$prepLinkText.' '.$baseUrl?>"><?= __('https://google.com', 'prep-link')?></a>
                                        <?= __('and enter your search query as:', 'prep-link')?>
                                        <a target="_blank" href="https://www.google.com/search?q=<?=$prepLinkText.' '.$baseUrl?>"><?= __('keyword +', 'prep-link') . ' '. $baseUrl?></a>
                                    </p>
                                </div>

                                <?php if ( isset($advertising['preplink_advertising_4']) && (int)$advertising['preplink_advertising_4'] == 1 && !empty($advertising['preplink_advertising_code_4'])): ?>
                                    <div class="preplink-ads preplink-ads-4">
                                        <?= $advertising['preplink_advertising_code_4'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($endpointSetting['preplink_related_post']) && $endpointSetting['preplink_related_post'] == 1): ?>
                                    <div class="related_post">
                                        <h3 class="suggestions-post"><?= __('Related Posts','prep-link') ?></h3>
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

                                <?php if (isset($advertising['preplink_advertising_7']) && (int)$advertising['preplink_advertising_7'] == 1 && !empty($advertising['preplink_advertising_code_7'])): ?>
                                    <div class="preplink-ads preplink-ads-7">
                                        <?= $advertising['preplink_advertising_code_7'] ?>
                                    </div>
                                <?php endif; ?>

                                <?php
                                if (file_exists(get_template_directory() . '/comments.php') && (int)$endpointSetting['preplink_comment'] == 1) {
                                    ?>
                                    <div class="comment"><?php comments_template(); ?></div><?php
                                }
                                ?>
                                <?php if ( isset($advertising['preplink_advertising_8']) && (int)$advertising['preplink_advertising_8'] == 1 && !empty($advertising['preplink_advertising_8'])): ?>
                                    <div class="preplink-ads preplink-ads-8">
                                        <?= $advertising['preplink_advertising_code_8'] ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>
