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
 * @api
 * @since 4.0.0
 */
interface tubepress_core_media_provider_api_ItemSorterInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_media_provider_api_ItemSorterInterface';

    function numericSort(tubepress_core_media_item_api_MediaItem $first,
                         tubepress_core_media_item_api_MediaItem $second,
                         $attributeName,
                         $descending);
}