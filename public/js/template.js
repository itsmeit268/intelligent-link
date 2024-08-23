/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co/
 */

(function ($) {
    'use strict';

    $(function () {
        var $progress = $('#endpoint-progress'),
            time_cnf = parseInt(prep_template.countdown),
            t2_timer = $('#preplink-timer-link'),
            href_modify = prep_template.modify_conf,
            end_point   = href_vars.end_point,
            encrypt_url = parseInt(href_vars.encrypt_url);

        function href_restore(url) {
            if (url.includes(atob(href_modify.mstr)) || url.includes(atob(href_modify.sfix))) {
                return url.replace(atob(href_modify.pfix), '').replace(atob(href_modify.mstr), '').replace(atob(href_modify.sfix), '');
            }
            return url.replace(href_modify.pfix, '').replace(href_modify.mstr, '').replace(href_modify.sfix, '');
        }

        function getDownloadLinkParameter(url) {
            const urlObj = new URL(url);
            const params = new URLSearchParams(urlObj.search);
            return params.get(end_point);
        }

        function redirect_link() {
            $('.preplink-btn-link,.list-preplink-btn-link').on('click', function (e) {
                e.preventDefault();
                var _href = $(this).attr('href');
                if (encrypt_url) {
                    var parameter = getDownloadLinkParameter($(this).attr('href'));
                    if ($(this).parents('.ilgl-file-timer-btn').hasClass('cok-btn')) {
                        _href = atob(href_restore(atob(parameter)));
                    } else {
                        _href = atob(href_restore(parameter));
                    }
                }
                window.location.href = _href;
            });
        }

        function scrollToProgressElm() {
            $('.clickable,.prep-title').on('click', function () {
                $progress.trigger('click');
                $('html, body').animate({
                    scrollTop: $progress.offset().top - 150
                }, 100);
            });
        }

        /**
         * Chức năng xử lý sự kiện click để download/nhận liên kết */
        function progressRunning() {
            if (time_cnf > 0) {
                var isProgressRunning = false;
                $progress.on('click', function (e) {
                    e.preventDefault();
                    var $progress = $(this);
                    $progress.show();

                    if (isProgressRunning) {
                        return;
                    }
                    isProgressRunning = true;

                    const $counter = $('.counter');
                    const startTime = new Date().getTime();
                    const totalTime = time_cnf * 1000;
                    let isCountdownFinished = false;

                    function updateProgress() {
                        const currentTime = new Date().getTime();
                        const timeRemaining = totalTime - (currentTime - startTime);

                        if (timeRemaining <= 200) {
                            $counter.html('');
                            $('.prep-btn-download').appendTo($counter).fadeIn(1000);

                            if ($('.list-link-redirect,.list-server-download').length) {
                                $('.list-server-download').fadeIn(1000);
                                $progress.fadeOut(100);
                            }

                            clearInterval(interval);
                            isCountdownFinished = true;
                            isProgressRunning = false;
                            $progress.off('click');
                        } else if (!isCountdownFinished) {
                            const percent = Math.floor((1 - timeRemaining / totalTime) * 100);
                            $('.bar').css('width', percent + '%');
                            $counter.html(percent + '%');
                        }

                        if (isCountdownFinished) {
                            $('.bar').css('width', '100%');
                        }
                    }

                    let interval = setInterval(updateProgress, 10);
                    setTimeout(() => clearInterval(interval), totalTime);

                    $counter.on('click', function (e) {
                        if (!isCountdownFinished) {
                            e.preventDefault();
                        }
                    });
                });

                /**
                 * Countdown
                 */
                if (t2_timer.length) {
                    var data_time = t2_timer.attr('data-time');

                    function countdown(sec) {
                        sec--;
                        if (sec > 0) {
                            t2_timer.html('' + sec + '');
                            setTimeout(function () {
                                countdown(sec);
                            }, 1200);
                        } else {
                            $("#buttondw").addClass('del-timer');
                        }
                    }

                    countdown(data_time);
                }
            }
        }

        progressRunning();
        redirect_link();
        scrollToProgressElm();
    });
})(jQuery);
