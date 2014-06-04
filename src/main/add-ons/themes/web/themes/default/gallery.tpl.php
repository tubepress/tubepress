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

<div class="tubepress_container" id="tubepress_gallery_<?php echo ${tubepress_core_template_api_const_VariableNames::GALLERY_ID}; ?>">

  <?php echo ${tubepress_core_template_api_const_VariableNames::PLAYER_HTML}; ?>

  <div id="tubepress_gallery_<?php echo ${tubepress_core_template_api_const_VariableNames::GALLERY_ID}; ?>_thumbnail_area" class="tubepress_thumbnail_area">

    <?php if (isset(${tubepress_core_template_api_const_VariableNames::PAGINATION_TOP})) : echo ${tubepress_core_template_api_const_VariableNames::PAGINATION_TOP}; endif; ?>

    <div class="tubepress_thumbs">
        <?php foreach (${tubepress_core_template_api_const_VariableNames::VIDEO_ARRAY} as $video): ?>

      <div class="tubepress_thumb">
        <a id="tubepress_image_<?php echo $video->getId(); ?>_<?php echo ${tubepress_core_template_api_const_VariableNames::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_core_template_api_const_VariableNames::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${tubepress_core_template_api_const_VariableNames::PLAYER_NAME}; ?>_<?php echo ${tubepress_core_template_api_const_VariableNames::GALLERY_ID}; ?>">
          <img alt="<?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?>" src="<?php echo $video->getThumbnailUrl(); ?>" width="<?php echo ${tubepress_core_template_api_const_VariableNames::THUMBNAIL_WIDTH}; ?>" height="<?php echo ${tubepress_core_template_api_const_VariableNames::THUMBNAIL_HEIGHT}; ?>" />
        </a>
        <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_core_template_api_const_VariableNames::THUMBNAIL_WIDTH}; ?>px">

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_TITLE]): ?>
          <dt class="tubepress_meta tubepress_meta_title"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_TITLE]; ?></dt><dd class="tubepress_meta tubepress_meta_title"><a id="tubepress_title_<?php echo $video->getId(); ?>_<?php echo ${tubepress_core_template_api_const_VariableNames::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_core_template_api_const_VariableNames::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${tubepress_core_template_api_const_VariableNames::PLAYER_NAME}; ?>_<?php echo ${tubepress_core_template_api_const_VariableNames::GALLERY_ID}; ?>"><?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?></a></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_LENGTH]): ?>

          <dt class="tubepress_meta tubepress_meta_runtime"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_LENGTH]; ?></dt><dd class="tubepress_meta tubepress_meta_runtime"><?php echo $video->getDuration(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_AUTHOR]): ?>

          <dt class="tubepress_meta tubepress_meta_author"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_AUTHOR]; ?></dt><dd class="tubepress_meta tubepress_meta_author"><?php echo $video->getAuthorDisplayName(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_shortcode_api_Constants::OPTION_KEYWORDS]): ?>

          <dt class="tubepress_meta tubepress_meta_keywords"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_shortcode_api_Constants::OPTION_KEYWORDS]; ?></dt><dd class="tubepress_meta tubepress_meta_keywords"><?php echo $raw = htmlspecialchars(implode(" ", $video->getKeywords()), ENT_QUOTES, "UTF-8"); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_URL]): ?>

          <dt class="tubepress_meta tubepress_meta_url"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_URL]; ?></dt><dd class="tubepress_meta tubepress_meta_url"><a rel="external nofollow" href="<?php echo $video->getHomeUrl(); ?>"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_URL]; ?></a></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_CATEGORY] &&
              $video->getCategory() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_category"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_CATEGORY]; ?></dt><dd class="tubepress_meta tubepress_meta_category"><?php echo htmlspecialchars($video->getCategory(), ENT_QUOTES, "UTF-8"); ?></dd>
          <?php endif; ?>

          <?php if (isset(${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATINGS]) && ${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATINGS] &&
              $video->getRatingCount() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_ratings"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_youtube_api_Constants::OPTION_RATINGS]; ?></dt><dd class="tubepress_meta tubepress_meta_ratings"><?php echo $video->getRatingCount(); ?></dd>
          <?php endif; ?>

          <?php if (isset(${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_vimeo_api_Constants::OPTION_LIKES]) && ${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_vimeo_api_Constants::OPTION_LIKES] &&
              $video->getLikesCount() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_likes"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_vimeo_api_Constants::OPTION_LIKES]; ?></dt><dd class="tubepress_meta tubepress_meta_likes"><?php echo $video->getLikesCount(); ?></dd>
          <?php endif; ?>

          <?php if (isset(${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATING]) && ${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_youtube_api_Constants::OPTION_RATING] &&
              $video->getRatingAverage() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_rating"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_youtube_api_Constants::OPTION_RATING]; ?></dt><dd class="tubepress_meta tubepress_meta_rating"><?php echo $video->getRatingAverage(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_ID]): ?>

          <dt class="tubepress_meta tubepress_meta_id"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_ID]; ?></dt><dd class="tubepress_meta tubepress_meta_id"><?php echo $video->getId(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_VIEWS]): ?>

          <dt class="tubepress_meta tubepress_meta_views"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_VIEWS]; ?></dt><dd class="tubepress_meta tubepress_meta_views"><?php echo $video->getViewCount(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_UPLOADED]): ?>

          <dt class="tubepress_meta tubepress_meta_uploaddate"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_UPLOADED]; ?></dt><dd class="tubepress_meta tubepress_meta_uploaddate"><?php echo $video->getTimePublished(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW}[tubepress_core_media_single_api_Constants::OPTION_DESCRIPTION]): ?>

          <dt class="tubepress_meta tubepress_meta_description"><?php echo ${tubepress_core_template_api_const_VariableNames::META_LABELS}[tubepress_core_media_single_api_Constants::OPTION_DESCRIPTION]; ?></dt><dd class="tubepress_meta tubepress_meta_description"><?php echo $video->getDescription(); ?></dd>
          <?php endif; ?>

        </dl>
      </div>
      <?php endforeach; ?>

    </div>
    <?php if (isset(${tubepress_core_template_api_const_VariableNames::PAGINATION_BOTTOM})) : echo ${tubepress_core_template_api_const_VariableNames::PAGINATION_BOTTOM}; endif; ?>
  </div>
</div>
