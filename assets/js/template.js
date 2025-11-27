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
        const enable_rewrite = prep_template.enable_rewrite;
        const ajax_url = prep_template.ajax_url;

        function getCookie(name) {
            const match = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]*)'));
            return match ? match[2] : null;
        }

        function process_direct_link(link) {
            if (enable_rewrite) {
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    data: {
                        action: 'handle_direct_link',
                        link: link
                    },
                    success: function(response) {
                        if (response.success && response.data && response.data.link) {
                            window.location.href = response.data.link;
                        } else {
                            console.error('AJAX failed:', response.data?.message || 'Unknown error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX request failed:', error);
                    }
                });
            } else {
                window.location.href = link;
            }
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
                if (link) {
                    process_direct_link(link);
                }
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
                        const link = $(this).data('request');
                        if (link) {
                            process_direct_link(link);
                        }
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
                if (link) {
                    process_direct_link(link);
                }
            });
        }

        function scrollToProgressElm() {
            $('.clickable,.prep-title').on('click', function () {
                if (timeCnf === 0) {
                    const link = getCookie('prep_request');
                    if (link) {
                        process_direct_link(link);
                    }
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