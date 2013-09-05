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

<div class="tab-pane<?php if ($activeCategoryId === $category->getId()): ?> active<?php endif; ?>" id="<?php echo $category->getId(); ?>">

    <div class="row">

        <div class="col-md-8">
            <?php
            foreach ($categoryIdToParticipantIdToFieldsMap[$category->getId()] as $participantId => $fieldIds) {

                $participant = $participants[$participantId];

                require 'single-participant.tpl.php';

            }
            ?>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
</div>

