(function ($) {
    'use strict';

    $(function () {

        const config = {
            endPoint: href_vars.end_point.trim(),
            currentUrl: window.location.href.replace(/#.*/, ''),
            timeConfig: parseInt(href_vars.count_down),
            cookieTime: parseInt(href_vars.cookie_time),
            waitText: href_vars.wait_text.trim(),
            displayMode: href_vars.display_mode,
            autoDirect: parseInt(href_vars.auto_direct),
            textComplete: href_vars.replace_text,
            windowWidth: $(window).width(),
            isRewriteEnabled: href_vars.enable_rewrite || false,
            metaAttr: href_vars.meta_attr || {}
        };

        const countdownStatus = {};

        const CookieManager = {
            clear(name) {
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
            },

            set(name, value) {
                const expirationTime = new Date(Date.now() + config.cookieTime * 60 * 1000);
                this.clear(name);
                document.cookie = `${name}=${value}; expires=${expirationTime.toUTCString()}; path=/`;
            }
        };

        const UrlManager = {
            buildIntelligentLink() {
                let url = config.currentUrl.split('?')[0];

                if (url.includes('.html')) {
                    url = url.match(/.*\.html/)[0] + '/';
                } else if (!url.endsWith('/')) {
                    url += '/';
                }

                return `${url}?${config.endPoint}=1`;
            }
        };

        function navigate(url, title, isMeta) {
            CookieManager.set('prep_title', title);
            CookieManager.set('prep_request', url);
            CookieManager.set('prep_meta', isMeta);

            const targetUrl = UrlManager.buildIntelligentLink();

            if (config.windowWidth > 700) {
                window.open(targetUrl, '_blank');
            } else {
                window.location.href = targetUrl;
            }
        }

        function updateCompleteState($elm, title, url, isMeta, isProgress = false) {
            const completeText = config.textComplete.enable === 'yes' ? config.textComplete.text : title;

            if (isProgress) {
                $elm.addClass('completed');
                $elm.attr('data-original-text', title);
                $elm.find('.post-progress').html(`<span class="text-complete">${completeText}</span>`);
                $elm.find('.post-progress').removeAttr('style');
            } else {
                $elm.html(`<strong class="link-countdown"><span class="text-complete">${completeText}</span></strong>`);

                const $wrapper = $elm.parents('.wrap-countdown');
                if (isMeta) {
                    $wrapper.css('background', '#0c7905');
                } else {
                    $wrapper.css({ 'color': '#0c7905', 'font-weight': '600' });
                }
            }

            if ((config.metaAttr.auto_direct === '1') || config.autoDirect) {
                navigate(url, title, isMeta);
            }

            countdownStatus[url] = { active: false };
        }

        function startCountdown($elm, url, title, isMeta) {
            let timeleft = isMeta && config.metaAttr ? parseInt(config.metaAttr.time) : config.timeConfig;

            const countdown = () => {
                $elm.html(`<strong class="post-progress">${config.waitText} ${timeleft}s...</strong>`);
                timeleft--;

                if (timeleft < 0) {
                    $elm.addClass('completed');
                    $elm.attr('data-original-text', title);
                    updateCompleteState($elm, title, url, isMeta, false);
                } else {
                    setTimeout(countdown, 1000);
                }
            };

            countdown();
        }

        function startProgress($elm, url, title, isMeta) {
            const timeleft = isMeta && config.metaAttr ? parseInt(config.metaAttr.time) : config.timeConfig;

            $elm.addClass('loading');
            $elm.css('--progress', '0%');

            const startTime = Date.now();
            const totalTime = timeleft * 1000;

            function updateProgress() {
                const elapsed = Date.now() - startTime;
                const progress = Math.min((elapsed / totalTime) * 100, 100);

                $elm.css('--progress', progress + '%');

                if (progress < 100) {
                    requestAnimationFrame(updateProgress);
                } else {
                    updateCompleteState($elm, title, url, isMeta, true);
                }
            }

            updateProgress();
        }


        function processClick() {
            $(document).on('click', '.prep-request', function (e) {
                e.preventDefault();

                const $this = $(this);
                const title = $this.attr('data-text') || $this.text().trim() || '>> Redirect Link <<';
                const url = $this.attr('data-request');
                // SỬA: Kiểm tra class completed thay vì text-hide-complete
                const isCompleted = $this.hasClass('completed');
                const isImage = $this.attr('data-image');
                const isMeta = $this.attr('data-meta') ? parseInt($this.attr('data-meta')) : 0;

                if (!url) return;

                let startTime = config.timeConfig;

                if (isMeta && config.metaAttr) {
                    startTime = parseInt(config.metaAttr.time);
                    if (config.metaAttr.auto_direct === '1' && startTime === 0) {
                        startTime = 0;
                    }
                }

                // SỬA: Kiểm tra nếu đã completed thì navigate luôn
                if (isCompleted) {
                    navigate(url, title, isMeta);
                    return;
                }

                if (countdownStatus[url]?.active) return;

                if (startTime === 0 || isImage === '1') {
                    navigate(url, title, isMeta);
                } else {
                    $this.off('click');
                    countdownStatus[url] = { active: true };

                    if (config.displayMode === 'wait_time') {
                        startCountdown($this, url, title, isMeta);
                    } else {
                        startProgress($this, url, title, isMeta);
                    }
                }
            });
        }

        function resetRequest() {
            if (config.currentUrl.indexOf(`?${config.endPoint}=`) === -1) {
                ['prep_meta', 'prep_request', 'prep_title'].forEach(CookieManager.clear.bind(CookieManager));
            }
        }

        // resetRequest();
        processClick();
    });
})(jQuery);