<?php

function render_back_icon($view_link){ ?>
    <div class="igl-back">
        <a href="<?= esc_url($view_link)?>">
            <i class="c-svg"><svg width="48" height="20"><use xlink:href="#i__back"></use></svg></i>
            <svg aria-hidden="true" style="display:none;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <symbol id="i__back" viewBox="0 0 48 20">
                        <path fill="currentColor" d="M46.27,9.24a1,1,0,0,0-1.41.1A10.71,10.71,0,0,1,36.78,13h-.16a10.78,10.78,0,0,1-8.1-3.66A12.83,12.83,0,0,0,18.85,5C14.94,5,10.73,7.42,6,12.4V5A1,1,0,0,0,4,5V15a1,1,0,0,0,1,1H15a1,1,0,0,0,0-2H7.24c4.42-4.71,8.22-7,11.62-7A10.71,10.71,0,0,1,27,10.66,12.81,12.81,0,0,0,36.61,15h.18a12.7,12.7,0,0,0,9.58-4.35A1,1,0,0,0,46.27,9.24Z"></path>
                    </symbol>
                </defs>
            </svg>
        </a>
    </div>
<?php }
function get_list_link($post_id, $settings) {
    $list_link = get_post_meta($post_id, 'link-download-metabox', true);

    $total = (int) $settings['field_lists']? : 5;
    if (isset($list_link) && !empty($list_link) && is_array($list_link)) { ?>
        <div class="list-link-redirect" >
            <?php for ($i = 1; $i <= $total; $i++) {
                $file_name_key = 'file_name-' . $i;
                $link_no_login_key = 'link_no_login-' . $i;
                $link_is_login_key = 'link_is_login-' . $i;
                $size_key = 'size-' . $i;

                if (isset($list_link[$file_name_key]) && !empty($list_link[$link_no_login_key]) && $list_link[$link_is_login_key]) { ?>
                    <?php
                    $file_name = $list_link[$file_name_key];
                    $size = $list_link[$size_key]; ?>
                    <?php if (is_user_logged_in()) :?>
                        <a href="javascript:void(0)" data-request="<?= esc_html(base64_encode($list_link[$link_is_login_key]))?>" class="preplink-btn-link list-preplink-btn-link"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php else: ?>
                        <a href="javascript:void(0)" data-request="<?= esc_html(base64_encode($list_link[$link_no_login_key]))?>" class="preplink-btn-link list-preplink-btn-link"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php endif;?>
                <?php }
            } ?>
        </div>
    <?php }
}

function link_render($isMeta, $link_is_login, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id, $settings) {
    if (is_user_logged_in()): ?>
        <a href="javascript:void(0)" data-request="<?php echo $isMeta ? esc_html(base64_encode($link_is_login)) : esc_html($prepLinkURL); ?>" class="preplink-btn-link" >
            <?php echo $isMeta ? ($file_name.' '.$file_size) : $prepLinkText; ?>
        </a>
        <?php if ($isMeta) get_list_link($post_id, $settings); ?>
    <?php else: ?>
        <a href="javascript:void(0)" data-request="<?php echo $isMeta ? esc_html(base64_encode($link_no_login)) : esc_html($prepLinkURL); ?>" class="preplink-btn-link" >
            <?php echo $isMeta ? ($file_name.' '.$file_size) : $prepLinkText; ?>
        </a>
        <?php if ($isMeta) get_list_link($post_id, $settings); ?>
    <?php endif;
}

function svg_render() { ?>
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

function ep_related_post($settings, $post_id){ ?>
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
            'post__not_in' => array($post_id),
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
                        <div class="page_file-img">
                            <?= has_post_thumbnail() ? get_the_post_thumbnail($post, 'thumbnail') : ''; ?>
                        </div>
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
<?php }

function faq_render($faq_title, $faq_description) { ?>
    <style>
        .accordion .accordion-item{border-bottom:1px solid #e5e5e5}.accordion .accordion-item button[aria-expanded=true]{border-bottom:1px solid #03b5d2}
        .accordion button{position:relative;display:block;text-align:left;width:100%;padding:8px 0!important;color:#0c1521!important;font-size:16px;font-weight:400;border:none!important;background:0 0!important;outline:0;box-shadow:none!important;line-height:20px!important}
        .accordion button:focus,.accordion button:hover{cursor:pointer;color:#03b5d2}.accordion button:focus::after,.accordion button:hover::after{cursor:pointer;color:#03b5d2;border:1px solid #03b5d2}
        .accordion button .accordion-title{padding:7px 0}.accordion button .icon{display:inline-block;position:absolute;top:8px;right:0;width:20px;height:20px;border:1px solid;border-radius:20px}
        .accordion button .icon::after,.accordion button .icon::before{display:block;position:absolute;content:'';background:currentColor}.accordion button .icon::before{top:8px;left:4px;width:10px;height:2px}
        .accordion button .icon::after{top:5px;left:8px;width:2px;height:8px}.accordion button[aria-expanded=true]{color:#03b5d2}.accordion button[aria-expanded=true] .icon::after{width:0}
        .accordion button[aria-expanded=true]+.accordion-content{opacity:1;max-height:100%;transition:.2s linear;will-change:opacity,max-height}
        .accordion .accordion-content{opacity:0;max-height:0;overflow:hidden;transition:opacity .2s linear,max-height .2s linear;will-change:opacity,max-height}
        .accordion .accordion-content p{font-size:1rem;font-weight:300;margin:10px 0;color:#010807}
    </style>
    <div class="faq-download">
        <h3 class="faq-title"><?= !empty($faq_title) ? $faq_title : '' ?></h3>
        <?= $faq_description; ?>
    </div>
<?php }

function set_no_index_page() {
    if (!function_exists('aioseo' ) && !function_exists('wpseo_init' ) && !function_exists('rank_math' )) {
        $robots = array('noindex' => true, 'nofollow' => true, 'noarchive' => true, 'nosnippet' => true,);
        add_filter('wp_robots', function() use ($robots) {
            return $robots;
        });
    }

    $robots = array(
        'index' => 'noindex', 'follow' => 'nofollow',
        'archive' => 'noarchive', 'snippet' => 'nosnippet',
    );

    if (function_exists('rank_math' )){
        add_filter( 'rank_math/frontend/robots', function() use ($robots) {
            return $robots;
        });
    }

    if (function_exists('wpseo_init' )){
        add_filter( 'wpseo_robots', function() use ($robots) {
            return $robots;
        });
    }

    if (function_exists('aioseo' )){
        add_filter( 'aioseo_robots_meta', function() use ($robots) {
            return $robots;
        });
    }
}