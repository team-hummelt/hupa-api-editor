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

        send_xhr_hupa_api_edit_form_data();
        function send_xhr_hupa_api_edit_form_data() {

            $.post(api_editor_ajax_obj.ajax_url, {
                    'action': 'HupaApiEditorNoAdmin',
                    '_ajax_nonce': api_editor_ajax_obj.nonce,
                    'method': 'get_api_sections',
                    'is_page': api_editor_ajax_obj.is_page,
                    'post_id':api_editor_ajax_obj.post_id
                },
                function (data) {
                    if (data.status) {
                        $.each(data.record, function (key, val) {
                            console.log(val.type)
                            switch (val.type){
                                case 'textarea':
                                case 'text':
                                    apiEditorJsTextarea(val);
                                    break;
                                case'inline':
                                    apiEditorJsInline(val);
                                    break;
                            }
                        });
                    }
                });
        }

        let lang = api_editor_ajax_obj.language;

      //  let entryContent = $('.entry-content p');


      /*  $('.entry-title').editable(function (value, settings) {
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
        });*/


        function apiEditorJsTextarea(data= null){
            let content = $('.'+data.css_selector);
            let contentHtml = content.html();
            content.html($.trim(contentHtml));
            content.editable(function (value, settings) {
                hupaApiInlineEditorSend(data.content_type, value);
                return value;
            }, {
                type: data.input_type,
                cancel: data.cancel,
                submit: data.save,
                tooltip: data.tooltip,
                inputcssclass: data.form_class,
                cancelcssclass: data.cancel_class,
                submitcssclass: data.submit_class,
                indicator: data.indicator,
                cssclass: data.form_wrapper,
                label: data.label,
                height: 'auto',
                onedit: function () {
                    return true;
                },
                onsubmit: function () {

                },
            });
        }

        function apiEditorJsInline(data= null){
            let content = $('.'+data.css_selector).html().trim();
            content.editable(function (value, settings) {
                hupaApiInlineEditorSend(data.content_type, value, 'inline');
                return value;
            }, {
                style  : "inherit",
                tooltip: data.tooltip,

            });
        }

        function hupaApiInlineEditorSend(what, content, input_type = null) {
            let hupaApiEditData = {};
            let type;
            api_editor_ajax_obj.is_page == '1' ? type = 'pages' : type = 'posts';

            hupaApiEditData[what] = content;

            $.ajax({
                url: wpApiSettings.root + wpApiSettings.versionString + type + '/' + api_editor_ajax_obj.post_id,
                method: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                },
                data: hupaApiEditData
            }).done(function (data) {
                console.log(data)
            });
        }


    })(jQuery);


});
