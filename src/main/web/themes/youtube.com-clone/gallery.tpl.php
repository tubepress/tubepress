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

<div class="tubepress_container tubepress-youtube" id="tubepress_gallery_<?php echo ${tubepress_core_api_const_template_Variable::GALLERY_ID}; ?>">

  <?php echo ${tubepress_core_api_const_template_Variable::PLAYER_HTML}; ?>

  <div id="tubepress_gallery_<?php echo ${tubepress_core_api_const_template_Variable::GALLERY_ID}; ?>_thumbnail_area" class="tubepress_thumbnail_area">

    <?php if (isset(${tubepress_core_api_const_template_Variable::PAGINATION_TOP})) : echo ${tubepress_core_api_const_template_Variable::PAGINATION_TOP}; endif; ?>

    <div class="tubepress_thumbs">
        <?php foreach (${tubepress_core_api_const_template_Variable::VIDEO_ARRAY} as $video): ?>

      <div class="tubepress_thumb">

          <div class="tubepress_img"  style="width: <?php echo ${tubepress_core_api_const_template_Variable::THUMBNAIL_WIDTH}; ?>px">
              <a id="tubepress_image_<?php echo $video->getId(); ?>_<?php echo ${tubepress_core_api_const_template_Variable::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_core_api_const_template_Variable::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${tubepress_core_api_const_template_Variable::PLAYER_NAME}; ?>_<?php echo ${tubepress_core_api_const_template_Variable::GALLERY_ID}; ?>">
                  <img alt="<?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?>" src="<?php echo $video->getThumbnailUrl(); ?>" width="<?php echo ${tubepress_core_api_const_template_Variable::THUMBNAIL_WIDTH}; ?>" height="<?php echo ${tubepress_core_api_const_template_Variable::THUMBNAIL_HEIGHT}; ?>" />
              </a>

              <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::LENGTH]): ?>
                  <span class="tubepress_meta_runtime">
                                <?php echo $video->getDuration(); ?>

              </span>
              <?php endif; ?>
          </div>

        <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_core_api_const_template_Variable::THUMBNAIL_WIDTH}; ?>px">

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::TITLE]): ?>
          <dt class="tubepress_meta tubepress_meta_title"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_core_api_const_options_Names::TITLE]; ?></dt><dd class="tubepress_meta tubepress_meta_title"><a id="tubepress_title_<?php echo $video->getId(); ?>_<?php echo ${tubepress_core_api_const_template_Variable::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_core_api_const_template_Variable::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${tubepress_core_api_const_template_Variable::PLAYER_NAME}; ?>_<?php echo ${tubepress_core_api_const_template_Variable::GALLERY_ID}; ?>"><?php

                      if (strlen($video->getTitle()) > 35) {

                          $video->setTitle(substr($video->getTitle(), 0, 35) . ' ...');
                      }
                      echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8");

                      ?></a></dd>
          <?php endif; ?>



          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::AUTHOR]): ?>

          <dt class="tubepress_meta tubepress_meta_author">by</dt>
              <dd class="tubepress_meta tubepress_meta_author"><a rel="external nofollow" href="http://www.youtube.com/user/<?php echo $video->getAuthorUid(); ?>"><?php echo $video->getAuthorDisplayName(); ?></a></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::KEYWORDS]): ?>

          <dt class="tubepress_meta tubepress_meta_keywords"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_core_api_const_options_Names::KEYWORDS]; ?></dt><dd class="tubepress_meta tubepress_meta_keywords"><?php echo htmlspecialchars(implode(" ", $video->getKeywords()), ENT_QUOTES, "UTF-8"); ?></a></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::URL]): ?>

          <dt class="tubepress_meta tubepress_meta_url"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_core_api_const_options_Names::URL]; ?></dt><dd class="tubepress_meta tubepress_meta_url"><a rel="external nofollow" href="<?php echo $video->getHomeUrl(); ?>"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_core_api_const_options_Names::URL]; ?></a></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::CATEGORY] &&
              $video->getCategory() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_category"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_core_api_const_options_Names::CATEGORY]; ?></dt><dd class="tubepress_meta tubepress_meta_category"><?php echo htmlspecialchars($video->getCategory(), ENT_QUOTES, "UTF-8"); ?></dd>
          <?php endif; ?>

          <?php if (isset(${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_youtube_api_const_options_Names::RATINGS]) && ${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_youtube_api_const_options_Names::RATINGS] &&
              $video->getRatingCount() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_ratings"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_youtube_api_const_options_Names::RATINGS]; ?></dt><dd class="tubepress_meta tubepress_meta_ratings"><?php echo $video->getRatingCount(); ?></dd>
          <?php endif; ?>

          <?php if (isset(${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_vimeo_api_const_options_Names::LIKES]) && ${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_vimeo_api_const_options_Names::LIKES] &&
              $video->getLikesCount() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_likes"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_vimeo_api_const_options_Names::LIKES]; ?></dt><dd class="tubepress_meta tubepress_meta_likes"><?php echo $video->getLikesCount(); ?></dd>
          <?php endif; ?>

          <?php if (isset(${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_youtube_api_const_options_Names::RATING]) && ${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_youtube_api_const_options_Names::RATING] &&
              $video->getRatingAverage() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_rating"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_youtube_api_const_options_Names::RATING]; ?></dt><dd class="tubepress_meta tubepress_meta_rating"><?php echo $video->getRatingAverage(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::ID]): ?>

          <dt class="tubepress_meta tubepress_meta_id"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_core_api_const_options_Names::ID]; ?></dt><dd class="tubepress_meta tubepress_meta_id"><?php echo $video->getId(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::VIEWS]): ?>

          <dt class="tubepress_meta tubepress_meta_views"></dt><dd class="tubepress_meta tubepress_meta_views"><?php echo $video->getViewCount(); ?> views</dd>
          <?php endif; ?>

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::UPLOADED]): ?>

          <dt class="tubepress_meta tubepress_meta_uploaddate"></dt><dd class="tubepress_meta tubepress_meta_uploaddate"><?php echo $video->getTimePublished(); ?></dd>
          <?php endif; ?>

          <?php if (${tubepress_core_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_core_api_const_options_Names::DESCRIPTION]): ?>

          <dt class="tubepress_meta tubepress_meta_description"><?php echo ${tubepress_core_api_const_template_Variable::META_LABELS}[tubepress_core_api_const_options_Names::DESCRIPTION]; ?></dt><dd class="tubepress_meta tubepress_meta_description"><?php echo $video->getDescription(); ?></dd>
          <?php endif; ?>

        </dl>
      </div>
      <?php endforeach; ?>

    </div>
    <?php if (isset(${tubepress_core_api_const_template_Variable::PAGINATION_BOTTOM})) : echo ${tubepress_core_api_const_template_Variable::PAGINATION_BOTTOM}; endif; ?>
  </div>
</div>
