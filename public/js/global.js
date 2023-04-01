/**
 * @link       https://github.com/itsmeit268/preplink
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

(function ($) {
    'use strict';

    $(function () {
        var $urls = $('a');
        var end_point = $.trim(prep_vars.end_point);
        var prep_url = prep_vars.prep_url;
        var current_url = window.location.href.replace(/#.*/, '');
        var time_cnf = parseInt(prep_vars.count_down);
        var wait_text = $.trim(prep_vars.wait_text);
        var display_mode = prep_vars.display_mode;
        var auto_direct = parseInt(prep_vars.auto_direct);
        var text_complete = $.trim(prep_vars.text_complete);
        var pre_elm_exclude = $.trim(prep_vars.pre_elm_exclude);
        var exclude_elm = pre_elm_exclude.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");

        function updateLink($link, href) {
            if (current_url.indexOf('?') !== -1) {
                current_url = current_url.split('?')[0];
            }

            if (window.location.href.indexOf(".html") > -1 && current_url.includes('.html')) {
                current_url = current_url.match(/.*\.html/)[0];
            } else if(current_url.includes('/download/')) {
                current_url = current_url.replace('/download/','');
            }
            $link.attr('href', `${current_url + '/' + end_point + '?id=' + href}`);
            $link.attr('data-id', `${current_url + '/' + end_point + '?id=' + href}`);
        }

        function startCountdown($link, href, text_link){
            let downloadTimer;
            let timeleft = time_cnf;
            const $progress = $link.find('.post-progress');
            const progressWidth = $progress.outerWidth() + 10;

            if (text_link.length > 35) {
                text_link = text_link.substring(0, text_link.length - 15) + '...';
            }

            const countdown = () => {
                if (display_mode === 'wait_time') {
                    $link.html(`<strong>[${wait_text} ${timeleft}s...]</strong>`);
                } else {
                    $progress.animate({width: progressWidth}, (timeleft * 1000) / 2, function () {
                        // $link.html('<span class="progress" style="color:red;">'+`${text_link}`+ '..' +'[Ready!]'+'</span>');
                    });
                }

                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);
                    $link.removeAttr('target');
                    if (display_mode === 'progress') {
                        $link.html('<span class="post-progress">' + `${text_link}` + '<strong class="text-complete">' + ' ' + text_complete + '</strong>' + '</span>');
                    } else {
                        $link.html(`${text_link}` + '<strong style="color:red;"> [Link ready!]</strong>');
                    }
                    updateLink($link, href);

                    if (auto_direct)
                        window.location.replace($link.attr('href'));
                } else {
                    setTimeout(countdown, 1000);
                }
            };

            countdown();
        }

        $urls.each(function () {
            var $this = $(this);
            var href = $(this).attr('href');
            var prep_urls = prep_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");
            var found = false;
            var text_link = $this.text();

            if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || href === undefined || href === null || !href.length) {
                return;
            }

            for (var i = 0; i < prep_urls.length; i++) {
                if (href.indexOf(prep_urls[i]) !== -1) {
                    found = true;
                    break;
                }
            }

            if (found) {
                // Kiểm tra URL được mã hóa bằng encodeURIComponent() hay không
                if (href === encodeURIComponent(decodeURIComponent(href))) {
                    href = decodeURIComponent(href);
                }
                var encoded_link = btoa(href);
                $this.attr('href', encoded_link);
                $this.attr('data-id', encoded_link);
                $this.removeAttr('target');

                if (display_mode === 'progress') {
                    $this.wrap('<div class="post-progress-bar"></div>');
                    $this.html('<span class="post-progress">' + text_link + '</span>');
                }
            }
        });

        $urls.on('click', function (e) {
            const $this = $(this);
            const text_link = $this.text();
            const href = $this.attr('href');

            if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || href === undefined || href === null || !href.length) {
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
