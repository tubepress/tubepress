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

<div class="form-group">

    <label for="<?php echo $field->getId(); ?>" class="col-md-3 control-label">

        <?php if ($field->isProOnly()) : ?>

            <a href="http://tubepress.com/pro"><span class="label label-primary">Pro</span></a>

        <?php endif; ?>

        <?php echo $field->getTranslatedDisplayName(); ?>

    </label>


    <div class="col-md-9">

        <?php echo $field->getWidgetHTML(); ?>

        <span class="help-block">
            <small>
                <?php echo $field->getTranslatedDescription(); ?>
            </small>
        </span>
    </div>
</div>
