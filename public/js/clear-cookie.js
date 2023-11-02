/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

(function ($) {
    'use strict';

    $(function () {
        var end_point = $.trim(cookie_vars.end_point),
            current_url = window.location.href.replace(/#.*/, '');
        var regex = new RegExp('(/' + end_point + '/)|(/' + end_point + ')|(.html/' + end_point + ')');

        function deleteCookie(cookieName) {
            document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }

        function _clear_cookie(){
            if (regex.test(current_url)) {
                return true;
            } else {
                deleteCookie("prep_link_href");
                deleteCookie("prep_text_link");
            }
        }

        _clear_cookie();
    });
})(jQuery);
