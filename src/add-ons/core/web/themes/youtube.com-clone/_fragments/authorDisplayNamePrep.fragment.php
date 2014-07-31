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
 * Add a link to the uploader's channel.
 */
if (in_array(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME, ${tubepress_app_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTES_TO_SHOW})):

    $attributeName = tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME;

    ${tubepress_app_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTE_LABELS}[$attributeName] = 'by';
    $namePre = sprintf('<a rel="external nofollow" href="https://www.youtube.com/user/%s" target="_blank">',
        $mediaItem->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID));
    $mediaItem->setAttribute("$attributeName.preHtml", $namePre);
    $mediaItem->setAttribute("$attributeName.postHtml", '</a>');
endif;