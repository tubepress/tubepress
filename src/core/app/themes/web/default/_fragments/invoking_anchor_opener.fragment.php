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
 * As you can see, the <a> element has four attributes that TubePress needs to work with: class, href, rel, and target.
 * You may add to these attributes - e.g. add a class name - but do not remove any of the attributes
 * as doing so will likely break playback.
 */
?>
<a class="tubepress-cursor-pointer js-tubepress-invoker js-tubepress-itemid-<?php echo $mediaItem->getId(); ?>"<?php

    if ($mediaItem->hasAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_URL)): ?>
        href="<?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_URL), ENT_QUOTES, 'UTF-8'); ?>"
    <?php endif;

    if ($mediaItem->hasAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_REL)): ?>
        rel="<?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_REL); ?>"
    <?php endif;

    if ($mediaItem->hasAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_TARGET)): ?>
        target="<?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_TARGET); ?>"
    <?php endif; ?>>