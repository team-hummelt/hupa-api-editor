document.addEventListener("DOMContentLoaded", function (event) {
    (function ($) {
        'use strict';

        let lang = api_editor_ajax_obj.language;
        /**
         * LOAD ALL DATA
         * @since 1.0.0
         */
        get_xhr_hupa_api_rest_api_data(hupa_api_data_callback);

        /**
         *
         * Get Hupa-Api EDITORJS
         * @param callback
         * @param data
         * @since v.1.0.0
         */
        function get_xhr_hupa_api_rest_api_data(callback, data = null) {
            let xhr = new XMLHttpRequest();
            let formData = new FormData();
            if (data) {
                let input = new FormData(data);
                for (let [name, value] of input) {
                    formData.append(name, value);
                }
            }

            formData.append('method', 'get_api_sections');
            formData.append('action', 'HupaApiEditorNoAdmin');
            formData.append('_ajax_nonce', api_editor_ajax_obj.nonce);
            formData.append('is_page', api_editor_ajax_obj.is_page);
            formData.append('post_id', api_editor_ajax_obj.post_id);
            xhr.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    if (typeof callback === 'function') {
                        xhr.addEventListener("load", callback);
                    }
                }
            }

            xhr.open('POST', api_editor_ajax_obj.ajax_url, true);
            xhr.send(formData);
        }

        /**
         * @since 1.0.0
         * Load All Sections CALLBACK
         */
        function hupa_api_data_callback() {
            let data = JSON.parse(this.responseText);
            if (data.status) {
                let postId;
                for (const [key, val] of Object.entries(data.record)) {
                    let selectorClass = $('.' + val.css_selector);
                     let selectedCssClass = document.querySelectorAll('.' + val.css_selector);
                     if (selectedCssClass && val.symbol) {
                         let symbolNode = Array.prototype.slice.call(selectedCssClass, 0);
                         symbolNode.forEach(function (symbolNode) {
                             if (val.symbol) {
                                 symbolNode.classList.add('api-edit-select');
                             }
                         });
                     }

                    switch (val.type) {
                        case 'textarea':
                        case 'text':
                            for (let i = 0; i < selectorClass.length; i++) {
                                if ($(selectorClass[i]).attr('data-id')) {
                                    postId = $(selectorClass[i]).attr('data-id');
                                } else {
                                    postId = api_editor_ajax_obj.post_id;
                                }
                                apiEditorJsTextarea(val, postId, selectorClass[i]);
                            }
                            break;
                        case 'widget-title':
                        case 'widget-text':
                        case 'widget-content':
                            for (let i = 0; i < selectorClass.length; i++) {
                                apiEditorJsWidget(val, selectorClass[i]);
                            }
                            break;
                        case'inline':

                            break;
                    }
                }
            }
        }

        /**
         *
         * HUPA API Editor Type(s) -> Text|Textarea
         * @param data
         * @param id
         * @param contentData
         * @since 1.0.0
         */
        function apiEditorJsTextarea(data, id, contentData) {
            let content = '';
            let contentHtml = '';

            let sectionType = data.section_type;
            content = $(contentData)
            contentHtml = content.html();
            content.html($.trim(contentHtml));

            content.editable(function (value, settings) {
                let formData = new FormData();
                let setData;

                formData.append(""+data.section_type+"", value);
                setData = {
                    'type': '',
                    'uri': '/' + id,
                }
                if(!data.section_type == 'title'){
                    set_xhr_hupa_api_rest_data(formData, setData, postDataCallback);
                }

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
                    let getData = '';
                    let formData = new FormData();
                    let setData;

                    switch (sectionType) {
                        case 'title':
                            getData = {
                                'get': 'hupa-meta/' + id + '/hupa_custom_title'
                            }

                            get_xhr_hupa_api_rest_data(getData, validateTitleMeta);

                        function validateTitleMeta() {
                            let data = JSON.parse(this.responseText);

                            if (data.value && data.meta) {
                                formData.append('value', content.html());
                                setData = {
                                    'type': 'post_meta',
                                    'uri': 'hupa-meta/' + id + '/hupa_custom_title',
                                }
                            } else {
                                formData.append('title', content.html());
                                setData = {
                                    'type': '',
                                    'uri': '/' + id,
                                }
                            }
                            set_xhr_hupa_api_rest_data(formData, setData, metaDataCallback);
                        }
                       break;
                    }
                },
            });
        }

        /**
         *
         * HUPA API Editor WIDGET Title|Text|Content
         * @param data
         * @param contentData
         * @since 1.0.0
         */
        function apiEditorJsWidget(data, contentData){
            let content = '';
            let contentHtml = '';
            let sectionType = data.section_type;
            content = $(contentData)
            contentHtml = content.html();
            content.html($.trim(contentHtml));

            content.editable(function (value, settings) {

                let formData = new FormData();
                let setData;
              //  formData.append('excerpt', value);
                setData = {
                    'type': '',
                    'uri': '/' + id,
                }

               // set_xhr_hupa_api_rest_data(formData, setData, postDataCallback);
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
                    let getData = '';
                    let formData = new FormData();
                    let setData;



                },
            });
        }

        /**
         *
         * GET WP-REST-API CONTENT INPUT
         * @param loadData
         * @param callback
         * @since 1.0.0
         */
        function get_xhr_hupa_api_rest_data(loadData, callback) {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', wpApiSettings.root + wpApiSettings.versionString + loadData.get, true);
            xhr.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    if (typeof callback === 'function') {
                        xhr.addEventListener("load", callback);
                    }
                }
            }
            xhr.send();
        }


        /**
         *
         * POST WP-REST-API CONTENT INPUT
         * @param formData
         * @param setData
         * @param callback
         * @since 1.0.0
         */
        function set_xhr_hupa_api_rest_data(formData, setData, callback = NULL) {
            let xhr = new XMLHttpRequest();
            let type = '';
            api_editor_ajax_obj.is_page == '1' ? type = 'pages' : type = 'posts';

            switch (setData.type) {
                case 'post_meta':
                    type = '';
                    break;
            }

            let url = wpApiSettings.root + wpApiSettings.versionString + type + setData.uri;

            xhr.open('POST', url, true);
            xhr.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    if (typeof callback === 'function') {
                        xhr.addEventListener("load", callback);
                    }
                }
            }
            xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            xhr.send(formData);
        }


        /**
         * @since 1.0.0
         * WP-API-REST META-DATA CALLBACK
         */
        function metaDataCallback() {
            let data = JSON.parse(this.responseText);
            //console.log(data);
        }

        /**
         * @since 1.0.0
         * WP-API-REST POST CALLBACK
         */
        function postDataCallback() {
            let data = JSON.parse(this.responseText);
            //console.log(data);
        }


        if (!String.prototype.trim) {
            String.prototype.trim = function () {
                return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
            };
        }

    })(jQuery);
});
