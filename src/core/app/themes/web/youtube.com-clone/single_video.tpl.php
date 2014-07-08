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
 * <div class="tubepress_single_video">
 *
 * The outer-most <div> for a TubePress media item. You may add additional class names to
 * the "class" attribute, but do not remove the existing "tubepress_single_video" class name.
 */
?>
<div class="tubepress_single_video tubepress-youtube">

    <?php
    /**
     * The following statement prints out any HTML required for the TubePress embedded media player. We do not recommend removing
     * this statement, though you may move it around the template if you'd like.
     */
    echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_SOURCE};

    /**
     * The following block prints out the media item's title, if requested. You may add to, but not remove,
     * the existing class names for each of the elements.
     */
    if (in_array(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})): ?>
        <div class="tubepress_embedded_title"><?php
            if (strlen($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE)) > 55) {

                $mediaItem->setAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE, substr($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE), 0, 55) . ' ...');
            }
            echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, 'UTF-8');
            ?></div>
    <?php endif;
    /**
     * <dl class="tubepress_meta_group" ...>
     *
     * This <dl> wraps each video's metadata (title, duration, etc). You may add additional class names to
     * the "class" attribute, but do not remove the existing "tubepress_meta_group" class name.
     */
    ?>
    <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>px">

        <?php require dirname(__FILE__) . '/_fragments/authorDisplayNamePrep.fragment.php';

        $topDlElements = array(
            tubepress_app_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_app_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT,
        );

        foreach ($topDlElements as $attributeName) {

            if (in_array($attributeName, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})) {

                require dirname(__FILE__) . '/../default/_fragments/dt_dd_pair.fragment.php';
            }
        } ?>

    </dl>

    <?php
    /**
     * <dl class="tubepress_meta_group" ...>
     *
     * This <dl> wraps each video's metadata (title, duration, etc). You may add additional class names to
     * the "class" attribute, but do not remove the existing "tubepress_meta_group" class name.
     */
    ?>
    <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>px">

        <?php
        if (in_array(tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})):

            $attributeName = tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED;
            ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}[$attributeName] = 'Published on';
            require dirname(__FILE__) . '/../default/_fragments/dt_dd_pair.fragment.php';
        endif;

        /**
         * Remove the title, author, and view count, since we already displayed them.
         */
        ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} =
            array_diff(${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW},
                array(
                    tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE,
                    tubepress_app_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
                    tubepress_app_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT,
                    tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
                ));

        /**
         * Loop over the attributes for this media item.
         */
        foreach (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} as $attributeName):

            /**
             * The logic for printing the dd/dt pairs is delegated a fragment since it is shared
             * with gallery.tpl.php. If you are extending this theme, don't forget to copy this fragment
             * over into your theme directory!
             */
            require dirname(__FILE__) . '/../default/_fragments/dt_dd_pair.fragment.php';

        endforeach; ?>

    </dl><?php //end of dl.tubepress_meta_group ?>

</div><?php //end of div.tubepress_single_video ?>