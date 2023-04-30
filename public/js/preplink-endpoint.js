/**
 * Script cho riêng trang /download (endpoint) nếu bạn có thể cập nhật code,
 * hoặc thêm mới các tính năng, fix bug, chính sửa các tính năng
 * Vui lòng chia sẻ tới cộng đồng bằng cách gửi pull requests trên branch mới.
 *
 * @link       https://github.com/itsmeit268/preplink
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

(function ($) {
    'use strict';
    $(function () {
        var urlParams = new URLSearchParams(window.location.search);
        var preUrlGo = urlParams.get('id');
        var $progress = $('#progress');
        var time_cnf = parseInt(prep_vars.countdown_endpoint);
        var auto_direct = parseInt(prep_vars.endpoint_direct);

        function _update_link_direct() {
            var decodedUrl = window.atob(preUrlGo);
            return window.location.replace(decodedUrl);
        }

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
                                _update_link_direct();
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
                            _update_link_direct();
                        }
                    });
                });
            }
        }

        scrollToProgressElm();
        progressRunning();
    });
})(jQuery);
