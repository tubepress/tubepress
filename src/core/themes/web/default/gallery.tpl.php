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
 * First let's see if we have any videos to display...
 */
if (empty(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY})):

    echo $translator->_('No matching videos'); //>(translatable)<

else:

/**
 * <div class="tubepress_container" id="tubepress_gallery_123456789">
 *
 * The outer-most <div> for a TubePress gallery. Do not modify the "id" attribute. You may add additional class names to
 * the "class" attribute, but do not remove the existing "tubepress_container" class name. You may add additional attributes
 * to this <div>.
 */
?>
<div class="tubepress_container" id="tubepress_gallery_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>">

    <?php
    /**
     * The following statement prints out any HTML required for the TubePress "player location". We do not recommend removing
     * this statement, though you may move it around the template if you'd like.
     */
    echo ${tubepress_core_player_api_Constants::TEMPLATE_VAR_HTML};

    /**
     * <div id="tubepress_gallery_123456789_thumbnail_area" class="tubepress_thumbnail_area">
     *
     * This <div> wraps TubePress's thumbnails and pagination. Do not modify the "id" attribute. You may add additional class names to
     * the "class" attribute, but do not remove the existing "tubepress_thumbnail_area" class name.
     */
    ?>
    <div id="tubepress_gallery_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>_thumbnail_area" class="tubepress_thumbnail_area">

        <?php
        /**
         * The following block prints out any pagination above the thumbnail array, if necessary.
         */
        if (isset(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP})) :
            echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP};
        endif;

        /**
         * <div class="tubepress_thumbs">
         *
         * This <div> is the inner-most wrapper for the thumbnail array. You may add additional class names to
         * the "class" attribute, but do not remove the existing "tubepress_thumbs" class name.
         */
        ?>
        <div class="tubepress_thumbs">

            <?php
            /**
             * Start looping through the videos...
             *
             * @var $mediaItem tubepress_core_media_item_api_MediaItem
             */
            foreach (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY} as $mediaItem):

            /**
             * <div class="tubepress_thumb">
             *
             * This <div> wraps each thumbnail: the image and all of its metadata.
             */
            ?>
            <div class="tubepress_thumb">

                <a id="tubepress_image_<?php echo $mediaItem->getId(); ?>_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>" <?php echo $mediaItem->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_INVOCATION_ANCHOR_ATTRIBUTES); ?>>
                    <img id="tubepress_image_<?php echo $mediaItem->getId(); ?>_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>"
                         alt="<?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, 'UTF-8'); ?>"
                         src="<?php echo $mediaItem->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL); ?>"
                         width="<?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>"
                         height="<?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT}; ?>" />
                </a>

                <?php
                /**
                 * <dl class="tubepress_meta_group" ...>
                 *
                 * This <dl> wraps each video's metadata (title, runtime, etc). You may add additional class names to
                 * the "class" attribute, but do not remove the existing "tubepress_meta_group" class name.
                 */
                ?>
                <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>px">

                    <?php
                    /**
                     * Loop over the attributes for this media item.
                     */
                    foreach (${tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} as $attributeName):

                        /**
                         * The logic for printing the dd/dt pairs is delegated a fragment since it is shared
                         * with single_video.tpl.php. If you are extending this theme, don't forget to copy this fragment
                         * over into your theme directory!
                         */
                        require dirname(__FILE__) . '/_fragments/dt_dd_pair.fragment.php';

                    endforeach; ?>

                </dl><?php //end of dl.tubepress_meta_group ?>

            </div><?php // end of div.tubepress_thumb

            /**
             * Stop looping through the videos...
             */
            endforeach; ?>

        </div><?php //end of div.tubepress_thumbs

        /**
         * The following block prints out any pagination below the thumbnail array, if necessary.
         */
        if (isset(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM})) :
            echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM};
        endif; ?>

    </div><?php //end of div.tubepress_thumbnail_area ?>
</div><?php //end of div.tubepress_container

endif; //end of top-level if/else block ?>