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

function is_plugin_enable(){
    return !empty(ilgl_settings()['preplink_enable_plugin']) && (int)ilgl_settings()['preplink_enable_plugin'] == 1;
}

function modify_conf() {
    $modify_href = [
        'pfix'  => !empty(ilgl_settings()['prefix']) ? ilgl_settings()['prefix']: 'gqbQsbQjv4Wd9NP',
        'mstr'  => !empty(ilgl_settings()['between']) ? base64_encode(ilgl_settings()['between']): base64_encode('aC5mQ1sj9Nvo9AK'),
        'sfix'  => !empty(ilgl_settings()['suffix']) ? base64_encode(ilgl_settings()['suffix']): base64_encode('FTTvYmbQ9Ni1mmVf'),
    ];
    return $modify_href;
}

function modify_href($url_encode) {
    $url_encode = substr($url_encode, 0, 5) . modify_conf()['pfix'] . substr($url_encode, 5);
    $url_encode = substr($url_encode, 0, strlen($url_encode) / 2) . modify_conf()['mstr'] . substr($url_encode, strlen($url_encode) / 2);
    $url_encode = substr($url_encode, 0, -12) . modify_conf()['sfix'] . substr($url_encode, -12);
    return $url_encode;
}

function modify_list_href($url_encode) {
    $url_encode = substr($url_encode, 0, 3) . modify_conf()['mstr'] . substr($url_encode, 3);
    $url_encode = substr($url_encode, 0, strlen($url_encode) / 2) . modify_conf()['pfix'] . substr($url_encode, strlen($url_encode) / 2);
    $url_encode = substr($url_encode, 0, -8) . modify_conf()['sfix'] . substr($url_encode, -8);
    return $url_encode;
}