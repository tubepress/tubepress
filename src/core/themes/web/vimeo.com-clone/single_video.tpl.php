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

<div class="tubepress_single_video tubepress-vimeo">

    <?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_SOURCE}; ?>

    <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_TITLE]): ?>
        <div class="tubepress_embedded_title"><?php echo htmlspecialchars($video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, "UTF-8"); ?></div>
    <?php endif; ?>

    <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>px">

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_LENGTH]): ?>

            <dt class="tubepress_meta tubepress_meta_runtime"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_LENGTH]; ?></dt><dd class="tubepress_meta tubepress_meta_runtime"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED); ?></dd>
        <?php endif; ?>

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_AUTHOR]): ?>

            <dt class="tubepress_meta tubepress_meta_author">from</dt><dd class="tubepress_meta tubepress_meta_author"><a rel="external nofollow" href="http://www.vimeo.com/<?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID); ?>"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME); ?></a></dd>
        <?php endif; ?>

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_UPLOADED]): ?>

            <dt class="tubepress_meta tubepress_meta_uploaddate"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_UPLOADED]; ?></dt><dd class="tubepress_meta tubepress_meta_uploaddate"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED); ?></dd>
        <?php endif; ?>

    <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_KEYWORDS]): ?>

            <dt class="tubepress_meta tubepress_meta_keywords"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_KEYWORDS]; ?></dt><dd class="tubepress_meta tubepress_meta_keywords"><?php echo $raw = htmlspecialchars(implode(" ", $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_KEYWORD_ARRAY)), ENT_QUOTES, "UTF-8"); ?></a></dd>
        <?php endif; ?>

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_URL]): ?>

            <dt class="tubepress_meta tubepress_meta_url"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_URL]; ?></dt><dd class="tubepress_meta tubepress_meta_url"><a rel="external nofollow" href="<?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_HOME_URL); ?>"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_URL]; ?></a></dd>
        <?php endif; ?>

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_CATEGORY] &&
            $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME) != ""):
            ?>

            <dt class="tubepress_meta tubepress_meta_category"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_CATEGORY]; ?></dt><dd class="tubepress_meta tubepress_meta_category"><?php echo htmlspecialchars($video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME), ENT_QUOTES, "UTF-8"); ?></dd>
        <?php endif; ?>

        <?php if (isset(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATINGS]) && ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATINGS] &&
            $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_COUNT) != ""):
            ?>

            <dt class="tubepress_meta tubepress_meta_ratings"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_youtube_api_Constants::OPTION_RATINGS]; ?></dt><dd class="tubepress_meta tubepress_meta_ratings"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_COUNT); ?></dd>
        <?php endif; ?>

        <?php if (isset(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_vimeo_api_Constants::OPTION_LIKES]) && ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_vimeo_api_Constants::OPTION_LIKES] &&
            $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT) != ""):
            ?>

            <dt class="tubepress_meta tubepress_meta_likes"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_vimeo_api_Constants::OPTION_LIKES]; ?></dt><dd class="tubepress_meta tubepress_meta_likes"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT); ?></dd>
        <?php endif; ?>

        <?php if (isset(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATING]) && ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATING] &&
            $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE) != ""):
            ?>

            <dt class="tubepress_meta tubepress_meta_rating"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_youtube_api_Constants::OPTION_RATING]; ?></dt><dd class="tubepress_meta tubepress_meta_rating"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE); ?></dd>
        <?php endif; ?>

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_ID]): ?>

            <dt class="tubepress_meta tubepress_meta_id"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_ID]; ?></dt><dd class="tubepress_meta tubepress_meta_id"><?php echo $video->getId(); ?></dd>
        <?php endif; ?>

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_VIEWS]): ?>

            <dt class="tubepress_meta tubepress_meta_views"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_VIEWS]; ?></dt><dd class="tubepress_meta tubepress_meta_views"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT); ?></dd>
        <?php endif; ?>

        <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION]): ?>

            <dt class="tubepress_meta tubepress_meta_description"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION]; ?></dt><dd class="tubepress_meta tubepress_meta_description"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION); ?></dd>
        <?php endif; ?>

    </dl>

</div>