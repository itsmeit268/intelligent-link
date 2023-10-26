<?php

/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Xóa option khi gỡ bỏ plugin
$settings = get_option( 'preplink_setting', array() );
if ( isset( $settings['preplink_delete_option'] ) && $settings['preplink_delete_option'] ) {
    delete_option( 'preplink_advertising' );
    delete_option( 'preplink_setting' );
    delete_option( 'preplink_endpoint' );
    delete_option( 'preplink_faq' );
}