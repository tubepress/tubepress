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
<select class="form-control" id="<?php echo $id; ?>" name="<?php echo $id; ?>">

	<?php foreach ($choices as $choiceValue => $choiceDisplayName): ?>

	    <option value="<?php echo $choiceValue; ?>" <?php if ($value === $choiceValue) { echo 'SELECTED'; } ?>><?php echo $choiceDisplayName; ?></option>

    <?php endforeach; ?>

</select>