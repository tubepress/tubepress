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

<select id="<?php echo $id; ?>" name="<?php echo $id; ?>[]" multiple="multiple" class="form-control multiselect tubepress-bootstrap-multiselect-field" data-selecttext="<?php echo $selectText; ?>">

    <?php foreach ($ungroupedChoices as $choiceValue => $choiceDisplayName): ?>

        <option value="<?php echo $choiceValue; ?>" <?php if (in_array($choiceValue, $currentlySelectedValues)) { echo 'selected="selected"'; } ?>><?php echo $choiceDisplayName ?></option>

    <?php endforeach; ?>

    <?php foreach ($groupedChoices as $choiceGroupDisplayName => $choicesArray): ?>

        <optgroup label="<?php echo $choiceGroupDisplayName; ?>">

            <?php foreach ($choicesArray as $choiceValue => $choiceDisplayName): ?>

                <option value="<?php echo $choiceValue; ?>" <?php if (in_array($choiceValue, $currentlySelectedValues)) { echo 'selected="selected"'; } ?>><?php echo $choiceDisplayName ?></option>

            <?php endforeach; ?>

        </optgroup>

    <?php endforeach; ?>
</select>