/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @since      1.0.0
 * @link       https://github.com/itsmeit268/preplink
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

(function ($) {
    'use strict';
    $(function () {

        var end_point = prep_vars.end_point;
        var prep_url = prep_vars.prep_url;
        var current_url = window.location.href.replace(/#.*/, '');

        let isLinkReady = false;
        let isCountdownRunning = false;

        const startCountdown = ($link, full_link, text_link) => {
            let downloadTimer;
            let timeleft = 5;

            isCountdownRunning = true;

            downloadTimer = setInterval(() => {
                $link.html(`<strong>[waiting ${timeleft}s...]</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);
                    $link.removeAttr('target');
                    if (window.location.href.indexOf(".html") > -1) {
                        $link.attr('href', `${current_url + '/' + $.trim(end_point)}`);
                    } else {
                        $link.attr('href', `${current_url + $.trim(end_point)}`);
                    }

                    $link.text(`${text_link} (Ready!)`);
                    isLinkReady = true;
                    isCountdownRunning = false;

                    document.cookie = "pre_url_go=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    // set cookie
                    document.cookie = "pre_url_go=" + encodeURIComponent(full_link) + "; path=/;";

                }
            }, 1000);
        };

        $('a').on('click', function (e) {
            const $this = $(this);
            if ($this.closest('.prep-link-download-btn').hasClass('prep-link-download-btn')) {
                return;
            }

            const text_link = $this.text();
            const full_link = $this.attr('href');
            const urls = prep_url.split(",");

            if (typeof full_link === 'undefined' || !full_link.length) {
                return;
            }

            let isPrevented = false;

            $.each(urls, function (key, text) {
                if (full_link.includes($.trim(text))) {
                    if (isLinkReady || isCountdownRunning) {
                        e.preventDefault();
                        if (isCountdownRunning) {
                            alert("Please wait for the countdown to finish before clicking again.");
                        } else {
                            window.location.href = $this.attr('href');
                        }
                        isPrevented = true;
                    } else {
                        e.preventDefault();
                        startCountdown($this, full_link, text_link);
                        isPrevented = true;
                    }
                    return false;
                }
            });

            if (!isPrevented && isLinkReady) {
                isLinkReady = false;
            }
        });
    });
})(jQuery);
