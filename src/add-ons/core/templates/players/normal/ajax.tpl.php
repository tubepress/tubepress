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

/**
 * <div class="tubepress-normal-player">
 *
 * An outer wrapper for the title and embed.
 */
?>
<div class="tubepress-normal-player js-tubepress-player-normal" style="width: <?php echo ${tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX}; ?>px"><?php

    /**
     * <div class="tubepress-big-title">
     *
     * A <div> to hold the media items's title.
     */
    ?><div class="tubepress-big-title"><?php
        echo ${tubepress_app_api_template_VariableNames::MEDIA_ITEM}->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE);
    ?></div>

    <?php echo ${tubepress_app_api_template_VariableNames::EMBEDDED_SOURCE}; ?>

</div>