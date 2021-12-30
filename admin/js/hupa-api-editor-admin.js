let collapseElementList = [].slice.call(document.querySelectorAll('.collapse'));
let resetMsgAlert = document.getElementById("reset-msg-alert");
//Ajax Spinner
let ajaxApiEditFormSpinner = document.querySelectorAll(".ajax-status-spinner");

let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
});

/**================================================
 ========== TOGGLE FORMULAR COLLAPSE BTN  ==========
 ===================================================
 */
let btnEditorCollapse = document.querySelectorAll('button.btn-editor-collapse');
if (btnEditorCollapse) {
    let collapseNode = Array.prototype.slice.call(btnEditorCollapse, 0);
    collapseNode.forEach(function (collapseNode) {
        collapseNode.addEventListener("click", function (e) {
            if (ajaxApiEditFormSpinner) {
                let spinnerNodes = Array.prototype.slice.call(ajaxApiEditFormSpinner, 0);
                spinnerNodes.forEach(function (spinnerNodes) {
                    spinnerNodes.innerHTML = '';
                });
            }
            this.blur();
            if (this.classList.contains("active")) return false;
            let siteTitle = document.getElementById("currentSideTitle");
            let dataSite = this.getAttribute('data-site');
            let dataLoad = this.getAttribute('data-load');
            switch (dataLoad) {
                case 'visibility':

                    break;
                case'start':
                    get_data_css_sections();
                    break;
            }
            siteTitle.innerText = dataSite;
            remove_active_api_editor_btn();
            this.classList.add('active');
            this.setAttribute('disabled', true);
        });
    });


    function remove_active_api_editor_btn() {
        for (let i = 0; i < collapseNode.length; i++) {
            collapseNode[i].classList.remove('active');
            collapseNode[i].removeAttribute('disabled');
        }
    }
}

let hupaApiEditSendFormTimeout;
let hupaApiEditSendFormular = document.querySelectorAll(".sendAjaxEditApiForm:not([type='button'])");
if (hupaApiEditSendFormular) {
    let formNodes = Array.prototype.slice.call(hupaApiEditSendFormular, 0);
    formNodes.forEach(function (formNodes) {
        formNodes.addEventListener("keyup", form_api_edit_input_ajax_handle, {passive: true});
        formNodes.addEventListener('touchstart', form_api_edit_input_ajax_handle, {passive: true});
        formNodes.addEventListener('change', form_api_edit_input_ajax_handle, {passive: true});

        function form_api_edit_input_ajax_handle(e) {
            let spinner = Array.prototype.slice.call(ajaxApiEditFormSpinner, 0);
            spinner.forEach(function (spinner) {
                spinner.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp; Saving...';
            });
            clearTimeout(hupaApiEditSendFormTimeout);
            hupaApiEditSendFormTimeout = setTimeout(function () {
                send_xhr_hupa_api_edit_form_data(formNodes);
            }, 1000);
        }
    });
}

function send_xhr_hupa_api_edit_form_data(data, is_formular = true) {

    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    xhr.open('POST', api_editor_ajax_obj.ajax_url, true);

    if (is_formular) {
        let input = new FormData(data);
        for (let [name, value] of input) {
            formData.append(name, value);
        }
    } else {
        for (let [name, value] of Object.entries(data)) {
            formData.append(name, value);
        }
    }

    formData.append('_ajax_nonce', api_editor_ajax_obj.nonce);
    formData.append('action', 'HupaApiEditorHandle');
    xhr.send(formData);

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let data = JSON.parse(this.responseText);
            if (data.spinner) {
                show_api_edit_ajax_spinner(data);
                return false;
            }

            if (data.status) {
                let selContainer = document.getElementById('apiEditSections');
                let html = '';

                switch (data.type) {
                    case'load_data':
                        for (const [key, val] of Object.entries(data.record)) {
                            html += render_css_sections_template(val, data.select);
                            selContainer.innerHTML = html;
                        }
                        break;
                    case 'add_data':
                        html = render_css_sections_template(false, data.select);
                        selContainer.insertAdjacentHTML('beforeend', html);
                        break;
                    case 'insert':
                        let form = document.querySelector('.form' + data.rand);
                        let inputType = form.querySelector('.input_type')
                        let inputId = form.querySelector('.input_id');
                        let btnAdd = form.querySelector('button.btn-add');
                        let btnEdit = form.querySelector('button.btn-edit');
                        btnAdd.classList.add('d-none');
                        btnEdit.classList.remove('d-none');
                        inputType.value = 'update';
                        inputId.value = data.id;
                        if (data.status) {
                            success_message(data.msg);
                        } else {
                            warning_message(data.msg);
                        }
                        break;
                    case'update':
                        if (data.status) {
                            success_message(data.msg);
                        } else {
                            warning_message(data.msg);
                        }
                        break;
                    case'delete_section':
                        if (data.status) {
                            let formWrapper = document.getElementById('select' + data.rand);
                            formWrapper.remove();
                            success_message(data.msg);
                        } else {
                            warning_message(data.msg);
                        }
                        break;
                    case'reset_options_settings':
                        if (data.status) {
                            let resetWrapper = document.querySelectorAll('.api-reset-wrapper');
                            resetWrapper[0].classList.add('d-none');
                            resetWrapper[1].classList.remove('d-none');
                            success_message(data.msg);
                        } else {
                            warning_message(data.msg);
                        }
                        break;
                }
            } else {
                if (data.msg)
                    warning_message(data.msg);
            }
        }
    }
}

