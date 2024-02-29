/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

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
            elm_exclude = href_vars.pre_elm_exclude.trim(),
            exclude_elm = elm_exclude.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(","),
            allow_url = href_vars.prep_url,
            windowWidth = $(window).width(),
            modify_conf = href_vars.modify_conf,
            meta_attr   = href_vars.meta_attr,
            href_ex_elm = href_vars.href_ex_elm;

        var countdownStatus = {};

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

        function modify_href(url) {
            url = url.substring(0, 5) + modify_conf.pfix + url.substring(5);
            var position = Math.floor(url.length / 2);
            url = url.substring(0, position) + atob(modify_conf.mstr) + url.substring(position);
            url = url.substring(0, url.length - 8) + atob(modify_conf.sfix) + url.substring(url.length - 8);
            return url;
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
                    text_link = $this.text().trim() || '>> Redirect Link <<';

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

                    var excludedElements = href_ex_elm.split(",").map(function(item) {
                        return item.trim();
                    });

                    if (imgExists || svgExists || icon_Exists || $this.is(excludedElements.join(','))) {
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
                const title = $this.text().trim() || '>> Redirect Link <<';
                const modified_url = $this.attr('data-id');
                const url = href_restore(modified_url);
                const complete = $this.find('.text-hide-complete').data('complete');
                const is_image = $this.attr('data-image');
                const is_meta = $this.parents('.igl-download-now');

                var start_time = time_cnf;

                if (!_isBtoaEncoded(url)) {
                    return;
                }

                if (exclude_elm.some(sel => $this.is(sel)) || $this.closest(exclude_elm.join(',')).length > 0 || url === undefined || url === null || !url.length) {
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
                    countdownStatus[modified_url] = { active: true };
                    if (display_mode === 'wait_time') {
                        _start_countdown($this, modified_url, title, is_meta);
                    } else {
                        _start_progress($this, modified_url, title, is_meta);
                    }
                }

            });
        }

        function _start_countdown($elm, url, title, is_meta) {
            let downloadTimer;
            let timeleft = is_meta.length? parseInt(meta_attr.time) : time_cnf;

            const countdown = () => {
                $elm.html(`<strong> ${wait_text} ${timeleft}s...</strong>`);
                timeleft--;
                if (timeleft < 0) {
                    clearInterval(downloadTimer);

                    let wait_time_html = `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
                    wait_time_html += '<span class="text-show-complete">' + ((text_complete.enable === 'yes') ? text_complete.text : title) + '</span>';

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
                    } else if (!is_meta.length && auto_direct){
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = intelligent_link();
                    }

                    countdownStatus[url] = { active: false };
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
            let timeleft = is_meta.length? parseInt(meta_attr.time) : time_cnf;

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
                } else{
                    currentWidth += progressWidth / (timeleft * 1000 / timeleft);
                }

                $progress.width(currentWidth);
                if (currentWidth >= progressWidth) {
                    clearInterval(intervalId);

                    parent.css('margin-right', '0');
                    let progress_html = `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span>`;
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
                    } else if (!is_meta.length && auto_direct){
                        set_cookie_title(title);
                        set_cookie_url(url);
                        window.location.href = intelligent_link();
                    }
                    countdownStatus[url] = { active: false };
                }
            }, timeleft);
        }

        function remove_empty_elm(){
            if ($('.list-link-redirect ul').is(':empty')) {
                $('.list-link-redirect').remove();
            }
        }

        remove_empty_elm();
        reset_request();
        prep_request_link();
        processClick();
    });
})(jQuery);
