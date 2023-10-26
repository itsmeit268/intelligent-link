/**
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */

(function ($) {
    'use strict';

    $(function () {
        var $progress   = $('#enpoint-progress');
        var time_cnf    = parseInt(href_proccess.countdown_endpoint);
        var auto_direct = parseInt(href_proccess.endpoint_direct);
        var preUrlGo    = $('#prep-link-single-page').attr('data-url');
        var gogo_link   = $('.gogo-link');

        function scrollToProgressElm() {
            $('.clickable').on('click', function () {
                if (time_cnf === 0) {
                    window.location.href = preUrlGo.atob(preUrlGo);
                    return;
                }
                $progress.trigger('click');
                $('html, body').animate({
                    scrollTop: $progress.offset().top - 150
                }, 100);
            });
        }

        /**
         * Chức năng xử lý sự kiện click để download/nhận liên kết */
        function progressRunning(){
            if (time_cnf > 0){
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
                            $('.prep-btn-download').appendTo($counter).show();

                            var list_download = $('.tr-thd');
                            if (list_download.length) {
                                $('.list-server-download').show();
                            }

                            clearInterval(interval);
                            isCountdownFinished = true;
                            isProgressRunning = false;
                            $progress.off('click');
                            if (auto_direct){
                                window.location.href = window.atob(preUrlGo);
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
                            window.location.href = window.atob(preUrlGo);
                        }
                    });
                });
            }
        }

        function redirect_link() {
            if (gogo_link.length) {
                gogo_link.on('click', function (e) {
                    e.preventDefault();
                    var url = $(this).attr('data-url');
                    if (url) {
                        window.location.href = window.atob(url);
                    }
                });
            }
        }

        function faqPrepLink() {
            var question = ".prep-link-question";
            var answer = ".prep-link-answer";
            if ($(question).length) {
                $(question).on("click", function () {
                    if (!$(this).parent().find(answer).is(":visible")) {
                        $(question).removeClass("faq-active");
                        $(this).addClass("faq-active");
                        $(answer).hide();
                    } else {
                        $(this).removeClass("faq-active");
                    }
                    $(this).parent().find(answer).toggle(300);
                });
            }
        }

        scrollToProgressElm();
        progressRunning();
        faqPrepLink();
        redirect_link();
    });
})(jQuery);
