(function ($) {
    'use strict';

    class FAQ {
        constructor(enabled, input, textarea) {
            this.enabled = enabled;
            this.input = input;
            this.textarea = textarea;
            this.related  = input;
        }

        toggleField() {
            if (this.enabled.val() == 1) {
                this.input.show();
                this.textarea.show();
                this.input.find('input').attr('required', 'required');
                this.textarea.find('textarea').attr('required', 'required');
                this.related.find('input').attr('required', 'required');
            } else {
                this.input.hide();
                this.textarea.hide();
                this.input.find('input').removeAttr('required');
                this.textarea.find('textarea').removeAttr('required');
                this.related.find('input').removeAttr('required');
            }
        }
    }

    $(function () {
        const faq1    = new FAQ($('#preplink_faq1_enabled'), $('.preplink_faq1_title'), $('.preplink_faq1_description'));
        const faq2    = new FAQ($('#preplink_faq2_enabled'), $('.preplink_faq2_title'), $('.preplink_faq2_description'));
        const related = new FAQ($('#preplink_related_enabled'), $('.preplink_related_number'), $('.preplink_related_description'));

        faq1.toggleField();
        faq2.toggleField();
        related.toggleField();

        faq1.enabled.on('change', () => faq1.toggleField());
        faq2.enabled.on('change', () => faq2.toggleField());
        related.enabled.on('change', () => related.toggleField());
    });
})(jQuery);
