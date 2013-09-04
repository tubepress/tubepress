<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<div class="tp-bs">

    <form class="form-horizontal" role="form" method="post">

        <div class="row">
            <div class="col-md-6">
                <h3><?php echo $pageTitle; ?></h3>
                <div class="well well-sm"><?php echo $introBlurb; ?></div>
            </div>
            <div class="col-md-6">
                <p id="filter-wrapper" class="text-right">
                    <?php $filterField = $fields['participant-filter-field']; echo $filterField->getTranslatedDisplayName() ?> <?php echo $filterField->getWidgetHTML(); ?>
                </p>
            </div>
        </div>

        <div class="tabbable">

            <ul class="nav nav-tabs">

                <?php $categoryIndex = 0; ?>
                <?php foreach ($categories as $category): ?>

                    <li<?php if ($categoryIndex++ === 0): ?> class="active"<?php endif; ?>><a href="#<?php echo $category->getId(); ?>" data-toggle="tab"><?php echo $category->getTranslatedDisplayName(); ?></a></li>

                <?php endforeach; ?>
            </ul>

            <div class="tab-content">

                <?php $categoryIndex = 0; ?>
                <?php foreach ($categories as $category): ?>

                    <div class="tab-pane<?php if ($categoryIndex++ === 0): ?> active<?php endif; ?>" id="<?php echo $category->getId(); ?>">

                            <?php foreach ($categoryIdToParticipantIdToFieldsMap[$category->getId()] as $participantId => $fieldIds): ?>
                            <?php $participant = $participants[$participantId]; ?>

                                <div class="row">
                                    <div class="col-md-10">

                                        <?php if ($participantId !== tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant::PARTICIPANT_ID): ?>

                                        <div class="panel panel-default">

                                            <div class="panel-heading">
                                                <h3 class="panel-title"><?php echo $participant->getTranslatedDisplayName(); ?></h3>
                                            </div>

                                            <div class="panel-body">

                                        <?php endif; ?>

                                                <?php foreach ($fieldIds as $fieldId) : ?>
                                                    <?php $field = $fields[$fieldId]; ?>
                                                    <div class="form-group">
                                                        <label for="<?php echo $field->getId(); ?>" class="col-lg-3 control-label">
                                                            <?php if ($field->isProOnly()) : ?>
                                                            <a href="http://tubepress.com/pro"><img src="http://tubepress.com/wp-content/plugins/tubepress/src/main/web/images/pro_tag.png" /></a>
                                                            <?php endif; ?>
                                                            <?php echo $field->getTranslatedDisplayName(); ?>
                                                        </label>
                                                        <div class="col-lg-9">
                                                            <?php echo $field->getWidgetHTML(); ?><span class="help-block"><?php echo $field->getTranslatedDescription(); ?></span>
                                                        </div>
                                                    </div>

                                                <?php endforeach; ?>

                                        <?php if ($participantId !== tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant::PARTICIPANT_ID): ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                    </div>
                                    <div class="col-md-2">&nbsp;</div>
                                </div>

                            <?php endforeach; ?>
                     </div>

                <?php endforeach; ?>

            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>

        <?php
            //http://codex.wordpress.org/Function_Reference/wp_nonce_field
            wp_nonce_field('tubepress-save', 'tubepress-nonce');
        ?>

	</form>
</div>

<?php echo $inlineJS; ?>