(function ($) {
    'use strict';

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
                $link.attr('data-id', `${current_url + end_point + '?id=' + href}`);
            }
        }

        const startCountdown = ($link, href, text_link) => {
            let downloadTimer;
            let timeleft = time_cnf;
            const $progress = $link.find('.progress');
            const progressWidth = $progress.outerWidth() + 10;

            if (text_link.length > 35) {
                text_link = text_link.substring(0, text_link.length - 15) + '...';
            }

            const countdown = () => {

                // $progress.animate({width: progressWidth}, (timeleft * 1000) / 2, function () {
                //     // $link.html('<span class="progress" style="color:red;">'+`${text_link}`+ '..' +'[Ready!]'+'</span>');
                // });
                $link.html(`<strong>[ waiting ${timeleft}s... ]</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);
                    $link.removeAttr('target');
                    $link.html('<span class="progress" style="color:red;">' + `${text_link}` + '[Link ready!]' + '</span>');
                    updateLink($link, href);
                } else {
                    setTimeout(countdown, 1000);
                }
            };

            countdown();
        };


        $urls.each(function () {
            var href = $(this).attr('href');
            var prep_urls = prep_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");
            var found = false;
            var text_link = $(this).text();
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
                // $(this).addClass('progress');
                $(this).html('<span class="progress">' + text_link + '</span>')
                $(this).removeAttr('target');
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
        });
    });
})(jQuery);
