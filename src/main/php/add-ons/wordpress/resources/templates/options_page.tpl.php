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

        <?php require TUBEPRESS_ROOT . '/src/main/resources/options-gui/page-fragment-templates/errors.tpl.php'; ?>

        <?php require TUBEPRESS_ROOT . '/src/main/resources/options-gui/page-fragment-templates/tabs-parent.tpl.php'; ?>

        <button type="submit" class="btn btn-primary">Save</button>

        <?php echo $fields['tubepress-nonce']->getWidgetHTML(); ?>

	</form>
</div>
