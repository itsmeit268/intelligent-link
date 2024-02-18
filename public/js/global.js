/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

(function ($) {
    'use strict';

    $(function () {
        var end_point = href_proccess.end_point.trim(),
            post_url = window.location.href.replace(/#.*/, ''),
            time_cnf = parseInt(href_proccess.count_down),
            cookie_time = parseInt(href_proccess.cookie_time),
            wait_text = href_proccess.wait_text.trim(),
            display_mode = href_proccess.display_mode,
            auto_direct = parseInt(href_proccess.auto_direct),
            text_complete = href_proccess.text_complete.trim(),
            elm_exclude = href_proccess.pre_elm_exclude.trim(),
            exclude_elm = elm_exclude.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(","),
            allow_url = href_proccess.prep_url,
            links_noindex_fl = href_proccess.links_noindex_nofollow,
            windowWidth = $(window).width(),
            is_popup = parseInt(href_proccess.is_popup),
            remix_url = href_proccess.remix_url;

        let countdownStatus = {};

        function mix_url(url) {
            url = remix_url.prefix + url;
            var position = Math.floor(url.length / 2);
            url = url.substring(0, position) + remix_url.mix_str + url.substring(position);
            url = url + remix_url.suffix;
            return url;
        }

        function restore_original_url(url) {
            return url.replace(remix_url.prefix, '').replace(remix_url.mix_str, '').replace(remix_url.suffix, '');
        }

        function _setCookie(name, value) {
            var expirationTime = new Date(Date.now() + cookie_time * 60 * 1000);
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
            document.cookie = `${name}=${value}; expires=${expirationTime.toUTCString()}; path=/`;
        }

        function set_cookie_title(title) {
            _setCookie("prep_title", title);
        }

        function set_cookie_url(url) {
            _setCookie("prep_request", url);
        }

        function endpoint_url() {
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
            if (value === undefined) {
                return false;
            }
            for (var i = 0; i < array.length; i++) {
                if (array[i] !== undefined && value.indexOf(array[i]) !== -1) {
                    return true;
                }
            }
            return false;
        }

        function prep_request_link() {
            $('a').each(function () {
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

                    var modified_url = mix_url(btoa(href));

                    if (display_mode === 'progress') {
                        $this.replaceWith('<div class="post-progress-bar"><span id="prep-request" data-id="' + modified_url + '"><strong class="post-progress">' + text_link + '</strong></span></div>');
                    } else {
                        $this.replaceWith('<span class="wrap-countdown"><span id="prep-request" data-id="' + modified_url +'"><strong class="link-countdown">' + text_link + '</strong></span></span>');
                    }

                    var strongElement = $(".post-progress-bar,.wrap-countdown").find("strong:contains('|')");
                    if ($(window).width() < 700 && strongElement.length) {
                        strongElement.remove();
                    }
                }
            });
        }
        
        function processClick() {
            $(document).on('click', '#prep-request', function (e) {
                e.preventDefault();

                const $this = $(this);
                const title = $this.text();
                const modified_url = $this.attr('data-id');
                const url = restore_original_url(modified_url);
                const complete = $this.find('.text-hide-complete').data('complete');

                if (!_isBtoaEncoded(url)) {
                    return;
                }

                if (time_cnf === 0) {
                    set_cookie_title(title);
                    set_cookie_url(modified_url);

                    if (windowWidth > 700) {
                        window.open(endpoint_url(), '_blank');
                    } else {
                        window.location.href = endpoint_url();
                    }
                    return;
                }

                if (complete === 1) {
                    set_cookie_title($this.find('.text-hide-complete').data('text'));
                    set_cookie_url(modified_url);

                    if (windowWidth > 700) {
                        window.open(endpoint_url(), '_blank');
                    } else {
                        window.location.href = endpoint_url();
                    }
                    return;
                }
                
                if (countdownStatus[modified_url] && countdownStatus[modified_url].active) {
                    return;
                }

                if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || url === undefined || url === null || !url.length) {
                    return;
                }

                if (time_cnf > 0) {
                    $this.off('click');
                    countdownStatus[modified_url] = { active: true };
                    if (display_mode === 'wait_time') {
                        _start_countdown($this, modified_url, title);
                    } else {
                        _start_progress($this, modified_url, title);
                    }
                }
            });
        }
        function _start_countdown($elm, url, title) {
            let downloadTimer;
            let timeleft = time_cnf;

            const countdown = () => {
                $elm.html(`<strong> ${wait_text} ${timeleft}s...</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);

                    let wait_time_html = `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
                    wait_time_html += '<span style="vertical-align: unset;">' + text_complete + '</span>';
                    $elm.html(wait_time_html);
                    $elm.parents('.wrap-countdown').css('color', '#ff0000')
                    if (auto_direct) {
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = endpoint_url();
                    }
                    countdownStatus[url] = { active: false };
                } else {
                    setTimeout(countdown, 1000);
                }

            };
            countdown();
        }

        function _start_progress($elm, url, title) {
            const $progress = $elm.find('.post-progress');
            const progressWidth = $progress.width();
            const parent = $elm.parent('.post-progress-bar');

            let currentWidth = 0;
            let timeleft = time_cnf;

            parent.css('width', parent.width());
            $progress.width("0%");
            $progress.css({
                'background-color': '#1479B3',
                'color': '#fff',
                'padding': '0px 10px'
            });

            const intervalId = setInterval(function () {
                currentWidth += progressWidth / (timeleft * 1000 / timeleft);
                $progress.width(currentWidth);
                if (currentWidth >= progressWidth) {
                    clearInterval(intervalId);
                    let progress_html = '<i class="fa fa-angle-double-right fa-shake" style="color: #fff;cursor: pointer;font-size: 13px;"></i>';
                    progress_html += `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
                    progress_html += '<span class="text-complete">' + text_complete + '</span>';
                    $elm.html('<strong class="post-progress" style="color:#0c7c3f">' + progress_html + '</strong>');
                    parent.removeAttr('style');
                    if (auto_direct) {
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = endpoint_url();
                    }
                }
            }, timeleft);
        }

        function _isBtoaEncoded(url) {
            try {
                const decodedHref = atob(url);
                return decodedHref.match(/^https?:\/\/.+/) !== null;
            } catch (e) {
                console.log(e.message);
                return false;
            }
        }

        prep_request_link();
        processClick();
    });
})(jQuery);
