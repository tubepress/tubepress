<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>

<div class="tab-pane fade<?php if ($categoryIndex++ === 0): ?> in active<?php endif; ?>" id="tubepress-core-theme-category">

    <div class="row">

        <div class="col-xs-12 col-sm-8 col-md-6 col-lg-5">

            <dl class="dl-horizontal text-muted">

            <?php

            /** @noinspection PhpUndefinedVariableInspection */
            foreach ($categoryIdToProviderIdToFieldsMap['tubepress-core-theme-category'] as $providerId => $fieldIds) {

                if (count($fieldIds) === 0) {

                    //no fields in this provider - move on
                    continue;
                }

                /** @noinspection PhpUndefinedVariableInspection */
                $fieldProvider = $fieldProviders[$providerId];

                foreach ($fieldIds as $fieldId) {

                    if (!isset($fields[$fieldId])) {

                        continue;
                    }

                    $field = $fields[$fieldId]; ?>

                    <dt><?php echo $field->getTranslatedDisplayName(); ?></dt>
                    <dd id="theme-field-dropdown"><?php echo $field->getWidgetHTML(); ?></dd><?php
                }
            }

            $termMap = array(

                ''              => 'description',
                'Author'        => 'author',
                'License(s)'    => 'licenses',
                'Version'       => 'version',
                'Demo'          => 'demo',
                'Keywords'      => 'keywords',
                'Homepage'      => 'homepage',
                'Documentation' => 'docs',
                'Download'      => 'download',
                'Bugs'          => 'bugs'
            );

            foreach ($termMap as $label => $id) :
            ?>
                <dt style="display: none"><?php echo $label; ?></dt>
                <dd style="display: none" id="theme-field-<?php echo $id; ?>"></dd>
            <?php endforeach; ?>
            </dl>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-6 col-lg-7" id="theme-screenshots">
            <div class="panel panel-default">
                <div class="panel-heading">Screenshots<span id="click-to-englarge-screenshots" style="display: none"> - click to enlarge</span></div>
                <div class="panel-body">
                    <p class="text-muted" style="display: none">None available.</p>
                </div>
            </div>

        </div>

    </div>

    <?php if ($field instanceof tubepress_core_options_ui_impl_fields_provided_ThemeField) : ?>

        <div id="theme-field-data" style="display: none">

            <script style="text-javascript">

                var TubePressThemes = <?php echo $field->getThemeDataAsJson(); ?>;
            </script>

        </div>

    <?php endif; ?>
</div>



<!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <!-- The container for the modal slides -->
    <div class="slides"></div>
    <!-- Controls for the borderless lightbox -->
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
    <!-- The modal dialog, which will be used to wrap the lightbox content -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-primary next">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

