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

<div class="tab-pane<?php if ($categoryIndex++ === 0): ?> active<?php endif; ?>" id="<?php echo $category->getId(); ?>">

    <div class="row">

        <div class="col-md-12">
            <?php
            foreach ($categoryIdToParticipantIdToFieldsMap[$category->getId()] as $participantId => $fieldIds) {

                if (count($fieldIds) === 0) {

                    //no fields in this participant - move on
                    continue;
                }

                $participant = $participants[$participantId];

                require 'single-participant.tpl.php';

            }
            ?>
        </div>
    </div>
</div>

