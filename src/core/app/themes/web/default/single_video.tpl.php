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
 * Non-exhaustive list of variables in this template (add-ons may add other variables).
 *
 * ${tubepress_app_feature_single_api_Constants::TEMPLATE_VAR_MEDIA_ITEM}
 *    An instance of tubepress_app_media_item_api_MediaItem representing the video.
 *
 * ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW}
 *     An array of strings of the media item attribute names that the user has requested to display for
 *     each element of ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY}
 *
 * ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}
 *     An associative array of media item attribute names (strings) to translated labels.
 *     e.g. 'timePublishedFormatted' => 'Date posted'
 *
 * ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_SOURCE}
 *     A string containing the HTML for the embedded media player.
 *
 * ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_WIDTH}
 *     An integer representing the requested embedded media player width (in pixels).
 */

/**
 * <div class="tubepress-single-video-outermost">
 *
 * The outer-most <div> for a single TubePress media item.
 */
?>
<div class="tubepress-single-item-outermost">

<?php if (!isset($mediaItem)): ?>
    <p class="tubepress-single-item-not-found">
        <?php $translator->_('Item not found'); ?>
    </p>

<?php else:

    /**
     * The following block prints out the media item's title, if requested.
     */
    if (in_array(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})): ?>
    <div class="tubepress-meta-value-title"><?php
        echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php endif;

    /**
     * The following statement prints out any HTML required for the TubePress embedded media player. We do not recommend removing
     * this statement, though you may move it around the template if you'd like.
     */
    echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_SOURCE};

    /**
     * <dl class="tubepress_meta_group" ...>
     *
     * This <dl> wraps each video's metadata (title, duration, etc).
     */
    ?>
    <dl class="tubepress-meta-group">

        <?php
        /**
         * Loop over the attributes for this media item.
         */
        foreach (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} as $attributeName):

            if ($attributeName === tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE) {

                //we already showed this above.
                continue;
            }

            /**
             * The logic for printing the dd/dt pairs is delegated a fragment since it is shared
             * with gallery.tpl.php. If you are extending this theme, don't forget to copy this fragment
             * over into your theme directory and update the path!
             */
            require dirname(__FILE__) . '/_fragments/dt_dd_pair.fragment.php';

        endforeach; ?>
    
    </dl><?php //end of dl.tubepress-meta-group

    endif; ?>
    
</div><?php //end of div.tubepress-single-video-outermost ?>