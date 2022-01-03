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
            let btnHide = document.querySelector('.btn-admin-hide');
            if (btnHide) {
                btnHide.classList.add('d-none');
            }
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

function send_xhr_hupa_api_edit_form_data(data, is_formular = true, callback = null) {

    let xhr = new XMLHttpRequest();
    let formData = new FormData();

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

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            if (typeof callback === 'function') {
                xhr.addEventListener("load", callback);
                return false;
            }

            let data = JSON.parse(this.responseText);
            if (data.spinner) {
                show_api_edit_ajax_spinner(data);
                // return false;
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
                        selContainer.insertAdjacentHTML('afterbegin', html);
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
                    case 'admin_option':
                        let optionShowWrapper = document.querySelectorAll('.hupa-edit-option');
                        if (optionShowWrapper) {
                            let optNode = Array.prototype.slice.call(optionShowWrapper, 0);
                            optNode.forEach(function (optNode) {
                                if (data.option_page) {
                                    optNode.classList.remove('hupa-hide');
                                } else {
                                    optNode.classList.add('hupa-hide');
                                }
                            });
                        }

                        let collBtn = document.getElementById('btnShowOptions');
                        collBtn.classList.remove('active');
                        collBtn.classList.remove('collapsed');
                        collBtn.removeAttribute('disabled');

                        let adminCollapse = document.getElementById('collapseApiAdmin');
                        let bsCollapse = new bootstrap.Collapse(adminCollapse, {});
                        bsCollapse.toggle();
                        break;
                }
            } else {
                if (data.msg)
                    warning_message(data.msg);
            }
        }
    }
    xhr.open('POST', api_editor_ajax_obj.ajax_url, true);
    xhr.send(formData);
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

    let html = `<div id="select${rand}" class="p-3 border rounded bg-light shadow-sm mb-4">`;
    if (data) {
        html += get_section_header_template(data, rand);
    }

    html += `
    <div class="newItemPlaceholder${rand}"></div>
    <div id="section${rand}" class="section-collapse collapse ${data ? '' : 'show'}">
    <hr>
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
    <div class="col-4 col-xxl-2 mb-3  pe-0 py-xxl-0 py-1 col-12 ms-0">
    <div class="input-group-text">
    <label for="postCheck${rand}"
    class="small me-2" style="min-width: 3rem;text-align: left; flex:1 1 auto">${l.posts}</label>
    <input  data-rand="${rand}" data-type="post" name="post_aktiv" 
    id="postCheck${rand}" class="checkSelectSektion form-check-input py-2 mt-0"
    type="checkbox" ${data && data.posts_aktiv ? 'checked' : !data && selected.post ? 'checked' : ''} ${data && data.posts_disabled ? ' disabled' : selected.post ? '' : 'disabled'}>
    </div>
    </div>
    <div class="w-100 row gx-2 mt-2">
    <div class="col-xxl-4 col-xl-5 col-12">
    <label for="selectContentType${rand}" class="form-label mb-1"> Inhalts Typ</label>
    <select onchange="change_content_type_select(this, '${rand}')" id="selectContentType${rand}" 
    class="form-control mb-3 me-xxl-2 pe-2 me-0" name="content_type"
    style="max-width: 100%">`;
    if (selected) {
        for (const [key, val] of Object.entries(selected.content_types_select)) {
            let sel = '';
            if (val.aktiv == 1) {
                data && data.content_type == val.id ? sel = 'selected' : sel = '';
                html += `<option value="${val.id}" ${sel}>${val.bezeichnung}</option>`;
            }
        }
    }
    html += `</select>
    </div>
    <div class="widget-select${rand} col-xxl-4 col-xl-5 col-12">
       <label for="selectWidget${rand}" class="form-label mb-1"> Widget</label>
    <select id="selectWidget${rand}" 
    class="form-control mb-3 me-xxl-2 pe-2 me-0" name="content_type"
    style="max-width: 100%" disabled>`;

    html +=` </select></div>
    </div>
    
    <div class="w-100 row gx-2 mt-2">
    <div class="col-xxl-4 col-xl-5 col-12">
    <label for="selectFormOutputType${rand}" class="form-label mb-1"> Ausgabe Type</label>
    <select onchange="this.blur()" id="selectFormOutputType${rand}" 
    class="form-control mb-3 me-xxl-2 pe-2 me-0" name="output_type"
    style="max-width: 100%" ${data ? '' : 'disabled'}>`;
    if (data) {
        for (const [key, val] of Object.entries(selected.output_select)) {
            let sel = '';
            if (val.aktiv == 1) {
                data && data.output_type == val.id ? sel = 'selected' : sel = '';
                html += `<option value="${val.id}" ${sel}>${val.bezeichnung}</option>`;
            }
        }
    }
    html += `</select>
    </div>
    
    <div class="col-xxl-4 col-xl-5 col-12">
    <label for="selectSectionType${rand}" class="form-label mb-1"> Post Sektion Typ</label>
    <select onchange="this.blur()" id="selectSectionType${rand}" class="form-control mb-3 me-xxl-2 me-0" name="section_type"
    style="max-width: 100%" ${data ? '' : 'disabled'}>`;
    if (data) {
        for (const [key, val] of Object.entries(data.section_select)) {
            let sel = '';
            if (val.aktiv == 1) {
                data && data.section_type == val.id ? sel = 'selected' : sel = '';
                html += `<option value="${val.id}" ${sel}>${val.bezeichnung}</option>`;
            }
        }
    }
    html += `</select>
    </div>
        
    <div class="col-xxl-8 col-xl-7 col-12">
    <label for="inputSelectClass${rand}" class="form-label mb-1"> CSS Selector</label>
    <input type="text" id="inputSelectClass${rand}" class="form-control ms-0 " name="css_selector"
    placeholder="${l.section_ph}" value="${data && data.css_selector ? data.css_selector : ''}">
    </div>
    </div>
    </div>
    </fieldset>
    <button onclick="add_new_section(this)" class="btn-add btn btn-blue-outline btn-sm mt-2 ${selected.btn_add ? '' : ' d-none'}"
    type="button">${l.add}</button>
    <button class="btn-api-data btn-edit btn btn-blue-outline btn-sm mt-2 ${selected.btn_change ? '' : ' d-none'}"
    type="button">${l.save_changes}</button> 
    <button class="btn-delete-section btn btn-outline-danger btn-sm mt-2 ${selected.btn_delete ? '' : ' d-none'}" 
    type="button">
    ${l.delete}</button>
  
    </form>
    </div>
    </div>`;
    return html;
}

