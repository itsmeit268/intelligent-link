<?php

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

function ilgl_settings() {
    return get_option('preplink_setting', []);
}

function ep_settings() {
    return get_option('preplink_endpoint', []);
}

function ilgl_meta_option(){
    return get_option('meta_attr', []);
}

function ilgl_ads_option(){
    return get_option('ads_code', []);
}

function is_plugin_enable(){
    return !empty(ilgl_settings()['preplink_enable_plugin']) && (int)ilgl_settings()['preplink_enable_plugin'] == 1;
}

function endpoint_conf(){
    $endpoint = ENDPOINT;
    if (!empty(ep_settings()['endpoint'])) {
        $endpoint = preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim(ep_settings()['endpoint']));
    }
    return $endpoint;
}

function modify_conf() {
    $modify_href = [
        'pfix'  => !empty(ilgl_settings()['prefix']) ? base64_encode(ilgl_settings()['prefix']): base64_encode('gqbQsbQjv4Wd9NP'),
        'mstr'  => !empty(ilgl_settings()['between']) ? base64_encode(ilgl_settings()['between']): base64_encode('aC5mQ1sj9Nvo9AK'),
        'sfix'  => !empty(ilgl_settings()['suffix']) ? base64_encode(ilgl_settings()['suffix']): base64_encode('FTTvYmbQ9Ni1mmVf'),
    ];
    return $modify_href;
}

function modify_href($url_encode) {
    $url_encode = substr($url_encode, 0, 5) . modify_conf()['pfix'] . substr($url_encode, 5);
    $url_encode = substr($url_encode, 0, strlen($url_encode) / 2) . modify_conf()['mstr'] . substr($url_encode, strlen($url_encode) / 2);
    $url_encode = substr($url_encode, 0, -4) . modify_conf()['sfix'] . substr($url_encode, -4);
    return $url_encode;
}

function modify_list_href($url_encode) {
    $url_encode = substr($url_encode, 0, 3) . modify_conf()['mstr'] . substr($url_encode, 3);
    $url_encode = substr($url_encode, 0, strlen($url_encode) / 2) . modify_conf()['pfix'] . substr($url_encode, strlen($url_encode) / 2);
    $url_encode = substr($url_encode, 0, -8) . modify_conf()['sfix'] . substr($url_encode, -8);
    return $url_encode;
}

//function encrypt($str) {
//    $key = hash('sha256', 'SecretKey'.get_bloginfo('url'));
//    $iv = substr(hash('sha256', 'SecretKeyIV'.get_bloginfo('url')), 0, 16);
//    $output = openssl_encrypt($str, 'AES-256-CBC', $key, 0, $iv);
//    $output = base64_encode($output);
//    return $output;
//}
//
//function decrypt($string) {
//    $key = hash('sha256', 'SecretKey'.get_bloginfo('url'));
//    $iv = substr(hash('sha256', 'SecretKeyIV'.get_bloginfo('url')), 0, 16);
//    $output = openssl_decrypt(base64_decode($string),'AES-256-CBC', $key, 0, $iv);
//    $re = '/(&pxdate=(\d{4}\-\d{2}-\d{2}))$/m';
//    preg_match_all($re, $output, $matches, PREG_SET_ORDER, 0);
//    if( !isset($matches[0][2]) ) {
//        return $string;
//    }
//    if( $matches[0][2] == date('Y-m-d') ) {
//        return preg_replace($re, '', $output);
//    }
//    return $output;
//}