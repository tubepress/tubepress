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
?>

<?php
/**
 * <div class="tubepress_container" id="tubepress_gallery_123456789">
 *
 * The outer-most <div> for a TubePress gallery. Do not modify the "id" attribute. You may add additional class names to
 * the "class" attribute, but do not remove the existing "tubepress_container" class name. You may add additional attributes
 * to this <div>.
 */
?>
<div class="tubepress_container tubepress-vimeo" id="tubepress_gallery_<?php echo ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>">

    <?php
    /**
     * The following statement prints out any HTML required for the TubePress "player location". We do not recommend removing
     * this statement, though you may move it around the file.
     */
    ?>
    <?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PLAYER_HTML}; ?>

    <?php
    /**
     * <div id="tubepress_gallery_123456789_thumbnail_area" class="tubepress_thumbnail_area">
     *
     * This <div> wraps TubePress's thumbnails and pagination. Do not modify the "id" attribute. You may add additional class names to
     * the "class" attribute, but do not remove the existing "tubepress_thumbnail_area" class name.
     */
    ?>
    <div id="tubepress_gallery_<?php echo ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>_thumbnail_area" class="tubepress_thumbnail_area">

        <?
        /**
         * The following statement prints out any pagination above the thumbnail array, if necessary.
         */
        ?>
        <?php if (isset(${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP})) : echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP}; endif; ?>

        <?php
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
             * @var $mediaItem tubepress_app_media_item_api_MediaItem
             */
            foreach (${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY} as $mediaItem): ?>

            <?php
            /**
             * <div class="tubepress_thumb">
             *
             * This <div> wraps each thumbnail: the image and all of its metadata.
             */
            ?>
            <div class="tubepress_thumb">

                <a id="tubepress_image_<?php echo $mediaItem->getId(); ?>_<?php echo ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME}; ?>_<?php echo ${tubepress_app_player_api_Constants::TEMPLATE_VAR_NAME}; ?>_<?php echo ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>">
                    <img alt="<?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, "UTF-8"); ?>" src="<?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL); ?>" width="<?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>" height="<?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT}; ?>" />
                </a>

                <?php
                /**
                 * <dl class="tubepress_meta_group" ...>
                 *
                 * This <dl> wraps each video's metadata (title, runtime, etc). You may add additional class names to
                 * the "class" attribute, but do not remove the existing "tubepress_meta_group" class name.
                 */
                ?>
                <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>px">

                    <?php
                    /**
                     * The following dt/dd block prints out the media item's title, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_TITLE]): ?>
                    <dt class="tubepress_meta tubepress_meta_title">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_TITLE]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_title">
                        <a id="tubepress_title_<?php echo $mediaItem->getId(); ?>_<?php echo ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME}; ?>_<?php echo ${tubepress_app_player_api_Constants::TEMPLATE_VAR_NAME}; ?>_<?php echo ${tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>">
                            <?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, "UTF-8"); ?>
                        </a>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's duration, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_LENGTH]): ?>
                    <dt class="tubepress_meta tubepress_meta_runtime">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_LENGTH]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_runtime">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's author display name, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_AUTHOR]): ?>
                    <dt class="tubepress_meta tubepress_meta_author">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_AUTHOR]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_author">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's keywords, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_KEYWORDS]): ?>
                    <dt class="tubepress_meta tubepress_meta_keywords">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_KEYWORDS]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_keywords">
                        <?php echo htmlspecialchars(implode(" ", $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_KEYWORD_ARRAY)), ENT_QUOTES, "UTF-8"); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's URL, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_URL]): ?>
                    <dt class="tubepress_meta tubepress_meta_url">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_URL]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_url">
                        <a rel="external nofollow" href="<?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_HOME_URL); ?>">
                            <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_URL]; ?>
                        </a>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's category, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_CATEGORY] &&
                      $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME) != ""):
                    ?>
                    <dt class="tubepress_meta tubepress_meta_category">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_CATEGORY]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_category">
                        <?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME), ENT_QUOTES, "UTF-8"); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's rating count, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (isset(${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATINGS]) &&
                        ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATINGS] &&
                        $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_RATING_COUNT) != ""):
                    ?>
                    <dt class="tubepress_meta tubepress_meta_ratings">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_youtube_api_Constants::OPTION_RATINGS]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_ratings">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_RATING_COUNT); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's "likes" count, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (isset(${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_vimeo_api_Constants::OPTION_LIKES]) &&
                        ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_vimeo_api_Constants::OPTION_LIKES] &&
                        $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT) != ""):
                    ?>
                    <dt class="tubepress_meta tubepress_meta_likes">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_vimeo_api_Constants::OPTION_LIKES]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_likes">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's rating average, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (isset(${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATING]) &&
                        ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATING] &&
                        $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE) != ""):
                    ?>
                    <dt class="tubepress_meta tubepress_meta_rating">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_youtube_api_Constants::OPTION_RATING]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_rating">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's ID, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_ID]): ?>
                    <dt class="tubepress_meta tubepress_meta_id">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_ID]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_id">
                        <?php echo $mediaItem->getId(); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's view count, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_VIEWS]): ?>
                    <dt class="tubepress_meta tubepress_meta_views">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_VIEWS]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_views">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's publish date, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_UPLOADED]): ?>
                    <dt class="tubepress_meta tubepress_meta_uploaddate">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_UPLOADED]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_uploaddate">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED); ?>
                    </dd>
                    <?php endif; ?>


                    <?php
                    /**
                     * The following dt/dd block prints out the media item's description, if required. You may add to, but not remove, the existing class names for each
                     * of the elements. Do not modify or remove any of the existing "id" attributes.
                     */
                    ?>
                    <?php if (${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_app_media_item_api_Constants::OPTION_DESCRIPTION]): ?>
                    <dt class="tubepress_meta tubepress_meta_description">
                        <?php echo ${tubepress_app_media_item_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_app_media_item_api_Constants::OPTION_DESCRIPTION]; ?>
                    </dt>
                    <dd class="tubepress_meta tubepress_meta_description">
                        <?php echo $mediaItem->getAttribute(tubepress_app_media_item_api_Constants::ATTRIBUTE_DESCRIPTION); ?>
                    </dd>
                    <?php endif; ?>

                </dl>
            </div><?php // end of div.tubepress_thumb ?>
            <?php
            /**
             * Stop looping through the videos...
             */
            ?>
            <?php endforeach; ?>

        </div><?php //end of div.tubepress_thumbs ?>

        <?
        /**
         * The following statement prints out any pagination above the thumbnail array, if necessary.
         */
        ?>
        <?php if (isset(${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM})) : echo ${tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM}; endif; ?>

    </div><?php //end of div.tubepress_thumbnail_area ?>
</div><?php //end of div.tubepress_container ?>
