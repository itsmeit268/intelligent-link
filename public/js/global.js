(function ($) {
    'use strict';

    var isLinkReady = false;
    var isCountdownRunning = false;

    $(function () {
        var $urls = $('a');
        var end_point = $.trim(prep_vars.end_point);
        var prep_url = prep_vars.prep_url;
        var current_url = window.location.href.replace(/#.*/, '');
        var time_cnf = parseInt(prep_vars.count_down);

        const updateLink = ($link, href) => {
            if (current_url.indexOf('?') !== -1) {
                current_url = current_url.split('?')[0];
            }
            if (window.location.href.indexOf(".html") > -1) {
                if (current_url.includes('/' + end_point)) {
                    current_url = current_url.replace('/' + end_point, '');
                }
                $link.attr('href', `${current_url + '/' + end_point + '?id=' + href}`);
                $link.attr('data-id', `${current_url + '/' + end_point + '?id=' + href}`);
            } else {
                $link.attr('href', `${current_url + end_point + '?id=' + href}`);
                $link.attr('data-id', `${current_url + end_point + '?id=' + href }`);
            }
        }

        const startCountdown = ($link, href, text_link) => {
            var isLinkReady = false;
            var isCountdownRunning = false;

            let downloadTimer;
            let timeleft = time_cnf;
            isCountdownRunning = true;

            const countdown = () => {
                $link.html(`<strong>[ waiting ${timeleft}s... ]</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);
                    $link.removeAttr('target');
                    $link.html(`${text_link}` + '<strong style="color:red;">(Ready!)</strong>');
                    isLinkReady = true;
                    isCountdownRunning = false;
                    updateLink($link, href);
                } else {
                    setTimeout(countdown, 1000);
                }
            }
            countdown();
        }

        $urls.each(function() {
            var href = $(this).attr('href');
            var prep_urls = prep_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");
            var found = false;

            const btnDownload = $(this).hasClass('prep-link-download-btn');
            if (btnDownload || href === undefined || href === null || !href.length) {
                return;
            }

            for (var i = 0; i < prep_urls.length; i++) {
                if (href.indexOf(prep_urls[i]) !== -1) {
                    found = true;
                    break;
                }
            }

            if (found) {
                var encoded_link = btoa(href);
                $(this).attr('href', encoded_link);
                $(this).attr('data-id', encoded_link);
            }
        });

        $urls.on('click', function (e) {
            const $this = $(this);
            const btnDownload = $this.closest('.prep-link-download-btn').hasClass('prep-link-download-btn');
            const text_link = $this.text();
            const href = $this.attr('href');

            if (btnDownload || href === undefined || href === null || !href.length) {
                return;
            }

            let isPrevented = false;

            if (time_cnf === 0) {
                e.preventDefault();
                updateLink($this, href);
                window.location.href = $this.attr('href');
                return;
            }

            var isBtoaEncoded = false;

            if (time_cnf > 0) {

                try {
                    var decodedHref = atob(href);
                    isBtoaEncoded = decodedHref.match(/^https?:\/\/.+/) !== null;
                } catch (e) {
                    console.log(e.message);
                }

                if (!isBtoaEncoded) {
                    return;
                }

                $this.off('click').attr('href', 'javascript:void(0);');
                startCountdown($this, href, text_link);
            }

            if (!isPrevented && isLinkReady) {
                isLinkReady = false;
            }
        });

    });
})(jQuery);
