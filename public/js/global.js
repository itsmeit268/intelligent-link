(function ($) {
    'use strict';
    $(function () {

        var end_point = $.trim(prep_vars.end_point);
        var prep_url = prep_vars.prep_url;
        var current_url = window.location.href.replace(/#.*/, '');

        let isLinkReady = false;
        let isCountdownRunning = false;


        const startCountdown = ($link, full_link, text_link) => {
            let downloadTimer;
            let timeleft = 5;

            isCountdownRunning = true;

            const countdown = () => {
                $link.html(`<strong>[waiting ${timeleft}s...]</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);
                    $link.removeAttr('target');

                    if (current_url.indexOf('?') !== -1) {
                        current_url = current_url.split('?')[0];
                    }

                    if (window.location.href.indexOf(".html") > -1) {
                        if (current_url.includes('/' + end_point)) {
                            current_url = current_url.replace('/' + end_point, '');
                        }
                        $link.attr('href', `${current_url + '/' + end_point}`);
                    } else {
                        $link.attr('href', `${current_url + end_point}`);
                    }

                    $link.text(`${text_link} (Ready!)`);
                    isLinkReady = true;
                    isCountdownRunning = false;

                    document.cookie = "pre_url_go=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    // set cookie
                    document.cookie = "pre_url_go=" + encodeURIComponent(full_link) + "; path=/;";

                } else {
                    setTimeout(countdown, 1000);
                }
            }

            countdown();
        };

        $('a').on('click', function (e) {
            const $this = $(this);
            const btnDownload = $this.closest('.prep-link-download-btn').hasClass('prep-link-download-btn');
            const text_link = $this.text();
            const full_link = $this.attr('href');
            const urls = prep_url.split(",");

            if (btnDownload || full_link === undefined || full_link === null || !full_link.length) {
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
