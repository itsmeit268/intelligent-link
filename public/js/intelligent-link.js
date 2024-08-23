/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co/
 */

(function ($) {
    'use strict';

    $(function () {
        var end_point   = href_vars.end_point,
            current_url = window.location.href.replace(/#.*/, ''),
            cookie_time = parseInt(href_vars.cookie_time),
            elm_exclude = href_vars.pre_elm_exclude.trim(),
            exclude_elm = elm_exclude.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(","),
            allow_url   = href_vars.prep_url,
            windowWidth = $(window).width(),
            modify_conf = href_vars.modify_conf,
            nofollow    = parseInt(href_vars.nofollow),
            encrypt_url = parseInt(href_vars.encrypt_url);

        function intelligent_link() {
            if (current_url.indexOf('?') !== -1) {
                current_url = current_url.split('?')[0];
            }

            if (current_url.indexOf(".html") > -1 && current_url.includes('.html')) {
                current_url = current_url.match(/.*\.html/)[0] + '?'+end_point+'=1';
            } else if (current_url.includes('/?'+end_point+'=1')) {
                return current_url;
            } else if (current_url.indexOf('.html') === -1 && !current_url.endsWith('/')) {
                current_url = current_url + '/?'+end_point+'=1';
            } else if (current_url.endsWith('/')) {
                current_url = current_url + '?' + end_point + '=1';
            }
            return current_url;
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

        function clear_cookie(cookie_name) {
            document.cookie = cookie_name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }

        function reset_request() {
            const link = window.location.href;
            const regex = new RegExp('[?&]' + end_point + '=1');

            if (regex.test(link)) {
                return true;
            } else {
                clear_cookie("prep_request");
                clear_cookie("prep_title");
                clear_cookie("prep_meta");
            }
        }

        function _setCookie(n, v) {
            var expirationTime;
            if (cookie_time === 0) {
                expirationTime = new Date('Fri, 31 Dec 9999 23:59:59 UTC');
            } else {
                expirationTime = new Date(Date.now() + cookie_time * 60 * 1000);
            }
            document.cookie = `${n}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
            document.cookie = `${n}=${v}; expires=${expirationTime.toUTCString()}; path=/`;
        }

        function prep_request_link() {
            $('a').each(function () {
                var self = $(this),
                    href = self.attr('href'),
                    allow_urls = allow_url.replace(/\\r\\|\r\n|\s/g, "").replace(/^,|,$/g, '').split(",");

                if (exclude_elm.some(sel => self.is(sel)) || self.closest(exclude_elm.join(',')).length > 0 || href === undefined || href === null || !href.length) {
                    return;
                }

                if (allow_url !== "" && contains_value(href, allow_urls)) {
                    if (href === encodeURIComponent(decodeURIComponent(href))) {
                        href = decodeURIComponent(href);
                    }

                    // var domain = window.location.origin;
                    // var relativeUrl = intelligent_link().replace(domain, '');

                    self.removeAttr('data-id');
                    self.attr({
                        'data-req': modify_href(btoa(href)),
                        'data-meta': '0',
                        'href': encrypt_url ? intelligent_link() : href,
                    }).addClass('prep-request');

                    nofollow ? self.attr('rel', 'nofollow'): '';
                    !encrypt_url ? self.removeAttr('data-req'): '';
                }
            });
        }

        function modify_href(str) {
            str = str.substring(0, 3) + modify_conf.pfix + str.substring(3);
            var position = Math.floor(str.length / 2);
            str = str.substring(0, position) + modify_conf.mstr + str.substring(position);
            str = str.substring(0, str.length - 4) + modify_conf.sfix + str.substring(str.length - 4);
            return str;
        }

        function processClick() {
            $(document).on('click', '.prep-request', function (e) {
                e.preventDefault();
                var self = $(this);
                if (exclude_elm.some(sel => self.is(sel)) || self.closest(exclude_elm.join(',')).length > 0) {
                    return;
                }

                clear_cookie("prep_request");
                clear_cookie("prep_title");
                clear_cookie("prep_meta");

                let link = encrypt_url ? self.attr('data-req') : modify_href(btoa(self.attr('href')));

                const title = self.text() || '>> Redirect Link <<';
                const is_meta = parseInt(self.attr('data-meta'));

                if (!is_meta && !encrypt_url) {
                    link = self.attr('href');
                }
                pre_cookie(link, title, self.attr('data-meta'));

                windowWidth > 700 ? window.open(intelligent_link(), '_blank') : window.location.href = intelligent_link();
            });
        }

        function pre_cookie(url, title, meta) {
            if (url !== undefined && url !== null && url.length > 0) {
                _setCookie("prep_title", title);
                _setCookie("prep_request", url);
            }
            _setCookie("prep_meta", meta);
        }

        function fix_elm(){
            var spanElement = $('li.file_version span');
            var spanText = spanElement.text().trim();
            if (spanText === "") {
                spanElement.text("Compatible with devices");
            }
        }

        fix_elm();
        reset_request();
        prep_request_link();
        processClick();
    });
})(jQuery);
