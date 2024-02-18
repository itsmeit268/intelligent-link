<?php

function get_list_link($post_id, $settings) {
    $list_link = get_post_meta($post_id, 'link-download-metabox', true);

    $total = (int) $settings['preplink_number_field_lists']? : 5;
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
                        <a href="javascript:void(0)" data-request="<?= esc_html(base64_encode($list_link[$link_is_login_key]))?>" class="btn blue-style list-preplink-btn-link"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php else: ?>
                        <a href="javascript:void(0)" data-request="<?= esc_html(base64_encode($list_link[$link_no_login_key]))?>" class="btn blue-style list-preplink-btn-link"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php endif;?>
                <?php }
            } ?>
        </div>
    <?php }
}

function link_render($isMeta, $link_is_login, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id, $settings) {
    if (is_user_logged_in()): ?>
        <a href="javascript:void(0)" data-request="<?php echo $isMeta ? esc_html(base64_encode($link_is_login)) : esc_html($prepLinkURL); ?>" class="btn blue-style preplink-btn-link" >
            <?php echo $isMeta ? ($file_name.' '.$file_size) : $prepLinkText; ?>
        </a>
        <?php if ($isMeta) get_list_link($post_id, $settings); ?>
    <?php else: ?>
        <a href="javascript:void(0)" data-request="<?php echo $isMeta ? esc_html(base64_encode($link_no_login)) : esc_html($prepLinkURL); ?>" class="btn blue-style preplink-btn-link" >
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
                            <?php
                            $app_image = get_post_meta($post->ID, 'app-image-metabox', true);

                            if ($app_image && function_exists('savvymobi_get_app_image')) {
                                echo savvymobi_get_app_image($post->ID, 116, 116);
                            } else {
                                if (has_post_thumbnail()) {
                                    echo get_the_post_thumbnail($post, 'thumbnail');
                                }
                            }
                            ?>
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