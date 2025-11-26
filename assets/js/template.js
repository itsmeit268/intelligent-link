(function ($) {
    'use strict';

    $(function () {

        const $progress = $('#endpoint-progress');
        const $counter = $('.counter');
        const $bar = $('.bar');
        const $t2Timer = $('#preplink-timer-link');
        const $buttonDw = $('#buttondw');

        const timeCnf = parseInt(prep_template.countdown_endpoint);
        const autoDirect = parseInt(prep_template.endpoint_direct);
        const hrefModify = prep_template.modify_conf;
        const isRewriteEnabled = prep_template.enable_rewrite || false;

        function getCookie(name) {
            const match = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]*)'));
            return match ? match[2] : null;
        }

        function hrefRestore(url) {
            if (!isRewriteEnabled) return url;

            const mstr = atob(hrefModify.mstr);
            const sfix = atob(hrefModify.sfix);

            if (url.includes(mstr) || url.includes(sfix)) {
                return url.replace(hrefModify.pfix, '').replace(mstr, '').replace(sfix, '');
            }
            return url.replace(hrefModify.pfix, '').replace(hrefModify.mstr, '').replace(hrefModify.sfix, '');
        }

        function getRedirectUrl(link) {
            const restoredLink = hrefRestore(link);
            return isRewriteEnabled ? window.atob(restoredLink) : restoredLink;
        }

        function redirectToLink(link) {
            window.location.href = getRedirectUrl(link);
        }

        function showDownloadButton() {
            $counter.html('');
            $('.prep-btn-download').appendTo($counter).fadeIn(1000);

            if ($('.list-link-redirect,.not-vip-download').length) {
                $('.list-server-download').fadeIn(1000);
                $progress.fadeOut(100);
            }
        }

        function handleAutoRedirect() {
            if (autoDirect) {
                const link = getCookie('prep_request');
                redirectToLink(link);
            }
        }

        function progressRunning() {
            if (timeCnf <= 0) return;

            let isProgressRunning = false;

            $progress.on('click', function (e) {
                e.preventDefault();

                if (isProgressRunning) return;
                isProgressRunning = true;

                $progress.show();

                const startTime = Date.now();
                const totalTime = timeCnf * 1000;
                let isCountdownFinished = false;

                function updateProgress() {
                    const timeRemaining = totalTime - (Date.now() - startTime);

                    if (timeRemaining <= 200) {
                        showDownloadButton();
                        clearInterval(interval);
                        isCountdownFinished = true;
                        isProgressRunning = false;
                        $progress.off('click');
                        handleAutoRedirect();
                    } else {
                        const percent = Math.floor((1 - timeRemaining / totalTime) * 100);
                        $bar.css('width', percent + '%');
                        $counter.html(percent + '%');
                    }
                }

                const interval = setInterval(updateProgress, 10);
                setTimeout(() => clearInterval(interval), totalTime);

                $counter.on('click', function (e) {
                    if (!isCountdownFinished) {
                        e.preventDefault();
                    } else {
                        redirectToLink(preUrlGo);
                    }
                });
            });

            if ($t2Timer.length) {
                const dataTime = parseInt($t2Timer.attr('data-time'));

                function countdown(sec) {
                    if (--sec > 0) {
                        $t2Timer.html(sec);
                        setTimeout(() => countdown(sec), 1200);
                    } else {
                        $buttonDw.addClass('del-timer');
                        handleAutoRedirect();
                    }
                }

                countdown(dataTime);
            }
        }

        function redirectLink() {
            $('.preplink-btn-link,.list-preplink-btn-link').on('click', function (e) {
                e.preventDefault();
                const link = $(this).data('request');
                redirectToLink(link);
            });
        }

        function scrollToProgressElm() {
            $('.clickable,.prep-title').on('click', function () {
                if (timeCnf === 0) {
                    const link = getCookie('prep_request');
                    redirectToLink(link);
                    return;
                }

                $progress.trigger('click');
                $('html, body').animate({
                    scrollTop: $progress.offset().top - 150
                }, 100);
            });
        }

        progressRunning();
        redirectLink();
        scrollToProgressElm();
    });
})(jQuery);
