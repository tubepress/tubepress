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


<?php if ($participantId !== tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::PARTICIPANT_ID): ?>

<div class="panel panel-default tubepress-participant-<?php echo $participantId; ?>">

    <div class="panel-heading">

        <h3 class="panel-title"><?php echo $participant->getTranslatedDisplayName(); ?></h3>

    </div>

    <div class="panel-body">

        <?php

        endif;

        foreach ($fieldIds as $fieldId) {

            if (!isset($fields[$fieldId])) {

                continue;
            }

            $field = $fields[$fieldId];

            require 'single-field.tpl.php';
        }

        ?>

        <?php if ($participantId !== tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::PARTICIPANT_ID): ?>

    </div>
</div>
<?php endif; ?>

