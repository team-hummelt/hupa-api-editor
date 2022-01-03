<?php
defined('ABSPATH') or die();
/**
 * hupa-minify
 * @package Hummelt & Partner HUPA API EDITOR
 * Copyright 2021, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 */
?>

<div class="api-editor">

    <div class="container">
        <div class="card card-license shadow-sm">
            <h5 class="card-header d-flex align-items-center bg-hupa py-4">
                <i class="icon-hupa-white d-block mt-2" style="font-size: 2rem"></i>&nbsp;
                HUPA&nbsp; <?= __('API Editor', 'hupa-api-editor') ?> </h5>
            <div class="card-body pb-4" style="min-height: 72vh">
                <div class="d-flex align-items-center">
                    <h5 class="card-title"><i
                                class="hupa-color fa fa-arrow-circle-right"></i> <?= __('API Editor', 'hupa-api-editor') ?>
                        / <span id="currentSideTitle"><?= __('Settings', 'hupa-api-editor') ?></span>
                    </h5>
                </div>
                <hr>
                <div class="settings-btn-group d-block d-md-flex flex-wrap">
                    <button data-site="<?= __('Settings', 'hupa-api-editor') ?>"
                            data-type="start"
                            type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseApiEditorStartSite"
                            class="btn-editor-collapse btn btn-hupa btn-outline-secondary btn-sm active" disabled>
                        <i class="fa fa-wrench"></i>&nbsp;
                        <?= __('Settings', 'hupa-api-editor') ?>
                    </button>

                </div>
                <hr>
                <div id="api-editor-display-data">
                    <!-- JOB WARNING API EDITOR STARTSEITE -->
                    <div class="collapse show" id="collapseApiEditorStartSite"
                         data-bs-parent="#api-editor-display-data">

                        <div class="border rounded mt-1 mb-3 shadow-sm p-3 bg-custom-gray" style="min-height: 53vh">

                        </div>
                    </div><!--Startseite-->
                </div><!--parent-->

            </div><!--card-->
            <small class="card-body-bottom" style="right: 1.5rem">API-Editor Version:&nbsp; <i
                        class="hupa-color">v<?= HUPA_API_EDITOR_VERSION ?></i></small>
        </div>
    </div>
</div>
