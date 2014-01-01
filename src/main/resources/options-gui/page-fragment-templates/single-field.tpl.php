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

<div class="form-group" <?php if ($field instanceof tubepress_impl_options_ui_fields_HiddenField) : ?>style="display:none"<?php endif; ?>>

    <label for="<?php echo $field->getId(); ?>" class="col-xs-12 col-sm-4 col-md-4 col-lg-3 control-label">

        <?php if (!$isPro && $field->isProOnly()) : ?>

            <a href="http://tubepress.com/pro">
                    <span class="label label-primary" style="font-size: 100%">Pro</span>
            </a>

        <?php endif; ?>

        <?php echo $field->getTranslatedDisplayName(); ?>

    </label>


    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">

        <?php echo $field->getWidgetHTML(); ?>

        <span class="help-block">
            <?php echo $field->getTranslatedDescription(); ?>
        </span>

    </div>
</div>
