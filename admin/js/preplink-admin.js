(function ($) {
    'use strict';

    $(function () {
        var $waitText = $('#preplink_wait_text');
        var text_replace = $('.wait_text_replace');
        var $faq1 = $('#preplink_faq1_enabled');
        var faq1_des = $('.preplink_faq1_description,.preplink_faq1_title');

        var $faq2 = $('#preplink_faq2_enabled');
        var faq2_des = $('.preplink_faq2_description,.preplink_faq2_title');

        var $related = $('#preplink_related_enabled');
        var related_des = $('.preplink_related_description,.preplink_related_number');


        function _display_mode(){
            if ($waitText.val() === 'wait_time') {
                text_replace.show();
            } else {
                text_replace.hide();
            }

            $waitText.on('change', function() {
                if (this.value === 'wait_time') {
                    text_replace.show();
                } else {
                    text_replace.hide();
                }
            });
        }

        function _faq1_enabled(){
            if ($faq1.val() === '1') {
                faq1_des.show();
            } else {
                faq1_des.hide();
            }
            $faq1.on('change', function() {
                if (this.value === '1') {
                    faq1_des.show();
                } else {
                    faq1_des.hide();
                }
            });
        }

        function _faq2_enabled(){
            if ($faq2.val() === '1') {
                faq2_des.show();
            } else {
                faq2_des.hide();
            }
            $faq2.on('change', function() {
                if (this.value === '1') {
                    faq2_des.show();
                } else {
                    faq2_des.hide();
                }
            });
        }

        function _related_enabled(){
            if ($related.val() === '1') {
                related_des.show();
            } else {
                related_des.hide();
            }
            $related.on('change', function() {
                if (this.value === '1') {
                    related_des.show();
                } else {
                    related_des.hide();
                }
            });
        }

        _display_mode();
        _faq1_enabled();
        _faq2_enabled();
        _related_enabled();
    });

})(jQuery);
