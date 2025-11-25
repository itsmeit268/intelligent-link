(function ($) {
    'use strict';

    $(function () {
        var end_point = href_vars.end_point.trim(),
            current_url = window.location.href.replace(/#.*/, ''),
            time_cnf = parseInt(href_vars.count_down),
            cookie_time = parseInt(href_vars.cookie_time),
            wait_text = href_vars.wait_text.trim(),
            display_mode = href_vars.display_mode,
            auto_direct = parseInt(href_vars.auto_direct),
            text_complete = href_vars.replace_text,
            windowWidth = $(window).width(),
            modify_conf = href_vars.modify_conf,
            meta_attr = href_vars.meta_attr;

        var countdownStatus = {};

        function clear_cookie(cookie_name) {
            document.cookie = cookie_name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }

        function reset_request() {
            var hasLinkParam = current_url.indexOf('?link=') !== -1;

            if (hasLinkParam) {
                return true;
            } else {
                clear_cookie("prep_request");
                clear_cookie("prep_title");
            }
        }

        function href_restore(url) {
            return url.replace(modify_conf.pfix, '').replace(atob(modify_conf.mstr), '').replace(atob(modify_conf.sfix), '');
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

        function intelligent_link() {
            if (current_url.indexOf('?') !== -1) {
                current_url = current_url.split('?')[0];
            }

            if (current_url.indexOf(".html") > -1 && current_url.includes('.html')) {
                current_url = current_url.match(/.*\.html/)[0] + '/';
            } else if (!current_url.endsWith('/')) {
                current_url = current_url + '/';
            }

            return current_url + '?' + end_point + '=1';
        }


        function processClick() {

            $(document).on('click', '.prep-request', function (e) {
                e.preventDefault();

                const $this = $(this);
                const title = $this.attr('data-text') || $this.text().trim() || '>> Redirect Link <<';
                const modified_url = $this.attr('data-request');
                const url = href_restore(modified_url);
                const complete = $this.find('.text-hide-complete').data('complete');
                const is_image = $this.attr('data-image');
                const is_meta = $this.parents('.igl-download-now');

                var start_time = time_cnf;

                if (!modified_url || !url) {
                    return;
                }

                if (is_meta.length) {
                    if (meta_attr.auto_direct === '1' && parseInt(meta_attr.time) === 0) {
                        start_time = 0;
                    }
                    start_time = parseInt(meta_attr.time);
                }

                if (complete === 1) {
                    set_cookie_title($this.find('.text-hide-complete').data('text'));
                    set_cookie_url(modified_url);

                    if (windowWidth > 700) {
                        window.open(intelligent_link(), '_blank');
                    } else {
                        window.location.href = intelligent_link();
                    }
                    return;
                }

                if (countdownStatus[modified_url] && countdownStatus[modified_url].active) {
                    return;
                }

                if (start_time === 0 || is_image === '1') {
                    set_cookie_title(title);
                    set_cookie_url(modified_url);

                    if (windowWidth > 700) {
                        window.open(intelligent_link(), '_blank');
                    } else {
                        window.location.href = intelligent_link();
                    }
                } else {
                    $this.off('click');
                    countdownStatus[modified_url] = {active: true};
                    if (display_mode === 'wait_time') {
                        _start_countdown($this, modified_url, title, is_meta);
                    } else {
                        _start_progress($this, modified_url, title, is_meta);
                    }
                }
            });
        }

        function _start_countdown($elm, url, title, is_meta) {
            let timeleft = is_meta.length ? parseInt(meta_attr.time) : time_cnf;

            const countdown = () => {
                $elm.html(`<strong>${wait_text} ${timeleft}s...</strong>`);
                timeleft--;

                if (timeleft < 0) {
                    let wait_time_html = `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
                    wait_time_html += '<span style="vertical-align: unset;">' + ((text_complete.enable === 'yes') ? text_complete.text : title) + '</span>';

                    $elm.html(wait_time_html);

                    if (!is_meta.length) {
                        $elm.parents('.wrap-countdown').css({'color': '#0c7905', 'font-weight': '600'});
                    } else {
                        $elm.parents('.wrap-countdown').css({'background': '#0c7905'});
                    }

                    if (is_meta.length && meta_attr.auto_direct === '1') {
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = intelligent_link();
                    } else if (!is_meta.length && auto_direct) {
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = intelligent_link();
                    }

                    countdownStatus[url] = {active: false};
                } else {
                    setTimeout(countdown, 1000);
                }
            };
            countdown();
        }

        function _start_progress($elm, url, title, is_meta) {
            const $progress = $elm.find('.post-progress');
            const progressWidth = $progress.width();
            const parent = $elm.parent('.post-progress-bar');

            let currentWidth = 0;
            let timeleft = is_meta.length ? parseInt(meta_attr.time) : time_cnf;

            parent.css({'width': parent.width(), 'margin-right': '25px'});
            $progress.width("0%");

            if (!is_meta.length) {
                $progress.css({
                    'background-color': '#1479B3',
                    'color': '#fff',
                    'padding': '0 10px'
                });
            }

            const intervalId = setInterval(function () {
                if (timeleft <= 3) {
                    currentWidth += progressWidth / 100;
                } else {
                    currentWidth += progressWidth / (timeleft * 1000 / timeleft);
                }

                $progress.width(currentWidth);

                if (currentWidth >= progressWidth) {
                    clearInterval(intervalId);

                    parent.css('margin-right', '0');
                    let progress_html = '<i class="fa fa-angle-double-right fa-shake" style="color: #fff;cursor: pointer;font-size: 13px;"></i>';
                    progress_html += `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
                    progress_html += '<span class="text-complete">' + ((text_complete.enable === 'yes') ? text_complete.text : title) + '</span>';

                    $elm.html('<strong class="post-progress" style="color:#0c7c3f;">' + progress_html + '</strong>');

                    if (parent.parents('.igl-download-now').length) {
                        $elm.html('<strong class="post-progress" style="background-color:#018f06">' + progress_html + '</strong>');
                    }

                    parent.removeAttr('style');

                    if (is_meta.length && meta_attr.auto_direct === '1') {
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = intelligent_link();
                    } else if (!is_meta.length && auto_direct) {
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = intelligent_link();
                    }

                    countdownStatus[url] = {active: false};
                }
            }, timeleft);
        }
        reset_request();
        processClick();
    });
})(jQuery);
