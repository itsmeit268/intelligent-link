(function ($) {
    'use strict';

    var isLinkReady = false;
    var isCountdownRunning = false;

    $(function () {
        var $urls = $('a');
        var end_point = $.trim(prep_vars.end_point);
        var prep_url = prep_vars.prep_url;
        var current_url = window.location.href.replace(/#.*/, '');
        var count_down = parseInt(prep_vars.count_down);


        const updateLink = ($link, href) => {
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
            document.cookie = "pre_url_go=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "pre_url_go=" + encodeURIComponent(href) + "; path=/;";
        }

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
                    $link.text(`${text_link} (Ready!)`);
                    isLinkReady = true;
                    isCountdownRunning = false;
                    updateLink($link, href);
                } else {
                    setTimeout(countdown, 1000);
                }
            }
            countdown();
        }

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

            if (count_down === 0) {
                e.preventDefault();
                updateLink($this, href);
                window.location.href = $this.attr('href');
                return;
            }

            $.each(prep_urls, function (key, text) {
                if (href.includes($.trim(text))) {
                    if (isLinkReady || (isCountdownRunning && $(this).is($this))) {
                        e.preventDefault();
                        if (isCountdownRunning && !$(this).is($this)) {
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
