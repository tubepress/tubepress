<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_media_AttributeFormatterInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_media_AttributeFormatterInterface';

    /**
     * @param tubepress_app_api_media_MediaItem $item
     * @param string                            $sourceAttributeName
     * @param string                            $destinationAttributeName
     * @param string                            $optionName
     */
    function truncateStringAttribute(tubepress_app_api_media_MediaItem $item, $sourceAttributeName,
                                               $destinationAttributeName, $optionName);

    /**
     * @param tubepress_app_api_media_MediaItem $item
     * @param string                            $sourceAttributeName
     * @param string                            $destinationAttributeName
     * @param int                               $precision
     */
    function formatNumberAttribute(tubepress_app_api_media_MediaItem $item, $sourceAttributeName,
                                             $destinationAttributeName, $precision);

    /**
     * @param tubepress_app_api_media_MediaItem $item
     * @param string                            $sourceAttributeName
     * @param string                            $destinationAttributeName
     */
    function formatDateAttribute(tubepress_app_api_media_MediaItem $item, $sourceAttributeName,
                                           $destinationAttributeName);

    /**
     * @param tubepress_app_api_media_MediaItem $item
     * @param string                            $sourceAttributeName
     * @param string                            $destinationAttributeName
     */
    function formatDurationAttribute(tubepress_app_api_media_MediaItem $item, $sourceAttributeName,
                                               $destinationAttributeName);

    /**
     * @param tubepress_app_api_media_MediaItem $item
     * @param string                            $sourceAttributeName
     * @param string                            $destinationAttributeName
     * @param string                            $glue
     */
    function implodeArrayAttribute(tubepress_app_api_media_MediaItem $item, $sourceAttributeName,
                                             $destinationAttributeName, $glue);
}