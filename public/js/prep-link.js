/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://github.com/itsmeit268/preplink
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

(function ($) {
    'use strict';
    $(function () {
        var $hiddenLink = $('.link-session-expired');
        //Nếu cookie link download không tồn tại thì chuyển hướng về trang ban đầu
        if ($hiddenLink.length) {
            setTimeout(function () {
                window.location.replace($hiddenLink.attr('href'));
            }, 5000);
        }

        //FAQ COPPY FROM RANKMATH PLUGIN
        if ($('.faq-download').length) {
            $('.rank-math-question').click(function(event) {
                if (!$(this).parent().find('.rank-math-answer ').is(":visible")) {
                    $('.rank-math-question').removeClass('faq-active');
                    $(this).addClass('faq-active');
                    $('.rank-math-answer ').hide();
                } else {
                    $(this).removeClass('faq-active');
                }
                $(this).parent().find('.rank-math-answer ').toggle(300);
            });
        }

        $('.clickable').on('click', function (e){
            e.preventDefault();
            var href = $(this).attr('href');
            window.location.href = decodeURIComponent(href);
        });
    });
})(jQuery);
