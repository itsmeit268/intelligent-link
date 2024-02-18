(function ($) {
    'use strict';

    $(function () {
        var $waitText = $('#preplink_wait_text');
        var text_replace = $('.wait_text_replace');
        var $faq1 = $('#faq_enabled');
        var faq1_des = $('.faq_description,.faq_title');
        var $related = $('#preplink_related_enabled');
        var related_des = $('.preplink_related_number');
        var $endpointAutoDirect = $('#endpoint_auto_direct');
        var endpoint_auto_direct_num = $('.preplink_endpoint_number');
        var $post_countdown = $('#preplink_countdown');
        var $endpoint_countdown = $('#countdown_endpoint');
        var $relatedNum = $('#related_number');
        var __ = wp.i18n.__;

        function _display_mode() {
            if ($waitText.val() === 'wait_time') {
                text_replace.show();
            } else {
                text_replace.hide();
            }

            $waitText.on('change', function () {
                if (this.value === 'wait_time') {
                    text_replace.show();
                } else {
                    text_replace.hide();
                }
            });
        }

        function _faq1_enabled() {
            if ($faq1.val() === '1') {
                faq1_des.show();
            } else {
                faq1_des.hide();
            }
            $faq1.on('change', function () {
                if (this.value === '1') {
                    faq1_des.show();
                } else {
                    faq1_des.hide();
                }
            });
        }
        function _related_enabled() {
            if ($related.val() === '1') {
                related_des.show();
            } else {
                related_des.hide();
            }
            $related.on('change', function () {
                if (this.value === '1') {
                    related_des.show();
                } else {
                    related_des.hide();
                }
            });

            $relatedNum.on('change', function () {
                if (parseInt($relatedNum.val()) < 1) {
                    $('.prep-notice').remove();
                    $relatedNum.parents('.related_number').append('<p class="prep-notice">' + __('The value must be greater than 0 to show the number of related posts.', 'prep-link') + '</p>');
                } else {
                    $('.prep-notice').remove();
                }
            });
        }

        function _checkPostAutoDirect() {
            $post_countdown.on('change', function () {
                if (parseInt($post_countdown.val()) <= 0) {
                    $('.prep-notice').remove();
                    $post_countdown.parents('.preplink_post_number_notice').append('<p class="prep-notice">'+ __('The value must be greater than 0 when you run the countdown. Or you can choose (Yes) for Auto direct','prep-link') +'</p>')
                } else {
                    $('.prep-notice').remove();
                }
            });
        }

        function _checkEndpointAutoDirect() {
            if ($endpointAutoDirect.val() === '0') {
                endpoint_auto_direct_num.show();
            } else {
                endpoint_auto_direct_num.hide();
            }
            $endpointAutoDirect.on('change', function () {
                if (this.value === '0') {
                    endpoint_auto_direct_num.show();
                } else {
                    endpoint_auto_direct_num.hide();
                }
            });

            $endpoint_countdown.on('change', function () {
                if (parseInt($endpoint_countdown.val()) <= 0) {
                    $('.prep-notice').remove();
                    $endpoint_countdown.parents('.preplink_endpoint_number_notice').append('<p class="prep-notice">'+ __('The value must be greater than 0 when you run the countdown. Or you can choose (Yes) for Auto direct', 'prep-link') +'</p>')
                } else {
                    $('.prep-notice').remove();
                }
            });
        }

        function _checkCookieValue() {
            var cookie = $('#cookie_time');
            cookie.on('change', function () {
                if (parseInt(cookie.val()) <= 4) {
                    $('.prep-notice').remove();
                    cookie.parents('td').append('<p class="prep-notice">'+ __('Value must be greater than 5','prep-link')+'</p>');
                } else {
                    $('.prep-notice').remove();
                }
            });
        }

        $('#submit').on('click', function () {
            var errors = $('.prep-notice');
            if (errors.length) {
                $('html, body').animate({ scrollTop: errors.offset().top }, 100);
                return false;
            }
        });

        _display_mode();
        _faq1_enabled();
        _related_enabled();
        _checkCookieValue();
        _checkEndpointAutoDirect();
        _checkPostAutoDirect();
    });

})(jQuery);
