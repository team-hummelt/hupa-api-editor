<?php
defined('ABSPATH') or die();
/**
 * hupa-minify
 * @package Hummelt & Partner HUPA API EDITOR
 * Copyright 2021, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 */

global $editableOption;
$options = $editableOption->hupa_set_editable_options();
//Optionen Settings
$opt = apply_filters('get_hupa_api_settings', false);

?>

<div class="api-editor">
    <div class="container">
        <div class="card card-license shadow-sm">
            <h5 class="card-header d-flex align-items-center bg-hupa py-4">
                <i class="icon-hupa-white d-block mt-2" style="font-size: 2rem"></i>&nbsp;
                HUPA&nbsp; <?= __('API Editor', 'hupa-api-editor') ?> </h5>
            <div class="card-body pb-4" style="min-height: 72vh">
                <div class="d-flex align-items-center">
                    <h5 class="card-title">
                        <i data-id="btnShowOptions"
                           class="btn-admin-secret hupa-color fa fa-arrow-circle-right"></i> <?= __('API Editor', 'hupa-api-editor') ?>
                        / <span id="currentSideTitle"><?= __('CSS Sections', 'hupa-api-editor') ?></span>
                    </h5>
                </div>
                <hr>
                <div class="hupa-edit-option settings-btn-group d-block d-md-flex flex-wrap <?= $options['show_option'] ?: 'hupa-hide' ?>">
                    <button data-site="<?= __('CSS Sections', 'hupa-api-editor') ?>"
                            data-load="start"
                            type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseApiEditorSektionen"
                            class="btn-editor-collapse btn btn-hupa btn-outline-secondary btn-sm active" disabled>
                        <i class="fa fa-css3"></i>&nbsp;
                        <?= __('CSS Sections', 'hupa-api-editor') ?>
                    </button>
                    <button data-site="<?= __('Visibility', 'hupa-api-editor') ?>"
                            data-load="visibility"
                            type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseApiEditorVisibility"
                            class="btn-editor-collapse btn btn-hupa btn-outline-secondary btn-sm">
                        <i class="fa fa-eye-slash"></i>&nbsp;
                        <?= __('Visibility', 'hupa-api-editor') ?>
                    </button>
                    <button data-site="<?= __('Settings', 'hupa-api-editor') ?>"
                            data-load="settings"
                            type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseApiEditorSettings"
                            class="btn-editor-collapse btn btn-hupa btn-outline-secondary btn-sm">
                        <i class="fa fa-gears"></i>&nbsp;
                        <?= __('Settings', 'hupa-api-editor') ?>
                    </button>

                    <button data-site="<?= __('Reset', 'hupa-api-editor') ?>"
                            data-load="reset"
                            type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseApiEditorReset"
                            class="btn-editor-collapse btn btn-hupa btn-outline-secondary btn-sm ms-auto me-xl-2">
                        <i class="fa fa-random"></i>&nbsp;
                        <?= __('Reset', 'hupa-api-editor') ?>
                    </button>
                </div>
                <button data-site="<?= __('Admin', 'hupa-api-editor') ?>"
                        id="btnShowOptions"
                        data-load="admin"
                        type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseApiAdmin"
                        class="btn-admin-hide btn-editor-collapse btn btn-hupa btn-outline-secondary btn-sm ms-1 mt-2 d-none">
                    <i class="fa fa-user-secret"></i>&nbsp;
                    <?= __('Admin', 'hupa-api-editor') ?>
                </button>
                <hr class="hupa-edit-option <?= $options['show_option'] ?: 'hupa-hide' ?>">
                <div id="api-editor-display-data">
                    <div class="hupa-edit-option <?= $options['show_option'] ?: 'hupa-hide' ?>">
                        <!-- JOB WARNING API EDITOR STARTSEITE -->
                        <div class="collapse show" id="collapseApiEditorSektionen"
                             data-bs-parent="#api-editor-display-data">
                            <div class="border rounded mt-1 mb-3 shadow-sm p-3 " style="min-height: 53vh">
                                <form method="post">
                                    <input type="hidden" name="method" value="sections_form_options">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <h5 class="card-title">
                                            <i class="font-blue fa fa-wordpress"></i>&nbsp; <?= __('Add | Remove CSS Sections', 'hupa-api-editor') ?>
                                        </h5>
                                    </div>
                                    <hr>
                                    <button id="add_css_section" type="button" class="btn btn-blue btn-sm">
                                        <i class="fa fa-plus"></i>&nbsp; <?= __('Add new section', 'hupa-api-editor') ?>
                                    </button>
                                    <hr>
                                </form>

                                <div id="apiEditSections"></div>
                            </div>
                        </div>

                        <!-- JOB WARNING API EDITOR SICHTBARKEIT -->
                        <div class="collapse" id="collapseApiEditorVisibility"
                             data-bs-parent="#api-editor-display-data">

                            <div class="border rounded mt-1 mb-3 shadow-sm p-3 bg-custom-gray" style="min-height: 53vh">
                                <form class="sendAjaxEditApiForm" action="#" method="post">
                                    <input type="hidden" name="method" value="editable_form_options">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <h5 class="card-title">
                                            <i class="font-blue fa fa-wordpress"></i>&nbsp; <?= __('Posts | Edit pages', 'hupa-api-editor') ?>
                                        </h5>
                                        <div class="ajax-status-spinner ms-auto d-inline-block mb-2 pe-2"></div>
                                    </div>
                                    <hr>
                                    <?php
                                    $post_types = get_post_types();
                                    $ignore_post_types = ['reply', 'attachment', 'topic', 'report', 'status', 'wp_block'];
                                    $accept_post_types = ['post', 'page'];
                                    foreach ($post_types as $post_type_name):
                                        if (!in_array($post_type_name, $accept_post_types)) {
                                            continue;
                                        }

                                        if (is_post_type_hierarchical($post_type_name)) {
                                            //continue;
                                        }
                                        $post_type_data = get_post_type_object($post_type_name);
                                        if ($post_type_data->show_ui === FALSE) {
                                            continue;
                                        } ?>

                                        <div class="mb-3">
                                            <label for="postTypeDuplicatorSelect"
                                                   class="form-label mb-1 strong-font-weight"><?= esc_html($post_type_data->labels->singular_name) ?></label>
                                            <select onchange="this.blur()" id="postTypeDuplicatorSelect"
                                                    name="show_editable_interfaces[<?= esc_attr($post_type_name) ?>]"
                                                    class="form-select">
                                                <option value="show" <?= isset($options['show_editable_interfaces'][$post_type_name]) && $options['show_editable_interfaces'][$post_type_name] == 'show' ? ' selected' : ''; ?>><?= esc_html__("show", 'hupa-api-editor') ?></option>
                                                <option value="hide" <?= isset($options['show_editable_interfaces'][$post_type_name]) && $options['show_editable_interfaces'][$post_type_name] == 'hide' ? ' selected' : '' ?>><?= esc_html__("hide", 'hupa-api-editor') ?></option>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                    <hr>
                                    <h6>
                                        <i class="font-blue fa fa-arrow-circle-down"></i> <?= esc_html__('Minimum requirement for using this function', 'bootscore') ?>
                                    </h6>
                                    <hr>
                                    <label for="capabilityDuplicatorSelect"
                                           class="form-label mb-1 strong-font-weight"><?= esc_html__('User Role', 'bootscore') ?></label>
                                    <select onchange="this.blur()" id="capabilityDuplicatorSelect" name="capability"
                                            class="form-select mb-3">
                                        <option value="read" <?= isset($options['capability']) && $options['capability'] == "read" ? 'selected' : '' ?>><?= esc_html__('Subscriber', 'bootscore') ?></option>
                                        <option value="edit_posts" <?= isset($options['capability']) && $options['capability'] == "edit_posts" ? 'selected' : '' ?>><?= esc_html__('Contributor', 'hupa-api-editor') ?></option>
                                        <option value="publish_posts" <?= isset($options['capability']) && $options['capability'] == "publish_posts" ? 'selected' : '' ?>><?= esc_html__('Author', 'hupa-api-editor') ?></option>
                                        <option value="publish_pages" <?= isset($options['capability']) && $options['capability'] == "publish_pages" ? 'selected' : '' ?>><?= esc_html__('Editor', 'hupa-api-editor') ?></option>
                                        <option value="manage_options" <?= !isset($options['capability']) || empty($options['capability']) || (isset($options['capability']) && $options['capability'] == "manage_options") ? 'selected' : '' ?>><?= esc_html__('Administrator', 'hupa-api-editor') ?></option>
                                    </select>
                                    <hr>
                                </form>
                            </div>
                        </div><!--Startseite-->
                        <!-- JOB WARNING API EDITOR SETTINGS -->
                        <div class="collapse" id="collapseApiEditorSettings"
                             data-bs-parent="#api-editor-display-data">
                            <div class="border rounded mt-1 mb-3 shadow-sm p-3 bg-custom-gray" style="min-height: 53vh">
                                <form class="sendAjaxEditApiForm" action="#" method="post">
                                    <input type="hidden" name="method" value="update_api_settings_options">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <h5 class="card-title">
                                            <i class="font-blue fa fa-wordpress"></i>&nbsp; <?= __('Settings', 'hupa-api-editor') ?>
                                        </h5>
                                        <div class="ajax-status-spinner ms-auto d-inline-block mb-2 pe-2"></div>
                                    </div>
                                    <hr>
                                    <h6>
                                        <i class="font-blue fa fa-arrow-circle-down"></i> <?= __('Textarea', 'hupa-api-editor') ?> <?= __('Settings', 'hupa-api-editor') ?>
                                    </h6>
                                    <div class="row g-3 pt-2">
                                        <div class="col-xl-6 col-12">
                                            <label for="FormClass" class="form-label mb-1">Form Class</label>
                                            <input type="text" name="textarea_form_wrapper"
                                                   value="<?= $opt->textarea_form_wrapper ?>"
                                                   placeholder="api-edit-wrapper"
                                                   class="form-control" id="FormClass">
                                        </div>

                                        <div class="col-xl-6 col-12">
                                            <label for="InputTextAreaFormClass" class="form-label mb-1">Input Form
                                                Class</label>
                                            <input type="text" name="textarea_form_class"
                                                   value="<?= $opt->textarea_form_class ?>"
                                                   placeholder="form-control w-100"
                                                   class="form-control" id="InputTextAreaFormClass">
                                        </div>

                                        <div class="col-xl-6 col-12">
                                            <label for="TextAreaSubmitClass" class="form-label mb-1">Submit Button
                                                CSS</label>
                                            <input type="text" name="textarea_submit_class"
                                                   value="<?= $opt->textarea_submit_class ?>"
                                                   placeholder="btn btn-outline-success btn-sm mt-2 me-1"
                                                   class="form-control" id="TextAreaSubmitClass">
                                        </div>

                                        <div class="col-xl-6 col-12">
                                            <label for="TextAreaCancelClass" class="form-label mb-1">Cancel Button
                                                CSS</label>
                                            <input type="text" name="textarea_cancel_class"
                                                   value="<?= $opt->textarea_cancel_class ?>"
                                                   placeholder="btn btn-outline-danger btn-sm mt-2 me-1"
                                                   class="form-control" id="TextAreaCancelClass">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap mt-4">

                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" name="textarea_auto_height" type="checkbox"
                                                   role="switch"
                                                   id="CheckAutoHeightTextarea" <?= !$opt->textarea_auto_height ?: 'checked' ?>>
                                            <label class="form-check-label"
                                                   for="CheckAutoHeightTextarea"><?= __('Auto Height') ?></label>
                                        </div>

                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" name="textarea_show_button" type="checkbox"
                                                   role="switch"
                                                   id="CheckShowButtonTextarea" <?= !$opt->textarea_show_button ?: 'checked' ?>>
                                            <label class="form-check-label"
                                                   for="CheckShowButtonTextarea"><?= __('Show Button') ?></label>
                                        </div>


                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" name="textarea_symbol" type="checkbox"
                                                   role="switch"
                                                   id="CheckShowSymbolTextarea" <?= !$opt->textarea_symbol ?: 'checked' ?>>
                                            <label class="form-check-label"
                                                   for="CheckShowSymbolTextarea"><?= __('Symbol anzeigen') ?></label>
                                        </div>
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" type="checkbox" name="textarea_label"
                                                   role="switch"
                                                   id="CheckShowTextareaLabel" <?= !$opt->textarea_label ?: 'checked' ?>>
                                            <label class="form-check-label"
                                                   for="CheckShowTextareaLabel"><?= __('Label anzeigen') ?></label>
                                        </div>

                                    </div>
                                    <hr>
                                    <h6>
                                        <i class="font-blue fa fa-arrow-circle-down"></i> <?= __('Text, Number, Date, Select, E-Mail, Url', 'hupa-api-editor') ?> <?= __('Settings', 'hupa-api-editor') ?>
                                    </h6>
                                    <hr>
                                    <div class="row g-3 pt-2">
                                        <div class="col-xl-6 col-12">
                                            <label for="InputFormClassWrapper" class="form-label mb-1">Form
                                                Class</label>
                                            <input type="text" name="input_form_wrapper"
                                                   value="<?= $opt->input_form_wrapper ?>"
                                                   placeholder="api-edit-wrapper"
                                                   class="form-control" id="InputFormClassWrapper">
                                        </div>

                                        <div class="col-xl-6 col-12">
                                            <label for="InputFormClass" class="form-label mb-1">Input Form Class</label>
                                            <input type="text" name="input_form_class"
                                                   placeholder="form-control w-100"
                                                   value="<?= $opt->input_form_class ?>"
                                                   class="form-control" id="InputFormClass">
                                        </div>

                                        <div class="col-xl-6 col-12">
                                            <label for="InputSubmitClass" class="form-label mb-1">Submit Button
                                                CSS</label>
                                            <input type="text" name="input_submit_class"
                                                   value="<?= $opt->input_submit_class ?>"
                                                   placeholder="btn btn-outline-success btn-sm mt-2 me-1"
                                                   class="form-control" id="InputSubmitClass">
                                        </div>

                                        <div class="col-xl-6 col-12">
                                            <label for="InputCancelClass" class="form-label mb-1">Cancel Button
                                                CSS</label>
                                            <input type="text" name="input_cancel_class"
                                                   value="<?= $opt->input_cancel_class ?>"
                                                   placeholder="btn btn-outline-danger btn-sm mt-2 me-1"
                                                   class="form-control" id="InputCancelClass">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap mt-4">

                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" name="input_show_button" type="checkbox"
                                                   role="switch"
                                                   id="CheckShowButtonInput" <?= !$opt->input_show_button ?: 'checked' ?>>
                                            <label class="form-check-label"
                                                   for="CheckShowButtonInput"><?= __('Show Button') ?></label>
                                        </div>

                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" name="input_symbol" type="checkbox"
                                                   role="switch"
                                                   id="CheckShowSymbolInput" <?= !$opt->input_symbol ?: 'checked' ?>>
                                            <label class="form-check-label"
                                                   for="CheckShowSymbolInput"><?= __('Symbol anzeigen') ?></label>
                                        </div>
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" type="checkbox"
                                                   name="input_label"
                                                   role="switch"
                                                   id="CheckShowInputLabel" <?= !$opt->input_label ?: 'checked' ?>>
                                            <label class="form-check-label"
                                                   for="CheckShowInputLabel"><?= __('Label anzeigen') ?></label>
                                        </div>
                                    </div>

                                    <hr>
                                    <h6>
                                        <i class="font-blue fa fa-arrow-circle-down"></i> <?= __('Inline', 'hupa-api-editor') ?> <?= __('Settings', 'hupa-api-editor') ?>
                                    </h6>
                                    <hr>
                                    <div class="row g-3 py-2">
                                        <div class="col-xl-6 col-12">
                                            <label for="InlineFormClassWrapper" class="form-label mb-1">Form
                                                Class</label>
                                            <input type="text" name="inline_form_wrapper" placeholder="api-edit-wrapper"
                                                   value="<?= $opt->inline_form_wrapper ?>"
                                                   class="form-control" id="InlineFormClassWrapper">
                                        </div>

                                        <div class="col-xl-6 col-12">
                                            <label for="InlineFormClass" class="form-label mb-1">Input Form
                                                Class</label>
                                            <input type="text" name="inline_form_class"
                                                   placeholder="form-control w-100"
                                                   value="<?= $opt->inline_form_class ?>"
                                                   class="form-control" id="InlineFormClass">
                                        </div>
                                    </div>
                                    <div class="form-check form-switch my-3">
                                        <input class="form-check-input" name="inline_symbol" type="checkbox"
                                               role="switch"
                                               id="CheckShowSymbolInline" <?= !$opt->inline_symbol ?: 'checked' ?>>
                                        <label class="form-check-label"
                                               for="CheckShowSymbolInline"><?= __('Symbol anzeigen') ?></label>
                                    </div>

                                </form>

                            </div>
                        </div>
                        <!-- JOB WARNING API EDITOR RESET -->
                        <div class="collapse" id="collapseApiEditorReset"
                             data-bs-parent="#api-editor-display-data">
                            <div class="border rounded mt-1 mb-3 shadow-sm p-3 bg-custom-gray" style="min-height: 53vh">
                                <div class="d-flex align-items-center flex-wrap">
                                    <h5 class="card-title">
                                        <i class="font-blue fa fa-wordpress"></i>&nbsp; <?= __('Reset', 'hupa-api-editor') ?>
                                    </h5>
                                    <div class="ajax-status-spinner ms-auto d-inline-block mb-2 pe-2"></div>
                                </div>
                                <hr>
                                <div class="api-reset-wrapper">
                                    <h4>
                                        <i class="text-alert fa fa-exclamation-triangle"></i>&nbsp; <?= __('Reset all settings', 'hupa-api-editor') ?>
                                        <small class="small d-block fw-normal mt-1">Diese Aktion lädt die <b
                                                    class="text-alert strong-font-weight"> Default Settings</b> und kann
                                            <b class="text-alert strong-font-weight">nicht rückgängig</b> gemacht
                                            werden.</small>
                                    </h4>
                                    <hr>
                                    <button data-type="reset_settings" class="btn-delete-section btn btn-danger"><i
                                                class="fa fa-exclamation-triangle"></i>&nbsp; <?= __('Reset all settings', 'hupa-api-editor') ?>
                                    </button>
                                </div>
                                <div class="api-reset-wrapper d-none">
                                    <h5><i class="fa fa-info-circle font-blue"></i>&nbsp; Settings wurden zurückgesetzt
                                        <small class="small fw-normal text-muted d-block mb-3 mt-1">
                                            Nach dem zurücksetzen der Settings, muss die Seite <b>neu geladen</b>
                                            werden.</small></h5>
                                    <a href="<?= admin_url() . 'options-general.php?page=hupa-api-editor-options' ?>"
                                       class="btn btn-blue-outline"><i
                                                class="fa fa-refresh fa-spin"></i>&nbsp; <?= __('Reload page', 'hupa-api-editor') ?>
                                    </a>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div><!--hide-Option-->
                    <!-- JOB WARNING API ADMIN -->
                    <div class="collapse" id="collapseApiAdmin"
                         data-bs-parent="#api-editor-display-data">
                        <div class="border rounded mt-1 mb-3 shadow-sm p-3 bg-custom-gray" style="min-height: 53vh">
                            <div class="d-flex align-items-center flex-wrap">
                                <h4 class="card-title">
                                    <i class="font-blue fa fa-user-secret"></i>&nbsp; <?= __('SU Admin', 'hupa-api-editor') ?>
                                </h4>
                                <div class="ajax-status-spinner ms-auto d-inline-block mb-2 pe-2"></div>
                            </div>
                            <hr>
                            <h6><i class="fa fa-user-secret"></i> Option Seite anzeigen</h6>
                            <form class="sendAjaxEditApiForm" action="#" method="post">
                                <input type="hidden" name="method" value="update_su_admin_options">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           name="show_option_page" id="CheckOptionPageAktiv" <?=!$options['show_option'] ?: 'checked'?>>
                                    <label class="form-check-label" for="CheckOptionPageAktiv">aktiv</label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!--parent-->

            </div><!--card-->
            <small class="card-body-bottom" style="right: 1.5rem">
                DB: <i class="hupa-color me-1">v<?= HUPA_API_EDITOR_DB_VERSION ?></i> |
                API-Editor Version:&nbsp;
                <i class="hupa-color">v<?= HUPA_API_EDITOR_VERSION ?></i></small>
        </div>
    </div>
    <div class="modal fade" id="deleteApiEditModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-hupa">
                    <h5 class="modal-title text-white" id="exampleModalLabel"><i class="fa fa-trash-o"></i>&nbsp; <span
                                class="modal-delete-msg"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Recipient:</label>
                            <input type="text" class="form-control" id="recipient-name">
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Message:</label>
                            <textarea class="form-control" id="message-text"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal"><i
                                class="text-alert fa fa-close"></i>&nbsp; <?= __('cancel', 'hupa-api-editor') ?>
                    </button>
                    <button type="button" class="btn-delete-sections btn btn-danger btn-sm" data-bs-dismiss="modal"><i
                                class="fa fa-trash-o"></i>&nbsp; <?= __('execute', 'hupa-api-editor') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="snackbar-success"></div>
<div id="snackbar-warning"></div>