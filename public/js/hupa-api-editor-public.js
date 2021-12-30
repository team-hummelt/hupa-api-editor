document.addEventListener("DOMContentLoaded", function (event) {
    (function ($) {
        'use strict';

        /**
         * All of the code for your public-facing JavaScript source
         * should reside in this file.
         *
         * Note: It has been assumed you will write jQuery code here, so the
         * $ function reference has been prepared for usage within the scope
         * of this function.
         *
         * This enables you to define handlers, for when the DOM is ready:
         *
         * $(function() {
         *
         * });
         *
         * When the window is loaded:
         *
         * $( window ).load(function() {
         *
         * });
         *
         * ...and/or other possibilities.
         *
         * Ideally, it is not considered best practise to attach more than a
         * single DOM-ready or window-load handler for a particular page.
         * Although scripts in the WordPress core, Plugins and Themes may be
         * practising this, we should strive to set a better example in our own work.
         */

        let lang = api_editor_ajax_obj.language;

        let entryContent = $('.entry-content p');

        $('.entry-title').editable(function (value, settings) {
            hupaApiInlineEditorSend('title', value);
            return value;
        }, {
            indicator: lang.saving,
            type: "text",
            cancel: lang.cancel,
            submit: lang.save,
            tooltip: lang.tooltip,
            inputcssclass: 'form-control w-100',
            cssclass: 'api-edit-wrapper',
            cancelcssclass: 'btn btn-outline-danger btn-sm mt-2 me-1',
            submitcssclass: 'btn btn-outline-success btn-sm mt-2 me-1',
            label: lang.edit_title,
        });

        entryContent.editable(function (value, settings) {
            hupaApiInlineEditorSend('content', value);
            return value;
        }, {
            //type: 'textarea',
            type: "autogrow",
            cancel: lang.cancel,
            submit: lang.save,
            tooltip: lang.tooltip,
            inputcssclass: 'form-control w-100',
            cancelcssclass: 'btn btn-outline-danger btn-sm mt-2 me-1',
            submitcssclass: 'btn btn-outline-success btn-sm mt-2 me-1',
            indicator: lang.saving,
            cssclass: 'api-edit-wrapper',
            label: lang.edit_txt,
            height: 'auto',
            onedit: function () {
                return true;
            },
            onsubmit: function () {

            },

        });

        function hupaApiInlineEditorSend(what, content) {
            let hupaApiEditData = {};
            hupaApiEditData[what] = content;
            let type;
            api_editor_ajax_obj.is_page == '1' ? type = 'pages' : type = 'posts';
            $.ajax({
                url: wpApiSettings.root + wpApiSettings.versionString + type + '/' + api_editor_ajax_obj.post_id,
                method: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                },
                data: hupaApiEditData
            }).done(function (data) {
               // console.log(data)
            });
        }
    })(jQuery);


});
