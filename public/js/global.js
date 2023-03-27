(function ($) {
    'use strict';
    $(function () {
        var $urls = $('a');
        var end_point = $.trim(prep_vars.end_point);
        var prep_url = prep_vars.prep_url;
        var current_url = window.location.href.replace(/#.*/, '');
        var count_down   = $.trim(prep_vars.count_down);

        let isLinkReady = false;
        let isCountdownRunning = false;


        const startCountdown = ($link, href, text_link) => {
            let downloadTimer;
            let timeleft = count_down;

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
                        $link.attr('data-id', `${current_url + '/' + end_point}`);
                    } else {
                        $link.attr('href', `${current_url + end_point}`);
                        $link.attr('data-id', `${current_url + end_point}`);
                    }

                    $link.text(`${text_link} (Ready!)`);
                    isLinkReady = true;
                    isCountdownRunning = false;

                    document.cookie = "pre_url_go=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    // set cookie
                    document.cookie = "pre_url_go=" + encodeURIComponent(href) + "; path=/;";

                } else {
                    setTimeout(countdown, 1000);
                }
            }

            countdown();
        };

        // $urls.each(function() {
        //     var href = $(this).attr('href');
        //     var prep_urls = prep_url.split(',');
        //     var found = false;
        //
        //     const btnDownload = $('.prep-link-download-btn').hasClass('prep-link-download-btn');
        //     if (btnDownload || href === undefined || href === null || !href.length) {
        //         return;
        //     }
        //
        //     for (var i = 0; i < prep_urls.length; i++) {
        //         if (href.indexOf(prep_urls[i]) !== -1) {
        //             found = true;
        //             break;
        //         }
        //     }
        //
        //     if (found) {
        //         var encoded_link = encodeURIComponent(href);
        //         $(this).attr('href', encoded_link);
        //         $(this).attr('data-id', encoded_link);
        //     }
        // });

        $urls.on('click', function (e) {
            const $this = $(this);
            const btnDownload = $this.closest('.prep-link-download-btn').hasClass('prep-link-download-btn');
            const text_link = $this.text();
            const href = $this.attr('href');
            const prep_urls = prep_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");

            if (btnDownload || href === undefined || href === null || !href.length) {
                return;
            }

            let isPrevented = false;

            $.each(prep_urls, function (key, text) {
                if (href.includes($.trim(text)) || href.includes(text.trim())) {
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
                        startCountdown($this, href, text_link);
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
