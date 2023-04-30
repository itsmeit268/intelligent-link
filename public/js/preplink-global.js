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

        function __setCookieTitle(text_link) {
            const expirationTime = new Date(Date.now() + 5 * 60 * 1000); //5 phút phụt 5 phát
            document.cookie = "prep_text_link=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "prep_text_link=" + text_link + "; expires=" + expirationTime.toUTCString() + "; path=/";
        }

        function __updateLink($link, href) {
            if (current_url.indexOf('?') !== -1) {
                current_url = current_url.split('?')[0];
            }

            if (window.location.href.indexOf(".html") > -1 && current_url.includes('.html')) {
                current_url = current_url.match(/.*\.html/)[0];
            } else if(current_url.includes('/'+end_point+'/')) {
                current_url = current_url.replace('/'+end_point+'/','');
            }
            $link.attr('href', `${current_url + '/' + end_point + '?id=' + href}`);
            $link.attr('data-id', `${current_url + '/' + end_point + '?id=' + href}`);
        }

        function __startCountdown($link, href, text_link){
            let downloadTimer;
            let timeleft = time_cnf;
            const $progress = $link.find('.post-progress');
            const progressWidth = $progress.outerWidth() + 10;
            var text_link_cut = text_link;

            const countdown = () => {
                if (display_mode === 'wait_time') {
                    $link.html(`<strong>[${wait_text} ${timeleft}s...]</strong>`);
                } else {
                    $progress.animate({width: progressWidth}, timeleft * 1000, function () {
                        // $link.html('<span class="progress" style="color:red;">'+`${text_link}`+ '..' +'[Ready!]'+'</span>');
                    });
                }

                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);
                    $link.removeAttr('target');
                    if (display_mode === 'progress') {
                        var progress_html = '<i class="fa fa-angle-double-right fa-shake" style="color: #fff;cursor: pointer;font-size: 13px;"></i>';
                        progress_html += '<span class="text-hide-complete" style="display:none">' + `${text_link}` + '</span>';
                        progress_html += '<span style="vertical-align: unset;">'+ '&nbsp;'+text_complete+'</span>';
                        $link.html('<span class="post-progress">' + progress_html + '</span>');
                    } else {
                        if (text_link.length > 35) {
                            text_link_cut = text_link.substring(0, text_link.length - 15) + '...';
                        }
                        var wait_time_html = '<span class="text-hide-complete" style="display:none">' + `${text_link}` + '</span>';
                        wait_time_html += `${text_link_cut}` + '<strong style="color:red;">'+ '&nbsp;' +text_complete+'</strong>';
                        $link.html(wait_time_html);
                    }

                    __updateLink($link, href);

                    if (auto_direct) {
                        window.location.replace($link.attr('href'));
                    }
                } else {
                    setTimeout(countdown, 1000);
                }
            };

            countdown();
        }

        function _isBtoaEncoded(href) {
            try {
                var decodedHref = atob(href);
                return decodedHref.match(/^https?:\/\/.+/) !== null;
            } catch (e) {
                console.log(e.message);
                return false;
            }
        }

        function _createUrlEncode(){
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
                    // $this.attr('data-id', encoded_link);
                    // $this.removeAttr('target');
                    $this.removeAttr('data-id data-type');

                    if (display_mode === 'progress') {
                        $this.wrap('<div class="post-progress-bar"></div>');
                        $this.html('<span class="post-progress">' + text_link + '</span>');
                    } else {
                        $this.wrap('<span class="wrap-countdown"></span>');
                        $this.html('<strong class="link-countdown"">' + text_link + '</span>');
                    }
                }
            });
        }

        function _linkClickProcess(){
            $urls.on('click', function (e) {
                const $this = $(this);
                const text_link = $this.text();
                const href = $this.attr('href');
                if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || href === undefined || href === null || !href.length) {
                    return;
                }
                if (time_cnf === 0) {
                    if (!_isBtoaEncoded(href)) {
                        return;
                    }
                    e.preventDefault();
                    __setCookieTitle(text_link);
                    __updateLink($this, href);
                    window.location.href = $this.attr('href');
                    return;
                }

                if (time_cnf > 0) {
                    if (!_isBtoaEncoded(href)) {
                        return;
                    }

                    $this.off('click').attr('href', 'javascript:void(0);');
                    e.preventDefault();
                    __startCountdown($this, href, text_link);
                }
            });

            window.addEventListener('click', function (e){
                var clickedElement = e.target,
                    anchor = $(clickedElement).closest('a'),
                    progress_text = $(clickedElement).parents('.post-progress').find('.text-hide-complete').text(),
                    countdown_text = $(clickedElement).parents('.wrap-countdown').find('.text-hide-complete').text();

                if (display_mode === 'progress' && progress_text.length && anchor.length) {
                    e.preventDefault();
                    __setCookieTitle(progress_text);
                    window.location.replace(anchor.attr('href'));
                } else if (countdown_text.length && anchor.length){
                    e.preventDefault();
                    __setCookieTitle(countdown_text);
                    window.location.replace(anchor.attr('href'));
                }
            });
        }

        _createUrlEncode();
        _linkClickProcess();
    });
})(jQuery);
