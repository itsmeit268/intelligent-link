/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

(function ($) {
    'use strict';

    $(function () {
        var href = $('a'),
            end_point = $.trim(href_proccess.end_point),
            post_url = window.location.href.replace(/#.*/, ''),
            time_cnf = parseInt(href_proccess.count_down),
            cookie_time = parseInt(href_proccess.cookie_time),
            wait_text = $.trim(href_proccess.wait_text),
            display_mode = href_proccess.display_mode,
            auto_direct = parseInt(href_proccess.auto_direct),
            text_complete = $.trim(href_proccess.text_complete),
            elm_exclude = $.trim(href_proccess.pre_elm_exclude),
            exclude_elm = elm_exclude.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(","),
            allow_url = href_proccess.prep_url,
            links_noindex_fl = href_proccess.links_noindex_nofollow,
            windowWidth = $(window).width();

        function _setCookie(name, value) {
            var expirationTime = new Date(Date.now() + cookie_time * 60 * 1000);
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

        function contains_value(value, array) {
            for (var i = 0; i < array.length; i++) {
                if (value.indexOf(array[i]) !== -1) {
                    return true;
                }
            }
            return false;
        }

        function prepHrefLink() {
            href.each(function () {
                var $this = $(this),
                    href = $(this).attr('href'),
                    allow_urls = allow_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(","),
                    text_link = $this.text(),
                    links_noindex_nofollow = links_noindex_fl.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");

                if (links_noindex_fl !== "" && contains_value(href, links_noindex_nofollow)) {
                    $this.attr('rel', 'nofollow noopener noreferrer');
                }

                if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || href === undefined || href === null || !href.length) {
                    return;
                }

                if (allow_url !== "" && contains_value(href, allow_urls)) {
                    if (href === encodeURIComponent(decodeURIComponent(href))) {
                        href = decodeURIComponent(href);
                    }
                    var encoded_link = btoa(href);
                    $this.attr('href', encoded_link);

                    $this.removeAttr('target');
                    $this.removeAttr('data-id data-type');

                    if (display_mode === 'progress') {
                        $this.wrap('<div class="post-progress-bar"></div>');
                        $this.html('<span class="post-progress">' + text_link + '</span>');
                    } else {
                        $this.wrap('<span class="wrap-countdown"></span>');
                        $this.html('<strong class="link-countdown">' + text_link + '</span>');
                    }

                    var strongElement = $(".post-progress-bar,.wrap-countdown").next("strong:contains('|')");
                    if ($(window).width() < 700 && strongElement.length) {
                        strongElement.remove();
                    }
                }

            });
        }

        function _start_countdown($link, href, text_link) {
            let downloadTimer;
            let timeleft = time_cnf;
            const countdown = () => {
                $link.html(`<strong> ${wait_text} ${timeleft}s...</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);
                    let wait_time_html = `<span class="text-hide-complete" data-url="${href}" data-title="${text_link}"></span>`;
                    wait_time_html += '<strong style="vertical-align: unset;">' + '&nbsp;' + text_complete + '</strong>';
                    $link.html(wait_time_html);

                    if (auto_direct) {
                        __setCookieTitle(text_link);
                        __setCookieURL(href);
                        window.location.href = _endpoint();
                    }
                } else {
                    setTimeout(countdown, 1000);
                }
            };
            countdown();
        }

        function _start_progress($link, href, text_link) {
            const $progress = $link.find('.post-progress');
            const progressWidth = $progress.width();
            const parent = $link.parent('.post-progress-bar');

            let currentWidth = 0;
            let timeleft = time_cnf;

            parent.css('width', parent.width());
            $progress.width("0%");
            $progress.css("background-color", "#1479B3");

            const intervalId = setInterval(function () {
                currentWidth += progressWidth / (timeleft * 1000 / timeleft);
                $progress.width(currentWidth);

                if (currentWidth >= progressWidth) {
                    clearInterval(intervalId);
                    let progress_html = '<svg xmlns="http://www.w3.org/2000/svg" height="0.75em" viewBox="0 0 512 512"><style>svg{fill:#ffffff}</style><path d="M470.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 256 265.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L210.7 256 73.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/></svg>';
                    progress_html += `<span class="text-hide-complete" data-url="${href}" data-title="${text_link}"></span>`;
                    progress_html += '<span class="text-complete">' + '&nbsp;' + text_complete + '</span>';
                    $link.html('<span class="post-progress" style="background-color:#0c7c3f">' + progress_html + '</span>');
                    parent.removeAttr('style');
                    if (auto_direct) {
                        __setCookieTitle(text_link);
                        __setCookieURL(href);
                        window.location.href = _endpoint();
                    }
                }
            }, timeleft);
        }

        function _isBtoaEncoded(href) {
            try {
                const decodedHref = atob(href);
                return decodedHref.match(/^https?:\/\/.+/) !== null;
            } catch (e) {
                console.log(e.message);
                return false;
            }
        }

        function processClick() {
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

                    if (display_mode === 'wait_time') {
                        _start_countdown($this, href, text_link);
                    } else {
                        _start_progress($this, href, text_link);
                    }
                }
            });

            window.addEventListener('click', function (e) {
                let clickedElement = e.target,
                    anchor = $(clickedElement).closest('a'),
                    progress = $(clickedElement).parents('.post-progress').find('.text-hide-complete'),
                    countdown = $(clickedElement).parents('.wrap-countdown').find('.text-hide-complete');
                if (display_mode === 'progress' && progress.length && anchor.length) {
                    e.preventDefault();
                    __setCookieTitle(progress.attr('data-title'));
                    __setCookieURL(progress.attr('data-url'));
                    if (windowWidth > 700) {
                        window.open(_endpoint(), '_blank');
                    } else {
                        window.location.href = _endpoint();
                    }
                } else if (countdown.length && anchor.length) {
                    e.preventDefault();
                    __setCookieTitle(countdown.attr('data-title'));
                    __setCookieURL(countdown.attr('data-url'));
                    if (windowWidth > 700) {
                        window.open(_endpoint(), '_blank');
                    } else {
                        window.location.href = _endpoint();
                    }
                }
            });
        }

        prepHrefLink();
        processClick();
    });
})(jQuery);
