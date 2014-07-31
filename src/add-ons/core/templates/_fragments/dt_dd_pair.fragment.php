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
 * The following <dt> prints out each attribute label (e.g. Ratings, Views, Comment Count, etc).
 *
 * @var $mediaItem tubepress_app_api_media_MediaItem
 */
?>
<dt class="tubepress-meta-<?php echo $attributeName; ?>"><?php
    if (isset($attributeLabels[$attributeName])):
        echo htmlspecialchars($attributeLabels[$attributeName], ENT_QUOTES, 'UTF-8');
    endif;
?></dt>

<?php
/**
 * The following <dd> prints out each attribute value.
 */
?>
<dd class="tubepress-meta-<?php echo $attributeName; ?>"><?php

    $anchorOpen = false;

    /**
     * The title gets special treatment because we want users to be able to click on it
     * to start the video.
     */
    if ($attributeName === tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE):
    require 'invoking_anchor_opener.fragment.php';
    $anchorOpen = true;
    endif;

    /**
     * Are we showing the URL for this video? If so, let's wrap it in an anchor and change the text.
     */
    if ($attributeName === tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL):
    ?>
    <a rel="nofollow" target="_blank" href="<?php echo htmlspecialchars($mediaItem->getAttribute($attributeName), ENT_QUOTES, 'UTF-8'); ?>"><?php
    $mediaItem->setAttribute($attributeName, 'URL');
    $anchorOpen = true;
    endif;

    /**
     * Do we have an author URL? If so, let's make it an anchor.
     */
    if ($attributeName === tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME &&
        $mediaItem->hasAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL)): ?>
    <a ref="nofollow" target="_blank" href="<?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL), ENT_QUOTES, 'UTF-8'); ?>"><?php
    $anchorOpen = true;
    endif;

    /**
     * Print out the attribute value.
     *
     * @var $mediaItem tubepress_app_api_media_MediaItem
     */
    echo htmlspecialchars($mediaItem->getAttribute($attributeName), ENT_QUOTES, 'UTF-8');

    /**
     * Close the anchor tag, if needed.
     */
    if ($anchorOpen): ?>
    </a>
    <?php endif;

?></dd>