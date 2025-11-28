(function ($) {
    'use strict';

    class PrepLinkSettings {
        constructor() {

            this.elements = {
                waitText: $('#countdown-select'),
                countdownMode: $('.countdown-select'),
                faqEnabled: $('#faq_enabled'),
                faqDescription: $('.faq_description,.faq_title'),
                related: $('#preplink_related_enabled'),
                relatedDescription: $('.preplink_related_number'),
                relatedNum: $('#related_number'),
                replaceText: $('#replace_text'),
                replaceMode: $('.replace_text'),
                enableRewrite: $('#preplink_enable_rewrite'),
                rewriteFields: $('.preplink-rewrite-fields'),
                keyInput: $('#preplink_key_input'),
                ivInput: $('#preplink_iv_input'),
                generateKeyBtn: $('#generate_key_btn'),
                generateIvBtn: $('#generate_iv_btn'),
                cookieTime: $('#cookie_time'),
                submitBtn: $('#submit')
            };

            this.__ = wp.i18n.__;
            this.init();
        }

        init() {
            this.handleCountdownMode();
            this.handleReplaceTextMode();
            this.handleFaqEnabled();
            this.handleRelatedEnabled();
            this.handleEnableRewriteMode();
            this.handleCookieValidation();
            this.handleKeyIvGeneration();
            this.handleKeyIvValidation();
            this.handleFormSubmit();
            this.removeFaqLabel();
        }

        handleCountdownMode() {
            const toggleCountdown = (value) => {
                this.elements.countdownMode.toggle(value === 'wait_time');
            };

            toggleCountdown(this.elements.waitText.val());
            this.elements.waitText.on('change', (e) => toggleCountdown(e.target.value));
        }

        handleReplaceTextMode() {
            const toggleReplaceMode = (value) => {
                this.elements.replaceMode.toggle(value === 'yes');
            };

            toggleReplaceMode(this.elements.replaceText.val());
            this.elements.replaceText.on('change', (e) => toggleReplaceMode(e.target.value));
        }

        handleFaqEnabled() {
            const toggleFaq = (value) => {
                this.elements.faqDescription.toggle(value === '1');
            };

            toggleFaq(this.elements.faqEnabled.val());
            this.elements.faqEnabled.on('change', (e) => toggleFaq(e.target.value));
        }

        handleRelatedEnabled() {
            const toggleRelated = (value) => {
                this.elements.relatedDescription.toggle(value === '1');
            };

            toggleRelated(this.elements.related.val());
            this.elements.related.on('change', (e) => toggleRelated(e.target.value));

            this.elements.relatedNum.on('change', () => {
                const value = parseInt(this.elements.relatedNum.val());

                $('.prep-notice').remove();

                if (value < 1) {
                    const message = this.__('The value must be greater than 0 to show the number of related posts.', 'intelligent-link');
                    this.showNotice(this.elements.relatedNum.parents('.related_number'), message);
                }
            });
        }

        handleEnableRewriteMode() {
            const toggleRewrite = (value) => {
                this.elements.rewriteFields.toggle(value === 'yes');
            };

            toggleRewrite(this.elements.enableRewrite.val());
            this.elements.enableRewrite.on('change', (e) => toggleRewrite(e.target.value));
        }

        handleCookieValidation() {
            this.elements.cookieTime.on('change', () => {
                const value = parseInt(this.elements.cookieTime.val(), 10);

                if (isNaN(value) || value < 1) {
                    this.elements.cookieTime.val(1);

                    const message = this.__('Value cannot be less than 1', 'intelligent-link');
                    this.showNotice(this.elements.cookieTime.parents('td'), message);

                    setTimeout(() => {
                        $('.prep-notice').fadeOut(200, function() { $(this).remove(); });
                    }, 1000);
                }
            });
        }

        handleKeyIvGeneration() {
            this.elements.generateKeyBtn.on('click', () => {
                this.elements.keyInput.val(this.generateRandomString(32));
            });

            this.elements.generateIvBtn.on('click', () => {
                this.elements.ivInput.val(this.generateRandomString(16));
            });
        }
        
        handleKeyIvValidation() {
            const validateInput = ($input, expectedLength, fieldName) => {
                const value = $input.val().trim();
                const $parent = $input.parents('td');

                $parent.find('.prep-notice').remove();

                if (value !== '' && value.length !== expectedLength) {
                    this.showNotice($parent, `${fieldName} phải đúng ${expectedLength} ký tự.`);
                }
            };

            const $keyInput = $('input[name="preplink_setting[key]"]');
            const $ivInput = $('input[name="preplink_setting[iv]"]');

            $keyInput.on('blur input', () => validateInput($keyInput, 32, 'Key'));
            $ivInput.on('blur input', () => validateInput($ivInput, 16, 'IV'));
        }


        handleFormSubmit() {
            this.elements.submitBtn.on('click', () => {
                const errors = $('.prep-notice');

                if (errors.length) {
                    $('html, body').animate({
                        scrollTop: errors.offset().top
                    }, 100);
                    return false;
                }
            });
        }

        removeFaqLabel() {
            const label = $('label[for="preplink_faq"]');
            if (label.length > 0) {
                label.closest('th').remove();
            }
        }

        generateRandomString(length) {
            const characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            let result = '';

            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }

            return result;
        }

        showNotice($parent, message) {
            $('.prep-notice').remove();
            $parent.append(`<p class="prep-notice">${message}</p>`);
        }
    }

    $(function () {
        new PrepLinkSettings();
    });

})(jQuery);