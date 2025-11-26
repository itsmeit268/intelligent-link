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
            modifyConf: href_vars.modify_conf,
            metaAttr: href_vars.meta_attr,
            isRewriteEnabled: href_vars.enable_rewrite || false
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
            restore(url) {
                if (!config.isRewriteEnabled) {
                    try {
                        return atob(url);
                    } catch (e) {
                        return url;
                    }
                }
                const { pfix, mstr, sfix } = config.modifyConf;
                return url.replace(pfix, '').replace(atob(mstr), '').replace(atob(sfix), '');
            },

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

        function navigate(url, title, modifiedUrl, isMeta) {
            CookieManager.set('prep_title', title);
            CookieManager.set('prep_request', modifiedUrl);
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
                const $parent = $elm.parent('.post-progress-bar');
                const icon = '<i class="fa fa-angle-double-right fa-shake" style="color: #fff;cursor: pointer;font-size: 13px;"></i>';
                const content = `${icon}<span class="text-hide-complete" data-complete="1" data-text="${title}"></span><span class="text-complete">${completeText}</span>`;

                const bgColor = $parent.parents('.igl-download-now').length ? '#018f06' : '#0c7c3f';
                $elm.html(`<strong class="post-progress" style="${$parent.parents('.igl-download-now').length ? 'background-color' : 'color'}:${bgColor}">${content}</strong>`);
                $parent.css('margin-right', '0').removeAttr('style');
            } else {
                const content = `<span class="text-hide-complete" data-complete="1" data-text="${title}"></span><span style="vertical-align: unset;">${completeText}</span>`;
                $elm.html(content);

                const $wrapper = $elm.parents('.wrap-countdown');
                if (isMeta) {
                    $wrapper.css('background', '#0c7905');
                } else {
                    $wrapper.css({ 'color': '#0c7905', 'font-weight': '600' });
                }
            }

            if (config.metaAttr.auto_direct === '1' || config.autoDirect) {
                navigate(url, title, url, isMeta);
            }

            countdownStatus[url] = { active: false };
        }

        function startCountdown($elm, url, title, isMeta) {
            let timeleft = isMeta ? parseInt(config.metaAttr.time) : config.timeConfig;

            const countdown = () => {
                $elm.html(`<strong>${config.waitText} ${timeleft}s...</strong>`);
                timeleft--;

                if (timeleft < 0) {
                    updateCompleteState($elm, title, url, isMeta, false);
                } else {
                    setTimeout(countdown, 1000);
                }
            };

            countdown();
        }

        function startProgress($elm, url, title, isMeta) {
            const $progress = $elm.find('.post-progress');
            const progressWidth = $progress.width();
            const $parent = $elm.parent('.post-progress-bar');
            const timeleft = isMeta ? parseInt(config.metaAttr.time) : config.timeConfig;

            let currentWidth = 0;

            $parent.css({ 'width': $parent.width(), 'margin-right': '25px' });
            $progress.width('0%');

            if (!$progress.hasClass('meta-link')) {
                $progress.css({
                    'background-color': '#1479B3',
                    'color': '#fff',
                    'padding': '0 10px'
                });
            }

            const intervalId = setInterval(() => {
                currentWidth += timeleft <= 3
                    ? progressWidth / 100
                    : progressWidth / (timeleft * 1000 / timeleft);

                $progress.width(currentWidth);

                if (currentWidth >= progressWidth) {
                    clearInterval(intervalId);
                    updateCompleteState($elm, title, url, isMeta, true);
                }
            }, timeleft);
        }

        function processClick() {
            $(document).on('click', '.prep-request', function (e) {
                e.preventDefault();

                const $this = $(this);
                const title = $this.attr('data-text') || $this.text().trim() || '>> Redirect Link <<';
                const modifiedUrl = $this.attr('data-request');
                const url = UrlManager.restore(modifiedUrl);
                const complete = $this.find('.text-hide-complete').data('complete');
                const isImage = $this.attr('data-image');
                const isMeta = $this.attr('data-meta') ? parseInt($this.attr('data-meta')) : 0;

                if (!modifiedUrl || !url) return;

                let startTime = config.timeConfig;

                if (isMeta) {
                    startTime = parseInt(config.metaAttr.time);
                    if (config.metaAttr.auto_direct === '1' && startTime === 0) {
                        startTime = 0;
                    }
                }

                if (complete === 1) {
                    const completeTitle = $this.find('.text-hide-complete').data('text');
                    navigate(url, completeTitle, modifiedUrl, isMeta);
                    return;
                }

                if (countdownStatus[modifiedUrl]?.active) return;

                if (startTime === 0 || isImage === '1') {
                    navigate(url, title, modifiedUrl, isMeta);
                } else {
                    $this.off('click');
                    countdownStatus[modifiedUrl] = { active: true };

                    if (config.displayMode === 'wait_time') {
                        startCountdown($this, modifiedUrl, title, isMeta);
                    } else {
                        startProgress($this, modifiedUrl, title, isMeta);
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