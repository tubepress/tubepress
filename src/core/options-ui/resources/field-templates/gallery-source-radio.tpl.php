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
<div class="input-group">

    <span class="input-group-addon">

        <input type="radio" name="mode" id="<?php echo $modeName; ?>" value="<?php echo $modeName; ?>" <?php if ($currentMode === $modeName) { echo 'CHECKED'; } ?> />

    </span>

    <?php echo $additionalFieldWidgetHtml; ?>
</div><!-- /input-group -->

