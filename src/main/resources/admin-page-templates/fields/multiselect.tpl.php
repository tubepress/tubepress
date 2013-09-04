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

    <select id="<?php echo $name; ?>" name="<?php echo $name; ?>[]" multiple="multiple" class="form-control">

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


<script type="text/javascript">

    jQuery(function() {

        jQuery('#<?php echo $name; ?>').multiselect({

            buttonClass : 'btn btn-default btn-sm',
            dropRight   : true,
            buttonText  : function (options, select) { return 'select ...'; }
        });
    });
</script>
