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
<div class="tp-bs">

    <form class="form-horizontal" role="form" method="post">

        <div class="row">

            <div class="col-md-9">

                <h3><?php echo $pageTitle; ?></h3>

                <div class="well well-sm"><?php echo $introBlurb; ?></div>

            </div>

            <div class="col-md-3">

                <p id="filter-wrapper" class="text-right">

                    <?php $filterField = $fields['participant-filter-field']; echo $filterField->getTranslatedDisplayName() ?> <?php echo $filterField->getWidgetHTML(); ?>

                </p>
            </div>

        </div>

        <?php require TUBEPRESS_ROOT . '/src/main/resources/options-gui/page-fragment-templates/errors.tpl.php'; ?>

        <?php require TUBEPRESS_ROOT . '/src/main/resources/options-gui/page-fragment-templates/success.tpl.php'; ?>

        <?php require TUBEPRESS_ROOT . '/src/main/resources/options-gui/page-fragment-templates/tabs-parent.tpl.php'; ?>

        <div class="row">
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary" name="tubepress_save"><?php echo $saveText; ?></button>
            </div>

            <div class="col-md-3">

                <?php if ($isPro): ?>
                    &nbsp;
                <?php else: ?>

                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>You're Missing Out!</strong></div>
                        <div class="panel-body has-iframe" data-src="http://tubepress.com/snippets/wordpress/youre-missing-out.php">

                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <div class="col-md-3">

                <div class="panel panel-info">
                    <div class="panel-heading"><strong>TubePress News</strong></div>
                    <div class="panel-body has-iframe" data-src="http://tubepress.com/snippets/wordpress/latest-news.php">

                    </div>
                </div>

            </div>

            <div class="col-md-3">

                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Need Help?</strong></div>
                    <div class="panel-body has-iframe" data-src="http://tubepress.com/snippets/wordpress/need-help.php">

                    </div>
                </div>
            </div>
        </div>

        <?php echo $fields['tubepress-nonce']->getWidgetHTML(); ?>

	</form>
</div>
