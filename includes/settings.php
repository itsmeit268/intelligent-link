<?php

defined('ABSPATH') || exit;

class ILGL_Settings {
    private static $instance = null;

    const CIPHER = 'aes-256-cbc';

    private function __construct() {}

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function global_settings() {
        return get_option('preplink_setting', []);
    }

    public function ep_settings() {
        return get_option('preplink_endpoint', []);
    }

    public function meta_option() {
        return get_option('meta_attr', []);
    }

    public function ads_settings() {
        return get_option('ads_code', []);
    }

    public function faq_settings() {
        return get_option('preplink_faq', []);
    }

    public function allow_domain() {
        $settings = $this->global_settings();
        $prepList = $settings['preplink_url'] ?? '';

        if (!empty($prepList)) {
            $prepArr = array_filter(
                array_map('trim', explode(',', $prepList)),
                function($value) {
                    return $value !== '';
                }
            );
            return implode(',', $prepArr);
        }

        return '';
    }

    public function exclude_elm() {
        $settings = $this->global_settings();
        $excludeList = $settings['preplink_excludes_element'] ?? '';

        $defaultExcludes = [
            '.prep-link-download-btn',
            '.prep-link-btn',
            '.session-expired'
        ];

        if (!empty($excludeList)) {
            $excludesArr = array_filter(
                array_map('trim', explode(',', $excludeList)),
                function($value) {
                    return $value !== '';
                }
            );

            $merged = array_flip($excludesArr);
            foreach ($defaultExcludes as $default) {
                $merged[$default] = true;
            }

            return implode(',', array_keys($merged));
        }

        return implode(',', $defaultExcludes);
    }

    public function is_plugin_enable() {
        $settings = $this->global_settings();
        return !empty($settings['preplink_enable_plugin'])
            && (int)$settings['preplink_enable_plugin'] === 1;
    }

    private function encrypt_key() {
        $settings = $this->global_settings();
        $key = $settings['key'] ?? '12345678901234567890123456789012';

        $keyLen = strlen($key);
        if ($keyLen !== 32) {
            return str_pad(substr($key, 0, 32), 32, '0');
        }

        return $key;
    }

    private function encrypt_iv() {
        $settings = $this->global_settings();
        $iv = $settings['iv'] ?? '1234567890123456';

        $ivLen = strlen($iv);
        if ($ivLen !== 16) {
            return str_pad(substr($iv, 0, 16), 16, '0');
        }

        return $iv;
    }

    public function encrypt_url($url) {
        if (empty($url) || !is_string($url)) {
            return '';
        }

        $encrypted = openssl_encrypt( $url, self::CIPHER, $this->encrypt_key(), OPENSSL_RAW_DATA, $this->encrypt_iv() );
        if ($encrypted === false) {
            return '';
        }

        return bin2hex($encrypted);
    }

    public function decrypt_url($hex) {
        if (empty($hex) || !is_string($hex) || !ctype_xdigit($hex)) {
            return false;
        }

        $encrypted = hex2bin($hex);
        if ($encrypted === false) {
            return false;
        }

        $decrypted = openssl_decrypt( $encrypted, self::CIPHER, $this->encrypt_key(), OPENSSL_RAW_DATA, $this->encrypt_iv() );

        if ($decrypted === false) {
            return false;
        }

        return $decrypted;
    }
}

ILGL_Settings::get_instance();

function ilgl_settings() {
    return ILGL_Settings::get_instance();
}