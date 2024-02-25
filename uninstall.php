<?php

/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Xóa option khi gỡ bỏ plugin
$settings = get_option( 'preplink_setting', array() );
if ( !empty( $settings['preplink_delete_option'] ) && $settings['preplink_delete_option'] === '1') {
    delete_option( 'preplink_faq' );
    delete_option( 'meta_attr' );
    delete_option( 'preplink_advertising' );
    delete_option( 'preplink_setting' );
    delete_option( 'preplink_endpoint' );
}