/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

(function ($) {
    'use strict';

    $(function () {
        var $progress         = $('#enpoint-progress'),
            time_cnf          = parseInt(prep_template.countdown_endpoint),
            auto_direct       = parseInt(prep_template.endpoint_direct),
            page_elm          = $('#prep-link-single-page'),
            preUrlGo          = page_elm.data('request'),
            t2_timer          = $('#preplink-timer-link'),
            require_vip       = $('.not-vip-download'),
            is_user_logged_in = prep_template.is_user_logged_in,
            remix_url         = prep_template.remix_url;

        if (is_user_logged_in) {
            time_cnf = 1;
        }

        function restore_original_url(url) {
            return url.replace(remix_url.prefix, '').replace(remix_url.mix_str, '').replace(remix_url.suffix, '');
        }

        /**
         * Chức năng xử lý sự kiện click để download/nhận liên kết */
        function progressRunning(){
            if (time_cnf > 0){

                /**
                 * Default Template
                 * @type {boolean}
                 */
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

                            if ($('.list-link-redirect,.not-vip-download').length) {
                                $('.list-server-download').fadeIn(1000);

                                $progress.fadeOut(100);

                                if (require_vip.length) {
                                    require_vip.fadeIn(1000);
                                    $('#prep-link-single-page').removeAttr('data-request');
                                }
                            }

                            clearInterval(interval);
                            isCountdownFinished = true;
                            isProgressRunning = false;
                            $progress.off('click');
                            if (auto_direct){
                                window.location.href = window.atob(restore_original_url(preUrlGo));
                            }
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
                        } else {
                            window.location.href = window.atob(restore_original_url(preUrlGo));
                        }
                    });
                });

                /**
                 * Template 1
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
                            if (auto_direct){
                                var request_link = restore_original_url(preUrlGo);
                                window.location.href = window.atob(request_link);
                            }
                        }
                    }

                    countdown(data_time);
                }
            }
        }

        function redirect_link() {
            $('.preplink-btn-link,.list-preplink-btn-link').on('click', function (e) {
                e.preventDefault();
                var data_request = $(this).data('request');
                var request_link = restore_original_url(data_request)
                window.location.href = window.atob(request_link || restore_original_url(preUrlGo));
            });
        }

        function faq_download() {
            if ($('.faq-download').length) {
                const items = $('.accordion button');
                function toggleAccordion() {
                    const itemToggle = $(this).attr('aria-expanded');
                    items.attr('aria-expanded', 'false');
                    items.removeClass('faq-active');
                    if (itemToggle == 'false') {
                        $(this).addClass('faq-active');
                        $(this).attr('aria-expanded', 'true');
                        $(this).next('.content').slideDown(1000);
                    } else {
                        $(this).next('.content').slideUp(1000);
                    }
                }
                items.click(toggleAccordion);
            }
        }

        function scrollToProgressElm() {
            $('.clickable').on('click', function () {
                if (time_cnf === 0) {
                    window.location.href = preUrlGo.atob(restore_original_url(preUrlGo));
                    return;
                }
                $progress.trigger('click');
                $('html, body').animate({
                    scrollTop: $progress.offset().top - 150
                }, 100);
            });
        }

        progressRunning();
        redirect_link();
        faq_download();
        scrollToProgressElm();
    });
})(jQuery);