/**===========================================
 ========== ADD SEKTION TEMPLATE  ==========
 =============================================
 */

let addSectionBtn = document.getElementById('add_css_section');
if (addSectionBtn) {
    addSectionBtn.addEventListener("click", function (e) {
        const data = {
            'method': 'get_css_section_data',
            'type': 'add_data'
        }
        send_xhr_hupa_api_edit_form_data(data, false);
    });
}

/**===========================================
 ========== CSS SEKTIONEN TEMPLATE  ==========
 =============================================
 */
function get_data_css_sections() {
    const data = {
        'method': 'get_css_section_data',
        'type': 'load_data'
    }
    send_xhr_hupa_api_edit_form_data(data, false);
}

function render_css_sections_template(data = false, selected) {
    let rand = EditApiRandomCode(6);
    let l = selected.lang;


    let html = `<div id="select${rand}" class="p-3 border rounded bg-light shadow-sm mb-4">
    <form class="form${rand}">
    <fieldset class="section_field ${data && data.pages_aktiv || data && data.posts_aktiv ? 'active' : selected.page || selected.post ? 'active' : 'deactivated'}">
    <input type="hidden" name="method" value="css_sections_db_handle">
    <input class="input_type" type="hidden" name="type" value="${data && data.id ? 'update' : 'insert'}">
    <input class="input_id" type="hidden" name="id" value="${data && data.id ? data.id : ''}">
    <input class="input_rand" type="hidden" name="rand" value="${rand}">
    <div class="input-group">
    <div class="col-4 col-xxl-2 pe-xxl-2 py-xxl-0 py-1 pe-0 col-12  ms-0">
    <div class="input-group-text">
    <label for="pageCheck${rand}"
    class="small me-2" style="min-width: 3rem;text-align: left; flex:1 1 auto">${l.pages}</label>
    <input data-rand="${rand}" data-type="page" name="page_aktiv"
    id="pageCheck${rand}" class="checkSelectSektion form-check-input py-2 mt-0"
    type="checkbox" ${data && data.pages_aktiv ? 'checked' : !data && selected.page ? 'checked' : ''} ${data && data.pages_disabled ? ' disabled' : selected.page ? '' : 'disabled'}>
    </div>
    </div>
    <div class="col-4 col-xxl-2  pe-0 py-xxl-0 py-1 col-12 ms-0">
    <div class="input-group-text">
    <label for="postCheck${rand}"
    class="small me-2" style="min-width: 3rem;text-align: left; flex:1 1 auto">${l.posts}</label>
    <input  data-rand="${rand}" data-type="post" name="post_aktiv" 
    id="postCheck${rand}" class="checkSelectSektion form-check-input py-2 mt-0"
    type="checkbox" ${data && data.posts_aktiv ? 'checked' : !data && selected.post ? 'checked' : ''} ${data && data.posts_disabled ? ' disabled' : selected.post ? '' : 'disabled'}>
    </div>
    </div>
    <div class="w-100 d-flex flex-wrap mt-2">
    <div class="col-xxl-4 col-xl-5 col-12">
    <select onchange="this.blur()" class="form-control mb-2 me-xxl-2 me-0" name="select_type"
    style="max-width: 100%">`;
    if (selected) {
        for (const [key, val] of Object.entries(selected.select)) {
            let sel = '';
            data && data.input_type == val.id ? sel = 'selected' : sel = '';
            html += `<option value="${val.id}" ${sel}>${val.bezeichnung}</option>`;
        }
    }
    html += `</select>
    </div>
    <div class="col-xxl-8 col-xl-7 col-12">
    <input type="text" class="form-control ms-0 ms-xxl-2" name="css_selector"
    placeholder="${l.section_ph}" value="${data && data.css_class ? data.css_class : ''}">
    </div>
    </div>
    </div>
    </fieldset>
    <button class="btn-api-data btn-add btn btn-blue-outline btn-sm mt-2 ${selected.btn_add ? '' : ' d-none'}"
    type="button">${l.add}</button>
    <button class="btn-api-data btn-edit btn btn-blue-outline btn-sm mt-2 ${selected.btn_change ? '' : ' d-none'}"
    type="button">${l.save_changes}</button> 
    <button class="btn-delete-section btn btn-outline-danger btn-sm mt-2 ${selected.btn_delete ? '' : ' d-none'}" 
    type="button">
    ${l.delete}</button>
  
    </form>
    </div>`;
    return html;
}

/**======================================
 ========== AJAX SPINNER SHOW  ==========
 ========================================
 */
