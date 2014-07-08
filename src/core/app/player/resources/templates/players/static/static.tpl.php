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
 * <div class="tubepress-static-player">
 *
 * An outer wrapper for the title and embed.
 */
?>

<div class="tubepress-static-player" style="width: <?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>px"><?php

    /**
     * <div class="tubepress-big-title>
     *
     * A <div> to hold the media items's title.
     */
    ?><div class="tubepress-big-title"><?php
    echo ${tubepress_app_feature_single_api_Constants::TEMPLATE_VAR_MEDIA_ITEM}->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE);
    ?></div>

    <?php
    /**
     * A <div> to hold the media items's embed source.
     */
    ?>
    <div>
        <?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_SOURCE}; ?>
    </div>
</div>