function get_section_header_template(data, rand) {

    return `<h6>
        <i class="font-blue fa fa-arrow-circle-right"></i>&nbsp;
        Inhalts Typ: <span class="fw-normal">${data.content_type_head}</span>  | 
        Ausgabe Typ: <span class="fw-normal">${data.output_type_head}</span> | 
        CSS Selector: <span class="fw-normal">${data.css_selector}</span>
        </h6>
        <hr>
        <button onclick="sections_collapse_option(this)" type="button" data-bs-toggle="collapse" data-bs-target="#section${rand}"
        class="btn-section-collapse btn btn-blue-outline btn-sm">
        <i class="fa fa-arrow-circle-down"></i>&nbsp; Einstellungen
        </button>`;
}

function sections_collapse_option(e) {

    let collapsed = e.classList.contains('collapsed');
    let curIcon = e.querySelector('i');
    let collapseElementList = document.querySelectorAll('.btn-section-collapse');
    let collNodes = Array.prototype.slice.call(collapseElementList, 0);
    collNodes.forEach(function (collNodes) {
        let dataAttr = collNodes.getAttribute('data-bs-target');
        let collapseData = document.querySelector(dataAttr);
        let iconSel = collNodes.querySelector('i');
        let bsCollapse = new bootstrap.Collapse(collapseData, {
            'toggle': false
        });

        bsCollapse.hide();
        collNodes.classList.remove('active');
        iconSel.classList.remove('fa-arrow-circle-up');
        iconSel.classList.add('fa-arrow-circle-down')
    });

    if (collapsed) {
        e.classList.remove('active');
        curIcon.classList.remove('fa-arrow-circle-up');
        curIcon.classList.add('fa-arrow-circle-down');
    } else {
        e.classList.add('active');
        curIcon.classList.remove('fa-arrow-circle-down');
        curIcon.classList.add('fa-arrow-circle-up');
    }
}

function change_content_type_select(e, rand) {
    e.blur();
    let output_select = document.getElementById('selectFormOutputType' + rand);
    let sections_select = document.getElementById('selectSectionType' + rand);
    if (!e.value) {
        sections_select.selectedIndex = -1;
        output_select.setAttribute('disabled', 'disabled');
        output_select.selectedIndex = -1;
        sections_select.setAttribute('disabled', 'disabled');
    } else {
        const data = {
            'method': 'get_select_data',
            'content_type': e.value,
            'rand': rand,
        }
        send_xhr_hupa_api_edit_form_data(data, false, set_content_type_selection);
    }
}

function set_content_type_selection() {
    let data = JSON.parse(this.responseText);
    if (data.status) {
        let output_select = document.getElementById('selectFormOutputType' + data.rand);
        let sections_select = document.getElementById('selectSectionType' + data.rand);
        let outputSelectHtml = '';
        let sectionSelectHtml = '';
        for (const [key, val] of Object.entries(data.output_select)) {
            outputSelectHtml += `<option value="${val.id}">${val.bezeichnung}</option>`;
        }
        for (const [key, val] of Object.entries(data.section_select)) {
            sectionSelectHtml += `<option value="${val.id}">${val.bezeichnung}</option>`;
        }

        output_select.innerHTML = outputSelectHtml;
        output_select.removeAttribute('disabled');
        sections_select.innerHTML = sectionSelectHtml;
        sections_select.removeAttribute('disabled');
    } else {
        warning_message(data.msg);
    }
}

function add_new_section(e) {
    send_xhr_hupa_api_edit_form_data(e.form, true, set_new_content_type);
}

function set_new_content_type() {
    let data = JSON.parse(this.responseText);
    if (data.status) {
        let form = document.querySelector('.form' + data.rand);
        let inputType = form.querySelector('.input_type')
        let inputId = form.querySelector('.input_id');
        let btnAdd = form.querySelector('button.btn-add');
        let btnEdit = form.querySelector('button.btn-edit');
        btnAdd.classList.add('d-none');
        btnEdit.classList.remove('d-none');
        inputType.value = 'update';
        inputId.value = data.id;

        success_message(data.msg);
        let addBtn = get_section_header_template(data, data.rand);
        let placeDiv = document.querySelector('.newItemPlaceholder' + data.rand);
        placeDiv.insertAdjacentHTML("afterbegin", addBtn);
    } else {
        warning_message(data.msg);
    }
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

        $(document).on('dblclick', '.btn-admin-secret', function () {
            let dataId = $(this).attr('data-id');
            let btnSecret = $('#' + dataId);
            btnSecret.toggleClass('d-none');
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