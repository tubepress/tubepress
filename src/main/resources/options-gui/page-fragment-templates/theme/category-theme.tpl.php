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

<div class="tab-pane fade<?php if ($categoryIndex++ === 0): ?> in active<?php endif; ?>" id="theme-category">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-6">

            <dl class="dl-horizontal text-muted">

            <?php

            /** @noinspection PhpUndefinedVariableInspection */
            foreach ($categoryIdToParticipantIdToFieldsMap['theme-category'] as $participantId => $fieldIds) {

                if (count($fieldIds) === 0) {

                    //no fields in this participant - move on
                    continue;
                }

                /** @noinspection PhpUndefinedVariableInspection */
                $participant = $participants[$participantId];

                foreach ($fieldIds as $fieldId) {

                    if (!isset($fields[$fieldId])) {

                        continue;
                    }

                    $field = $fields[$fieldId]; ?>
                    <dt style="color: black"><?php echo $field->getTranslatedDisplayName(); ?></dt>
                    <dd><?php echo $field->getWidgetHTML(); ?></dd><?php
                }
            }
            ?>

                <dt></dt>
                <dd style="color: black">A description list is perfect for defining terms.</dd>
                <dt>Author</dt>
                <dd><a href="http://tubepress.llc" target="_blank">TubePress LLC</a></dd>
                <dt>License(s)</dt>
                <dd><a href="http://www.mozilla.org/MPL/2.0/" target="_blank">MPL-2.0</a></dd>
                <dt>Felis euismod semper eget lacinia</dt>
                <dd>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</dd>
            </dl>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">
            <p class="bg-info text-muted" style="padding: 15px">No screenshots available.</p>
        </div>
    </div>
</div>

