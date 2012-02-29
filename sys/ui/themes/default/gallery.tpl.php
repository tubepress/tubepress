<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Uber simple/fast template for TubePress. Idea from here: http://seanhess.net/posts/simple_templating_system_in_php
 * Sure, maybe your templating system of choice looks prettier but I'll bet it's not faster :)
 */
?>

<div class="tubepress_container" id="tubepress_gallery_<?php echo ${org_tubepress_api_const_template_Variable::GALLERY_ID}; ?>">

  <?php echo ${org_tubepress_api_const_template_Variable::PLAYER_HTML}; ?>

  <div id="tubepress_gallery_<?php echo ${org_tubepress_api_const_template_Variable::GALLERY_ID}; ?>_thumbnail_area" class="tubepress_thumbnail_area">

    <?php if (isset(${org_tubepress_api_const_template_Variable::PAGINATION_TOP})) : echo ${org_tubepress_api_const_template_Variable::PAGINATION_TOP}; endif; ?>

    <div class="tubepress_thumbs">
        <?php foreach (${org_tubepress_api_const_template_Variable::VIDEO_ARRAY} as $video): ?>

      <div class="tubepress_thumb">
        <a id="tubepress_image_<?php echo $video->getId(); ?>_<?php echo ${org_tubepress_api_const_template_Variable::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${org_tubepress_api_const_template_Variable::PLAYER_NAME}; ?>_<?php echo ${org_tubepress_api_const_template_Variable::GALLERY_ID}; ?>">
          <img alt="<?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?>" src="<?php echo $video->getThumbnailUrl(); ?>" width="<?php echo ${org_tubepress_api_const_template_Variable::THUMBNAIL_WIDTH}; ?>" height="<?php echo ${org_tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT}; ?>" />
        </a>
        <dl class="tubepress_meta_group">

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::TITLE]): ?>
          <dt class="tubepress_meta tubepress_meta_title"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::TITLE]; ?></dt><dd class="tubepress_meta tubepress_meta_title"><a id="tubepress_title_<?php echo $video->getId(); ?>_<?php echo ${org_tubepress_api_const_template_Variable::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${org_tubepress_api_const_template_Variable::PLAYER_NAME}; ?>_<?php echo ${org_tubepress_api_const_template_Variable::GALLERY_ID}; ?>"><?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?></a></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::LENGTH]): ?>

          <dt class="tubepress_meta tubepress_meta_runtime"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::LENGTH]; ?></dt><dd class="tubepress_meta tubepress_meta_runtime"><?php echo $video->getDuration(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::AUTHOR]): ?>

          <dt class="tubepress_meta tubepress_meta_author"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::AUTHOR]; ?></dt><dd class="tubepress_meta tubepress_meta_author"><?php echo $video->getAuthorDisplayName(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::KEYWORDS]): ?>

          <dt class="tubepress_meta tubepress_meta_keywords"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::KEYWORDS]; ?></dt><dd class="tubepress_meta tubepress_meta_keywords"><?php echo $raw = htmlspecialchars(implode(" ", $video->getKeywords()), ENT_QUOTES, "UTF-8"); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::URL]): ?>

          <dt class="tubepress_meta tubepress_meta_url"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::URL]; ?></dt><dd class="tubepress_meta tubepress_meta_url"><a rel="external nofollow" href="<?php echo $video->getHomeUrl(); ?>"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::URL]; ?></a></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::CATEGORY] &&
              $video->getCategory() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_category"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::CATEGORY]; ?></dt><dd class="tubepress_meta tubepress_meta_category"><?php echo htmlspecialchars($video->getCategory(), ENT_QUOTES, "UTF-8"); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::RATINGS] &&
              $video->getRatingCount() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_ratings"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::RATINGS]; ?></dt><dd class="tubepress_meta tubepress_meta_ratings"><?php echo $video->getRatingCount(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::LIKES] &&
              $video->getLikesCount() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_likes"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::LIKES]; ?></dt><dd class="tubepress_meta tubepress_meta_likes"><?php echo $video->getLikesCount(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::RATING] &&
              $video->getRatingAverage() != ""):
          ?>

          <dt class="tubepress_meta tubepress_meta_rating"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::RATING]; ?></dt><dd class="tubepress_meta tubepress_meta_rating"><?php echo $video->getRatingAverage(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::ID]): ?>

          <dt class="tubepress_meta tubepress_meta_id"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::ID]; ?></dt><dd class="tubepress_meta tubepress_meta_id"><?php echo $video->getId(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::VIEWS]): ?>

          <dt class="tubepress_meta tubepress_meta_views"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::VIEWS]; ?></dt><dd class="tubepress_meta tubepress_meta_views"><?php echo $video->getViewCount(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::UPLOADED]): ?>

          <dt class="tubepress_meta tubepress_meta_uploaddate"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::UPLOADED]; ?></dt><dd class="tubepress_meta tubepress_meta_uploaddate"><?php echo $video->getTimePublished(); ?></dd>
          <?php endif; ?>

          <?php if (${org_tubepress_api_const_template_Variable::META_SHOULD_SHOW}[org_tubepress_api_const_options_names_Meta::DESCRIPTION]): ?>

          <dt class="tubepress_meta tubepress_meta_description"><?php echo ${org_tubepress_api_const_template_Variable::META_LABELS}[org_tubepress_api_const_options_names_Meta::DESCRIPTION]; ?></dt><dd class="tubepress_meta tubepress_meta_description"><?php echo $video->getDescription(); ?></dd>
          <?php endif; ?>

        </dl>
      </div>
      <?php endforeach; ?>

    </div>
    <?php if (isset(${org_tubepress_api_const_template_Variable::PAGINATION_BOTTOM})) : echo ${org_tubepress_api_const_template_Variable::PAGINATION_BOTTOM}; endif; ?>
  </div>
</div>
