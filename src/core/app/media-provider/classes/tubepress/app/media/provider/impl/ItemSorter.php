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
class tubepress_app_media_provider_impl_ItemSorter implements tubepress_app_media_provider_api_ItemSorterInterface
{
    public function numericSort(tubepress_app_media_item_api_MediaItem $first,
                                tubepress_app_media_item_api_MediaItem $second,
                                $attributeName,
                                $descending)
    {
        if (!$first->hasAttribute($attributeName) || !$second->hasAttribute($attributeName)) {

            return 0;
        }

        $firstValue  = 1 * $first->getAttribute($attributeName);
        $secondValue = 1 * $second->getAttribute($attributeName);

        if ($descending) {

            //http://stackoverflow.com/a/3541734/229920
            list($firstValue, $secondValue) = array($secondValue, $firstValue);
        }

        if ($firstValue < $secondValue) {

            return -1;
        }

        if ($firstValue > $secondValue) {

            return 1;
        }

        return 0;
    }
}