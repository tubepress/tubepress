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
 * ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY}
 *     An array of tubepress_app_media_item_api_MediaItem instances representing the videos in the gallery.
 *     This array may be empty.
 *
 * ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}
 *     A string representing the page-unique ID for this gallery. Typically this is just a random number.
 *
 * ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PLAYER_HTML}
 *     A string containing HTML for the embedded media player. This may be empty as some players
 *     don't generate HTML.
 *
 * ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP}
 * ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM}
 *     Strings containing the top and bottom pagination HTML. These may or may not be present if the
 *     user hasn't requested pagination.
 *
 * ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}
 * ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT}
 *     Integers containing requested thumbnail width and height, in pixels.
 *
 * ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW}
 *     An array of strings of the media item attribute names that the user has requested to display for
 *     each item in ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY}
 *
 * ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}
 *     An associative array of media item attribute names (strings) to translated labels.
 *     e.g. 'timePublishedFormatted' => 'Date posted'. The array values will be shown to the user.
 *
 * $translator
 *     An instance of tubepress_lib_translation_api_TranslatorInterface
 */

/**
 * <div class="js-tubepress-gallery-123456789">
 *
 * The outer-most <div> for a TubePress media gallery. Ensure that the class
 * js-tubepress-gallery-* stays in the template, and on one of the
 * outer-most parent elements. Failure to do so will break most functionality for the user.
 */
?>
<div class="tubepress-youtube js-tubepress-gallery js-tubepress-gallery-<?php echo ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>"><?php

/**
 * First let's see if we have any items to display...
 */
if (empty(${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY})):

    ?><p class="tubepress-gallery-no-matching-videos">
        <?php echo $translator->_('No matching videos'); //>(translatable)<  ?>
    </p><?php

else:

    /**
     * The following statement prints out any HTML required for the TubePress "player location". This may be
     * completely empty. We do not recommend removing this statement, though you may move it around the template if you'd like.
     */
    echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PLAYER_HTML};

    /**
     * <div class="js-tubepress-pagination-and-thumbs">
     *
     * A <div> that wraps the pagination and thumbnails.
     *
     * You must retain the js-tubepress-pagination-and-thumbs class to let TubePress
     * know where the pagination and thumbnails are located. Failure to do so may break
     * client-side functionality.
     */
    ?>
    <div class="tubepress-pagination-and-thumbs js-tubepress-pagination-and-thumbs">

        <?php
        /**
         * The following block prints out any pagination above the thumbnail array, if necessary.
         */
        if (isset(${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP})) :
            echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP};
        endif;

        /**
         * <div class="tubepress_thumbs">
         *
         * A <div> that wraps the thumbnail array.
         */
        ?>
        <div class="tubepress-thumbs">

            <?php
            /**
             * Start looping through the videos...
             *
             * @var $mediaItem tubepress_app_media_item_api_MediaItem
             */
            foreach (${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY} as $mediaItem):

            /**
             * <div class="tubepress-thumb js-tubepress-fluid-thumb-adjustable">
             *
             * We set this to the width of the thumbnail so that the metadata doesn't spill over
             * onto other thumbnails.
             */
            ?>
            <div class="tubepress-thumb js-tubepress-fluid-thumb-adjustable"
                style="width: <?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>px">

                <div class="tubepress-youtube-thumbnail"  style="width: <?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>px">

                    <?php
                    /**
                     * The following <a> and <img> display the clickable video thumbnail. The opening <a> is a bit complex,
                     * so we delegate it to a fragment. If you are extending this theme, don't forget to copy this fragment
                     * over into your theme directory and update the path!
                     */
                    require dirname(__FILE__) . '/../default/_fragments/invoking_anchor_opener.fragment.php';

                    /**
                     * You must retain the js-tubepress-thumbnail-image class on the image. Failure to do
                     * so will break fluid thumbnails.
                     */
                    ?>
                    <img class="tubepress-thumbnail-image js-tubepress-fluid-thumb-reference"
                         alt="<?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, 'UTF-8'); ?>"
                         src="<?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL); ?>"
                         width="<?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>"
                         height="<?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT}; ?>" />
                    </a>

                    <?php
                    /**
                     * Print out the runtime.
                     */
                    if (in_array(tubepress_app_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})): ?>
                    <span class="tubepress-meta-duration">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED); ?>
                    </span>
                    <?php endif; ?>

                </div><?php //end of div.tubepress-youtube-thumbnail
                /**
                 * <dl class="tubepress-meta-group" ...>
                 *
                 * This <dl> wraps each video's metadata (title, runtime, etc).
                 */
                ?>
                <dl class="tubepress-meta-group"
                    style="width: <?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>px">

                        <?php
                        /**
                         * Limit the title to 35 characters.
                         */
                        if (in_array(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})):

                            if (strlen($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE)) > 35) {

                                $mediaItem->setAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE,
                                    substr($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE), 0, 35) . ' ...');
                            }
                        endif;

                        require dirname(__FILE__) . '/_fragments/authorDisplayNamePrep.fragment.php';

                        /**
                         * Adjust the display of "views"
                         */
                        if (in_array(tubepress_app_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})):

                            $attributeName = tubepress_app_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT;
                            ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}[$attributeName] = '';
                            $mediaItem->setAttribute("$attributeName.postHtml", ' views');
                        endif;

                        /**
                         * Get rid of the label for time published.
                         */
                        if (in_array(tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW})):

                            $attributeName = tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED;
                            ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}[$attributeName] = '';
                        endif;

                        /**
                         * We want to adjust the display order so that these are shown first.
                         */
                        $modifiedAttributeNames = array(
                            tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE,
                            tubepress_app_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
                            tubepress_app_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT,
                            tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
                        );
                        $modifiedAttributeNames = array_intersect($modifiedAttributeNames, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW});
                        ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} = array_diff(${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW}, $modifiedAttributeNames);
                        ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} = array_merge($modifiedAttributeNames, ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW});

                        /**
                         * Loop over the attributes for this media item.
                         */
                        foreach (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} as $attributeName):

                            /**
                             * We already showed the runtime.
                             */
                            if ($attributeName === tubepress_app_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED) {
                                continue;
                            }

                            /**
                             * The logic for printing the dd/dt pairs is delegated a fragment since it is shared
                             * with single_video.tpl.php. If you are extending this theme, don't forget to copy this fragment
                             * over into your theme directory!
                             */
                            require dirname(__FILE__) . '/../default/_fragments/dt_dd_pair.fragment.php';

                        endforeach; ?>

                </dl><?php //end of dl.tubepress-meta-group ?>

            </div><?php // end of div.tubepress-thumb

            /**
             * Stop looping through the videos...
             */
            endforeach; ?>

        </div><?php //end of div.tubepress-thumbs

        /**
         * The following block prints out any pagination below the thumbnail array, if necessary.
         */
        if (isset(${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM})) :
            echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM};
        endif; ?>

    </div><?php //end of div.tubepress-pagination-and-thumbs

endif; //end of top-level if/else block ?>

</div><?php //end of outermost div