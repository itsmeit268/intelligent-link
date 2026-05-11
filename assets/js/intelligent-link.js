const HrefVars = (($) => {
    'use strict';

    const cfg = {
        get endPoint()    { return href_vars.end_point.trim(); },
        get currentUrl()  { return window.location.href.replace(/#.*/, ''); },
        get time()        { return parseInt(href_vars.count_down); },
        get cookieTime()  { return parseInt(href_vars.cookie_time); },
        get waitText()    { return href_vars.wait_text.trim(); },
        get displayMode() { return href_vars.display_mode; },
        get autoDirect()  { return parseInt(href_vars.auto_direct); },
        get textComplete(){ return href_vars.replace_text; },
        get winWidth()    { return $(window).width(); },
        get rewrite()     { return href_vars.enable_rewrite || false; },
        get metaAttr()    { return href_vars.meta_attr || {}; },
    };

    const countdownStatus = {};

    const cookie = {
        clear: name => {
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        },
        set: (name, value) => {
            const expires = new Date(Date.now() + cfg.cookieTime * 60 * 1000).toUTCString();
            cookie.clear(name);
            document.cookie = `${name}=${value}; expires=${expires}; path=/`;
        },
        clearAll: () => ['prep_meta', 'prep_request', 'prep_title'].forEach(cookie.clear),
    };

    const url = {
        buildTarget: () => {
            let base = cfg.currentUrl.split('?')[0];
            if (base.includes('.html')) base = base.match(/.*\.html/)[0] + '/';
            else if (!base.endsWith('/')) base += '/';
            return `${base}?${cfg.endPoint}=1`;
        },
    };

    const nav = {
        go: (href, title, isMeta) => {
            cookie.set('prep_title', title);
            cookie.set('prep_request', href);
            cookie.set('prep_meta', isMeta);
            const target = url.buildTarget();
            cfg.winWidth > 700 ? window.open(target, '_blank') : (window.location.href = target);
        },
    };

    const complete = {
        text: title => cfg.textComplete.enable === 'yes' ? cfg.textComplete.text : title,

        apply: ($elm, title, href, isMeta, isProgress = false) => {
            const label = complete.text(title);

            if (isProgress) {
                $elm.addClass('completed').attr('data-original-text', title);
                $elm.find('.post-progress')
                    .html(`<span class="text-complete">${label}</span>`)
                    .removeAttr('style');
            } else {
                $elm.html(`<strong class="link-countdown"><span class="text-complete">${label}</span></strong>`);
                const $wrap = $elm.parents('.wrap-countdown');
                isMeta
                    ? $wrap.css('background', '#0c7905')
                    : $wrap.css({ color: '#0c7905', 'font-weight': '600' });
            }

            if (cfg.metaAttr.auto_direct === '1' || cfg.autoDirect) {
                nav.go(href, title, isMeta);
            }

            countdownStatus[href] = { active: false };
        },
    };

    const countdown = {
        resolveTime: isMeta => isMeta && cfg.metaAttr ? parseInt(cfg.metaAttr.time) : cfg.time,

        text: ($elm, href, title, isMeta) => {
            let left = countdown.resolveTime(isMeta);

            const tick = () => {
                $elm.html(`<strong class="post-progress">${cfg.waitText} ${left}s...</strong>`);
                left--;
                if (left < 0) {
                    $elm.addClass('completed').attr('data-original-text', title);
                    complete.apply($elm, title, href, isMeta, false);
                } else {
                    setTimeout(tick, 1000);
                }
            };

            tick();
        },

        progress: ($elm, href, title, isMeta) => {
            const total = countdown.resolveTime(isMeta) * 1000;
            const start = Date.now();

            $elm.addClass('loading').css('--progress', '0%');

            const frame = () => {
                const pct = Math.min(((Date.now() - start) / total) * 100, 100);
                $elm.css('--progress', pct + '%');
                pct < 100 ? requestAnimationFrame(frame) : complete.apply($elm, title, href, isMeta, true);
            };

            frame();
        },
    };

    const bindings = {
        processClick: () => {
            $(document).on('click', '.prep-request', e => {
                e.preventDefault();

                const $elm   = $(e.currentTarget);
                const href   = $elm.attr('data-request');
                if (!href) return;

                const title  = $elm.attr('data-text') || $elm.text().trim() || '>> Redirect Link <<';
                const isMeta = $elm.attr('data-meta') ? parseInt($elm.attr('data-meta')) : 0;
                const isImg  = $elm.attr('data-image');

                if ($elm.hasClass('completed')) { nav.go(href, title, isMeta); return; }
                if (countdownStatus[href]?.active) return;

                let startTime = isMeta && cfg.metaAttr ? parseInt(cfg.metaAttr.time) : cfg.time;
                if (isMeta && cfg.metaAttr?.auto_direct === '1') startTime = startTime || 0;

                if (startTime === 0 || isImg === '1') {
                    nav.go(href, title, isMeta);
                } else {
                    $elm.off('click');
                    countdownStatus[href] = { active: true };
                    cfg.displayMode === 'wait_time'
                        ? countdown.text($elm, href, title, isMeta)
                        : countdown.progress($elm, href, title, isMeta);
                }
            });
        },
    };

    const init = () => {
        bindings.processClick();
    };

    return { init };
})(jQuery);

jQuery(HrefVars.init);