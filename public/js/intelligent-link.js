/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

(function ($) {
    'use strict';

    $(function () {
        var end_point = href_process.end_point.trim(),
            current_url = window.location.href.replace(/#.*/, ''),
            time_cnf = parseInt(href_process.count_down),
            cookie_time = parseInt(href_process.cookie_time),
            wait_text = href_process.wait_text.trim(),
            display_mode = href_process.display_mode,
            auto_direct = parseInt(href_process.auto_direct),
            text_complete = href_process.replace_text,
            elm_exclude = href_process.pre_elm_exclude.trim(),
            exclude_elm = elm_exclude.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(","),
            allow_url = href_process.prep_url,
            windowWidth = $(window).width(),
            href_modify = href_process.modify_href,
            meta_attr   = href_process.meta_attr;

        var countdownStatus = {};

        function modify_href(url) {
            url = href_modify.pfix + url;
            var position = Math.floor(url.length / 2);
            url = url.substring(0, position) + href_modify.mstr + url.substring(position);
            url = url + href_modify.sfix;
            return url;
        }

        function href_restore(url) {
            return url.replace(href_modify.pfix, '').replace(href_modify.mstr, '').replace(href_modify.sfix, '');
        }

        function _setCookie(n, v) {
            var expirationTime = new Date(Date.now() + cookie_time * 60 * 1000);
            document.cookie = `${n}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
            document.cookie = `${n}=${v}; expires=${expirationTime.toUTCString()}; path=/`;
        }

        function set_cookie_title(title) {
            _setCookie("prep_title", title);
        }

        function set_cookie_url(url) {
            _setCookie("prep_request", url);
        }

        function endpoint_url() {
            if (current_url.indexOf('?') !== -1) {
                current_url = current_url.split('?')[0];
            }

            if (current_url.indexOf(".html") > -1 && current_url.includes('.html')) {
                current_url = current_url.match(/.*\.html/)[0] + '/';
            } else if (current_url.includes('/' + end_point + '/')) {
                return current_url;
            } else if (current_url.indexOf('.html') === -1 && !current_url.endsWith('/')) {
                current_url = current_url + '/';
            }

            return current_url + end_point;
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
                    href = $this.attr('href'),
                    allow_urls = allow_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(","),
                    text_link = $this.text().trim() || '>> link <<';

                if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || href === undefined || href === null || !href.length) {
                    return;
                }

                if (allow_url !== "" && contains_value(href, allow_urls)) {

                    if (href === encodeURIComponent(decodeURIComponent(href))) {
                        href = decodeURIComponent(href);
                    }

                    $this.attr('rel', 'nofollow noopener noreferrer');

                    var modified_url = modify_href(btoa(href));
                    var imgExists = $this.find("img").length > 0;
                    var svgExists = $this.find("svg").length > 0;
                    var icon_Exists = $this.find("i").length > 0;

                    if (imgExists || svgExists || icon_Exists) {
                        $this.attr({'href': 'javascript:void(0)', 'data-id': modified_url, 'data-text': text_link, 'data-image': '1'}).addClass('prep-request');
                    } else {
                        var replacement;
                        if (display_mode === 'progress') {
                            replacement = '<div class="post-progress-bar"><span class="prep-request" data-id="' + modified_url + '"><strong class="post-progress">' + text_link + '</strong></span></div>';
                        } else {
                            replacement = '<span class="wrap-countdown"><span class="prep-request" data-id="' + modified_url +'"><strong class="link-countdown">' + text_link + '</strong></span></span>';
                        }
                        $this.replaceWith(replacement);
                    }
                }
            });
        }
        
        function processClick() {
            $(document).on('click', '.prep-request', function (e) {
                e.preventDefault();

                const $this = $(this);
                const title = $this.text().trim() || '>> link <<';
                const modified_url = $this.attr('data-id');
                const url = href_restore(modified_url);
                const complete = $this.find('.text-hide-complete').data('complete');
                const is_image = $this.attr('data-image');

                const is_meta = $this.parents('#igl-download-now');
                if (!_isBtoaEncoded(url)) {
                    return;
                }

                console.log(meta_attr);
                console.log(is_meta);

                if (is_meta.length && meta_attr.auto_direct && meta_attr.time === '0') {
                    set_cookie_title(title);
                    set_cookie_url(modified_url);

                    if (windowWidth > 700) {
                        window.open(endpoint_url(), '_blank');
                    } else {
                        window.location.href = endpoint_url();
                    }
                    return;
                }

                if (time_cnf === 0 || is_image === '1') {
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
            var replace_title;

            if (text_complete.enable === 'yes') {
                replace_title = text_complete.text;
            }

            const countdown = () => {
                $elm.html(`<strong> ${wait_text} ${timeleft}s...</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);

                    let wait_time_html = `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
                    wait_time_html += '<span style="vertical-align: unset;">' + replace_title + '</span>';
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

            if (!parent.parent('.igl-download-now').length) {
                $progress.css({
                    'background-color': '#1479B3',
                    'color': '#fff',
                    'padding': '0 10px'
                });
            }

            const intervalId = setInterval(function () {
                let replace_title = '';

                currentWidth += progressWidth / (timeleft * 1000 / timeleft);
                $progress.width(currentWidth);
                if (currentWidth >= progressWidth) {
                    clearInterval(intervalId);

                    if (text_complete.enable === 'yes') {
                        replace_title = text_complete.text;
                    }

                    let progress_html = '<i class="fa fa-angle-double-right fa-shake" style="color: #fff;cursor: pointer;font-size: 13px;"></i>';
                    progress_html += `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
                    progress_html += '<span class="text-complete">' + replace_title + '</span>';
                    $elm.html('<strong class="post-progress" style="color:#0c7c3f;">' + progress_html + '</strong>');
                    if (parent.parent('.igl-download-now').length) {
                        $elm.html('<strong class="post-progress" style="background-color:#018f06">' + progress_html + '</strong>');
                    }

                    parent.removeAttr('style');
                    if (auto_direct) {
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = endpoint_url();
                    }
                    countdownStatus[url] = { active: false };
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

        function clear_cookie(cookie_name) {
            document.cookie = cookie_name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
        
        function reset_request(){
            var regex = new RegExp('(/' + end_point + '/)|(/' + end_point + ')|(.html/' + end_point + ')');
            if (regex.test(current_url)) {
                return true;
            } else {
                clear_cookie("prep_request");
                clear_cookie("prep_title");
            }
        }

        reset_request();
        prep_request_link();
        processClick();
    });
})(jQuery);
