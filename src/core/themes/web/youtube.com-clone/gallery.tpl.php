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

<div class="tubepress_container tubepress-youtube" id="tubepress_gallery_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>">

  <?php echo ${tubepress_core_player_api_Constants::TEMPLATE_VAR_HTML}; ?>

  <div id="tubepress_gallery_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>_thumbnail_area" class="tubepress_thumbnail_area">

    <?php if (isset(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP})) : echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOP}; endif; ?>

    <div class="tubepress_thumbs">
        <?php foreach (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_VIDEO_ARRAY} as $video): ?>

      <div class="tubepress_thumb">

          <div class="tubepress_img"  style="width: <?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>px">
              <a id="tubepress_image_<?php echo $video->getId(); ?>_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME}; ?>_<?php echo ${tubepress_core_player_api_Constants::TEMPLATE_VAR_NAME}; ?>_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>">
                  <img alt="<?php echo htmlspecialchars($video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, "UTF-8"); ?>" src="<?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL); ?>" width="<?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>" height="<?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT}; ?>" />
              </a>

              <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_LENGTH]): ?>
                  <span class="tubepress_meta_runtime">
                                <?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_FORMATTED); ?>

              </span>
              <?php endif; ?>
          </div>

        <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH}; ?>px">

          <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_TITLE]): ?>
          <dt class="tubepress_meta tubepress_meta_title"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_TITLE]; ?></dt><dd class="tubepress_meta tubepress_meta_title"><a id="tubepress_title_<?php echo $video->getId(); ?>_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME}; ?>_<?php echo ${tubepress_core_player_api_Constants::TEMPLATE_VAR_NAME}; ?>_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>"><?php

                      if (strlen($video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE)) > 35) {

                          $video->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE, substr($video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE), 0, 35) . ' ...');
                      }
                      echo htmlspecialchars($video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, "UTF-8");

                      ?></a></dd>
          <?php endif; ?>



          <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_AUTHOR]): ?>

          <dt class="tubepress_meta tubepress_meta_author">by</dt>
              <dd class="tubepress_meta tubepress_meta_author"><a rel="external nofollow" href="http://www.youtube.com/user/<?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID); ?>"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME); ?></a></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_KEYWORDS]): ?>

          <dt class="tubepress_meta tubepress_meta_keywords"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_KEYWORDS]; ?></dt><dd class="tubepress_meta tubepress_meta_keywords"><?php echo htmlspecialchars(implode(" ", $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_KEYWORD_ARRAY)), ENT_QUOTES, "UTF-8"); ?></a></dd>
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

          <dt class="tubepress_meta tubepress_meta_views"></dt><dd class="tubepress_meta tubepress_meta_views"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT); ?> views</dd>
          <?php endif; ?>

          <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_UPLOADED]): ?>

          <dt class="tubepress_meta tubepress_meta_uploaddate"></dt><dd class="tubepress_meta tubepress_meta_uploaddate"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_FORMATTED); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW}[tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION]): ?>

          <dt class="tubepress_meta tubepress_meta_description"><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS}[tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION]; ?></dt><dd class="tubepress_meta tubepress_meta_description"><?php echo $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION); ?></dd>
          <?php endif; ?>

        </dl>
      </div>
      <?php endforeach; ?>

    </div>
    <?php if (isset(${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM})) : echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_BOTTOM}; endif; ?>
  </div>
</div>