function show_api_edit_ajax_spinner(data) {
    let msg = '';
    if (data.status) {
        msg = '<i class="text-success fa fa-check"></i>&nbsp; Saved! Last: ' + data.msg;
    } else {
        msg = '<i class="text-danger fa fa-exclamation-triangle"></i>&nbsp; ' + data.msg;
    }
    let spinner = Array.prototype.slice.call(ajaxApiEditFormSpinner, 0);
    spinner.forEach(function (spinner) {
        spinner.innerHTML = msg;
    });
}


document.addEventListener("DOMContentLoaded", function (event) {
    (function ($) {
        'use strict';

        $(document).on('click', '.form-check-input, .btn', function () {
            $(this).trigger('blur')
        });

        $(document).on('click', '.checkSelectSektion', function () {
            let type = $(this).attr('data-type');
            let rand = $(this).attr('data-rand');
            let form = $(this).closest("form");
            let btnAddEdit = $('.btn-api-data ', form);
            let fieldSet = $('fieldset ', form);
            let postCheck = $('#postCheck' + rand);
            let pageCheck = $('#pageCheck' + rand);

            switch (type) {
                case 'post':
                    if ($(this).prop('checked')) {
                        fieldSet.removeClass('deactivated').addClass('active');
                        btnAddEdit.prop('disabled', false);
                    } else {
                        if (!pageCheck.prop('checked')) {
                            fieldSet.addClass('deactivated').removeClass('active');
                            btnAddEdit.prop('disabled', true);
                        }
                    }
                    break;
                case 'page':
                    if ($(this).prop('checked')) {
                        fieldSet.removeClass('deactivated').addClass('active');
                        btnAddEdit.prop('disabled', false);
                    } else {
                        if (!postCheck.prop('checked')) {
                            fieldSet.addClass('deactivated').removeClass('active');
                            btnAddEdit.prop('disabled', true);
                        }
                    }
                    break;
            }

        });


        $(document).on('click', '.btn-api-data', function () {
            let form = $(this).closest("form").get(0);
            send_xhr_hupa_api_edit_form_data(form);
        });

        $(document).on('click', '.btn-delete-section', function () {
            let form = $(this).closest("form");
            let rand = $('.input_rand ', form).val();
            let type = $('.input_type ', form).val();
            let delBtn = document.querySelector('#deleteApiEditModal .btn-delete-sections');
            let delTxt = document.querySelector('.modal-delete-msg');
            let msg = '';
            if(!type){
                type = $(this).attr('data-type');
            }
            switch (type) {
                case 'insert':
                    let sectionCont = $('#select' + rand);
                    sectionCont.remove();
                    return false;
                case 'update':
                    let id = $('.input_id ', form).val();
                    delBtn.setAttribute('data-id', id);
                    delTxt.innerHTML = 'CSS Selector löschen?'
                    delBtn.setAttribute('data-method', 'delete_css_sections');
                    delBtn.setAttribute('data-rand', rand);
                    delBtn.setAttribute('data-type', 'update');
                    msg = 'CSS Selector unwiderruflich <b class="text-alert"> löschen</b>';
                    break;
                case 'reset_settings':
                    delBtn.setAttribute('data-type', 'reset');
                    msg = 'Einstellungen unwiderruflich <b class="text-alert"> zurücksetzen</b>';
                    delTxt.innerHTML = 'Einstellungen zurücksetzen'
                    break;

            }
            let deleteModal = new bootstrap.Modal(document.getElementById('deleteApiEditModal'), {
                keyboard: false,
                focus: true
            });


            let delTxtBody = document.querySelector('#deleteApiEditModal  .modal-body');
            delTxtBody.innerHTML = `<h5 class="text-center">${msg}?
                                            <small class="d-block fw-normal mt-1">Die Aktion <b class="text-alert"> kann nicht rückgängig</b> gemacht werden!</small> </h5>`;
            deleteModal.toggle();

        });

        $(document).on('click', '.btn-delete-sections', function () {

            let type = $(this).attr('data-type');
            let data;
            switch (type){
                case 'update':
                     data = {
                        'method': $(this).attr('data-method'),
                        'id': $(this).attr('data-id'),
                        'rand': $(this).attr('data-rand'),
                    }
                    send_xhr_hupa_api_edit_form_data(data, false);
                    break;
                case'reset':
                    data = {
                        'method': 'reset_options_settings',
                    }
                    send_xhr_hupa_api_edit_form_data(data, false);
                    break;
            }

        });

    })(jQuery);

    get_data_css_sections();


});


function warning_message(msg) {
    let x = document.getElementById("snackbar-warning");
    x.innerHTML = msg;
    x.className = "show";
    setTimeout(function () {
        x.className = x.className.replace("show", "");
    }, 5000);
}

function success_message(msg) {
    let x = document.getElementById("snackbar-success");
    x.innerHTML = msg;
    x.className = "show";
    setTimeout(function () {
        x.className = x.className.replace("show", "");
    }, 5000);
}

/*=====================================
========== HELPER RANDOM KEY ==========
=======================================
*/
function EditApiRandomCode(length) {
    let randomCodes = '';
    let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        randomCodes += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return randomCodes;
}