/**
 * Script cho riêng trang /download (endpoint) nếu bạn có thể cập nhật code,
 * hoặc thêm mới các tính năng, fix bug, chính sửa các tính năng
 * Vui lòng chia sẻ tới cộng đồng bằng cách gửi pull requests trên branch mới.
 *
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

(function ($) {
    'use strict';

    $(function () {
        var $progress   = $('#enpoint-progress');
        var time_cnf    = parseInt(prep_vars.countdown_endpoint);
        var auto_direct = parseInt(prep_vars.endpoint_direct);
        var preUrlGo    = $('#prep-link-single-page').attr('data-url');

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
    });
})(jQuery);
