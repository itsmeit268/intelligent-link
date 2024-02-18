<?php

function get_list_link_download($post_id, $settings) {
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
                    <?php if (vip_level() || premium_level()) :?>
                        <a href="javascript:void(0)" data-request="<?= esc_html(base64_encode($list_link[$link_is_login_key]))?>" class="btn blue-style list-preplink-btn-link"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php else: ?>
                        <a href="javascript:void(0)" data-request="<?= esc_html(base64_encode($list_link[$link_no_login_key]))?>" class="btn blue-style list-preplink-btn-link"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php endif;?>
                <?php }
            } ?>
        </div>
    <?php }
}