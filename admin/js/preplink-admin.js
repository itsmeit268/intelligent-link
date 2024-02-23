(function ($) {
    'use strict';

    $(function () {
        var $waitText = $('#countdown-select');
        var countdown_mode = $('.countdown-select');
        var $faq_enabled = $('#faq_enabled');
        var $faq_description = $('.faq_description,.faq_title');
        var $related = $('#preplink_related_enabled');
        var related_des = $('.preplink_related_number');
        var $relatedNum = $('#related_number');
        var $replace_text = $('#replace_text');
        var replace_mode = $('.replace_text');
        var __ = wp.i18n.__;

        function _countdown_mode() {
            if ($waitText.val() === 'wait_time') {
                countdown_mode.show();
            } else {
                countdown_mode.hide();
            }

            $waitText.on('change', function () {
                if (this.value === 'wait_time') {
                    countdown_mode.show();
                } else {
                    countdown_mode.hide();
                }
            });
        }

        function _replace_text_mode() {
            if ($replace_text.val() === 'yes') {
                replace_mode.show();
            } else {
                replace_mode.hide();
            }

            $replace_text.on('change', function () {
                if (this.value === 'yes') {
                    replace_mode.show();
                } else {
                    replace_mode.hide();
                }
            });
        }

        function _faq1_enabled() {
            if ($faq_enabled.val() === '1') {
                $faq_description.show();
            } else {
                $faq_description.hide();
            }
            $faq_enabled.on('change', function () {
                if (this.value === '1') {
                    $faq_description.show();
                } else {
                    $faq_description.hide();
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

        var label = $('label[for="preplink_faq"]');
        if (label.length > 0) {
            label.closest('th').remove();
        }

        _countdown_mode();
        _replace_text_mode();
        _faq1_enabled();
        _related_enabled();
        _checkCookieValue();
    });

})(jQuery);
