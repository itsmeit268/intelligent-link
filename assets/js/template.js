const PrepLink = (($) => {
    'use strict';

    const cfg = {
        get time()       { return parseInt(prep_template.countdown_endpoint); },
        get autoDirect() { return parseInt(prep_template.endpoint_direct); },
        get rewrite()    { return prep_template.enable_rewrite; },
        get ajaxUrl()    { return prep_template.ajax_url; },
        get txtContinue(){ return prep_template.txt_continue; },
        get txtValidate(){ return prep_template.txt_validating; },
    };

    const sel = {
        progress:   () => $('#endpoint-progress'),
        counter:    () => $('.counter'),
        bar:        () => $('.bar'),
        t2Timer:    () => $('#preplink-timer-link'),
        buttonDw:   () => $('#buttondw'),
        dlBtn:      () => $('.prep-btn-download'),
        serverList: () => $('.list-link-redirect,.not-vip-download'),
        serverDl:   () => $('.list-server-download'),
        btnLink:    () => $('.preplink-btn-link,.list-preplink-btn-link'),
        clickable:  () => $('.clickable,.prep-title'),
    };

    const cookie = {
        get: name => {
            const m = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]*)'));
            return m ? m[2] : null;
        },
    };

    const api = {
        post: data => $.ajax({ url: cfg.ajaxUrl, type: 'POST', data }),
    };

    const modal = {
        show: link => {
            const $modal  = $('#ilgl-password-modal');
            const $input  = $('#ilgl-password-input');
            const $submit = $('#ilgl-password-submit');
            const $cancel = $('#ilgl-password-cancel');
            const $error  = $('#ilgl-password-error');

            const reset = () => {
                $submit.prop('disabled', false).text(cfg.txtContinue);
                $input.val('').focus();
            };

            const close = () => {
                $modal.hide();
                $(document).off('keydown.ilgl-modal');
            };

            $input.val('');
            $error.hide();
            $submit.prop('disabled', false).text(cfg.txtContinue);
            $modal.show();
            $input.focus();

            $submit.off('click').on('click', async () => {
                $submit.prop('disabled', true).text(cfg.txtValidate);
                $error.hide();
                try {
                    const res = await api.post({ action: 'validate_password', password: $input.val(), link });
                    if (res.success && res.data?.link) {
                        close();
                        window.location.href = res.data.link;
                    } else {
                        $error.text(res.data?.message || 'Validation failed').show();
                        reset();
                    }
                } catch {
                    $error.text('Server error. Please try again.').show();
                    reset();
                }
            });

            $cancel.off('click').on('click', close);

            $input.off('keypress').on('keypress', e => {
                if (e.which === 13) $submit.trigger('click');
            });

            $(document).off('keydown.ilgl-modal').on('keydown.ilgl-modal', e => {
                if (e.which === 27 && $modal.is(':visible')) close();
            });
        },
    };

    const redirect = {
        go: href => { window.location.href = href; },

        validate: async link => {
            try {
                const res = await api.post({ action: 'validate_password', password: '', link });
                if (res.success && res.data?.link) redirect.go(res.data.link);
                else modal.show(link);
            } catch {
                modal.show(link);
            }
        },

        execute: async link => {
            if (!cfg.rewrite) { redirect.go(link); return; }
            try {
                const res = await api.post({ action: 'handle_direct_link', link });
                if (res.success && res.data?.link) redirect.go(res.data.link);
                else console.error('AJAX failed:', res.data?.message || 'Unknown error');
            } catch (err) {
                console.error('AJAX request failed:', err);
            }
        },

        process: link => cfg.autoDirect === 0 ? redirect.validate(link) : redirect.execute(link),

        autoIfCookie: () => {
            if (!cfg.autoDirect) return;
            const link = cookie.get('prep_request');
            if (link) redirect.process(link);
        },
    };

    const progress = {
        showDownload: () => {
            const $counter = sel.counter();
            $counter.html('');
            sel.dlBtn().appendTo($counter).fadeIn(1000);
            if (sel.serverList().length) {
                sel.serverDl().fadeIn(1000);
                sel.progress().fadeOut(100);
            }
        },

        run: () => {
            if (cfg.time <= 0) return;

            const $progress = sel.progress();
            let running = false;

            $progress.on('click', e => {
                e.preventDefault();
                if (running) return;
                running = true;
                $progress.show();

                const total = cfg.time * 1000;
                const start = Date.now();
                let done = false;

                const tick = setInterval(() => {
                    const remaining = total - (Date.now() - start);
                    if (remaining <= 200) {
                        clearInterval(tick);
                        done = running = false;
                        $progress.off('click');
                        progress.showDownload();
                        redirect.autoIfCookie();
                    } else {
                        const pct = Math.floor((1 - remaining / total) * 100);
                        sel.bar().css('width', pct + '%');
                        sel.counter().html(pct + '%');
                    }
                }, 10);

                setTimeout(() => clearInterval(tick), total);

                sel.counter().on('click', e => {
                    if (!done) { e.preventDefault(); return; }
                    const link = $(e.currentTarget).data('request');
                    if (link) redirect.process(link);
                });
            });
        },

        t2Countdown: () => {
            const $timer = sel.t2Timer();
            if (!$timer.length) return;

            const tick = sec => {
                if (--sec > 0) {
                    $timer.html(sec);
                    setTimeout(() => tick(sec), 1200);
                } else {
                    sel.buttonDw().addClass('del-timer');
                    redirect.autoIfCookie();
                }
            };

            tick(parseInt($timer.attr('data-time')));
        },
    };

    const bindings = {
        linkButtons: () => {
            sel.btnLink().on('click', e => {
                e.preventDefault();
                const link = $(e.currentTarget).data('request');
                if (link) redirect.process(link);
            });
        },

        scrollToProgress: () => {
            sel.clickable().on('click', () => {
                if (cfg.time === 0) {
                    const link = cookie.get('prep_request');
                    if (link) redirect.process(link);
                    return;
                }
                sel.progress().trigger('click');
                $('html, body').animate({ scrollTop: sel.progress().offset().top - 150 }, 100);
            });
        },
    };

    const init = () => {
        progress.run();
        progress.t2Countdown();
        bindings.linkButtons();
        bindings.scrollToProgress();
    };

    return { init };

})(jQuery);

jQuery(PrepLink.init);