/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

(function ($) {
    'use strict';

    $(function () {
        var href = $('a'),
            end_point = $.trim(prep_vars.end_point),
            allow_url = prep_vars.prep_url,
            post_url = window.location.href.replace(/#.*/, ''),
            time_cnf = parseInt(prep_vars.count_down),
            cookie_time = parseInt(prep_vars.cookie_time),
            wait_text = $.trim(prep_vars.wait_text),
            display_mode = prep_vars.display_mode,
            auto_direct = parseInt(prep_vars.auto_direct),
            text_complete = $.trim(prep_vars.text_complete),
            elm_exclude = $.trim(prep_vars.pre_elm_exclude),
            exclude_elm = elm_exclude.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");

        var windowWidth = $(window).width();
        var expirationTime = new Date(Date.now() + cookie_time * 60 * 1000);

        function _setCookie(name, value) {
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
            document.cookie = `${name}=${value}; expires=${expirationTime.toUTCString()}; path=/`;
        }

        function __setCookieTitle(text_link) {
            _setCookie("prep_text_link", text_link);
        }

        function __setCookieURL(href) {
            _setCookie("prep_link_href", href);
        }

        function _endpoint() {
            if (post_url.indexOf('?') !== -1) {
                post_url = post_url.split('?')[0];
            }

            if (post_url.indexOf(".html") > -1 && post_url.includes('.html')) {
                post_url = post_url.match(/.*\.html/)[0] + '/';
            } else if (post_url.includes('/' + end_point + '/')) {
                post_url = post_url.replace('/' + end_point + '/', '');
            } else if (post_url.indexOf('.html') === -1 && !post_url.endsWith('/')) {
                post_url = post_url + '/';
            }
            return post_url + end_point;
        }

        function __startCountdown($link, href, text_link) {
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
                        progress_html += `<span class="text-hide-complete" data-url="${href}" style="display:none">${text_link}</span>`;
                        progress_html += '<span style="vertical-align: unset;">' + '&nbsp;' + text_complete + '</span>';
                        $link.html('<span class="post-progress">' + progress_html + '</span>');
                    } else {
                        if (text_link.length > 35) {
                            text_link_cut = text_link.substring(0, text_link.length - 15) + '...';
                        }
                        var wait_time_html = `<span class="text-hide-complete" data-url="${href}" style="display:none">${text_link}</span>`;
                        wait_time_html += `${text_link_cut}` + '<strong style="color:red;">' + '&nbsp;' + text_complete + '</strong>';
                        $link.html(wait_time_html);
                    }

                    if (auto_direct) {
                        window.location.href = _endpoint();
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

        function _createUrlEncode() {
            href.each(function () {
                var $this = $(this);
                var href = $(this).attr('href');
                var allow_urls = allow_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");
                var found = false;
                var text_link = $this.text();

                if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || href === undefined || href === null || !href.length) {
                    return;
                }

                for (var i = 0; i < allow_urls.length; i++) {
                    if (href.indexOf(allow_urls[i]) !== -1) {
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
                    $this.removeAttr('target');
                    $this.removeAttr('data-id data-type');

                    if (display_mode === 'progress') {
                        $this.wrap('<div class="post-progress-bar"></div>');
                        $this.html('<span class="post-progress">' + text_link + '</span>');
                    } else {
                        $this.wrap('<span class="wrap-countdown"></span>');
                        $this.html('<strong class="link-countdown">' + text_link + '</span>');
                    }
                }
            });
        }

        function _linkClickProcess() {
            href.on('click', function (e) {
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
                    __setCookieURL(href);
                    if (windowWidth > 700) {
                        window.open(_endpoint(), '_blank');
                    } else {
                        window.location.href = _endpoint();
                    }
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

            window.addEventListener('click', function (e) {
                var clickedElement = e.target,
                    anchor = $(clickedElement).closest('a'),
                    progress = $(clickedElement).parents('.post-progress').find('.text-hide-complete'),
                    countdown = $(clickedElement).parents('.wrap-countdown').find('.text-hide-complete');
                if (display_mode === 'progress' && progress.length && anchor.length) {
                    e.preventDefault();
                    __setCookieTitle(progress.text());
                    __setCookieURL(progress.attr('data-url'));
                    if (windowWidth > 700) {
                        window.open(_endpoint(), '_blank');
                    } else {
                        window.location.href = _endpoint();
                    }
                } else if (countdown.length && anchor.length) {
                    e.preventDefault();
                    __setCookieTitle(countdown.text());
                    __setCookieURL(countdown.attr('data-url'));
                    if (windowWidth > 700) {
                        window.open(_endpoint(), '_blank');
                    } else {
                        window.location.href = _endpoint();
                    }
                }
            });
        }

        _createUrlEncode();
        _linkClickProcess();
    });
})(jQuery);
