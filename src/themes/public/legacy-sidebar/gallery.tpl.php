<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>

<div class="tubepress_container tubepress_sidebar" id="tubepress_gallery_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>">

    <?php echo ${tubepress_api_const_template_Variable::PLAYER_HTML}; ?>

    <div id="tubepress_gallery_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>_thumbnail_area" class="tubepress_thumbnail_area">

        <?php if (isset(${tubepress_api_const_template_Variable::PAGINATION_TOP})) : echo ${tubepress_api_const_template_Variable::PAGINATION_TOP}; endif; ?>

        <div class="tubepress_thumbs">
            <?php foreach (${tubepress_api_const_template_Variable::VIDEO_ARRAY} as $video): ?>

                <div class="tubepress_thumb">
                    <a id="tubepress_image_<?php echo $video->getId(); ?>_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${tubepress_api_const_template_Variable::PLAYER_NAME}; ?>_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>">
                        <img alt="<?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?>" src="<?php echo $video->getThumbnailUrl(); ?>" width="<?php echo ${tubepress_api_const_template_Variable::THUMBNAIL_WIDTH}; ?>" height="<?php echo ${tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT}; ?>" />
                    </a>
                    <dl class="tubepress_meta_group" style="width: 100%">

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::TITLE]): ?>
                            <dt class="tubepress_meta tubepress_meta_title"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::TITLE]; ?></dt><dd class="tubepress_meta tubepress_meta_title"><a id="tubepress_title_<?php echo $video->getId(); ?>_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>" rel="tubepress_<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME}; ?>_<?php echo ${tubepress_api_const_template_Variable::PLAYER_NAME}; ?>_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>"><?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?></a></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::LENGTH]): ?>

                            <dt class="tubepress_meta tubepress_meta_runtime"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::LENGTH]; ?></dt><dd class="tubepress_meta tubepress_meta_runtime"><?php echo $video->getDuration(); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::AUTHOR]): ?>

                            <dt class="tubepress_meta tubepress_meta_author"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::AUTHOR]; ?></dt><dd class="tubepress_meta tubepress_meta_author"><?php echo $video->getAuthorDisplayName(); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::KEYWORDS]): ?>

                            <dt class="tubepress_meta tubepress_meta_keywords"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::KEYWORDS]; ?></dt><dd class="tubepress_meta tubepress_meta_keywords"><?php echo $raw = htmlspecialchars(implode(" ", $video->getKeywords()), ENT_QUOTES, "UTF-8"); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::URL]): ?>

                            <dt class="tubepress_meta tubepress_meta_url"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::URL]; ?></dt><dd class="tubepress_meta tubepress_meta_url"><a rel="external nofollow" href="<?php echo $video->getHomeUrl(); ?>"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::URL]; ?></a></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::CATEGORY] &&
                            $video->getCategory() != ""):
                            ?>

                            <dt class="tubepress_meta tubepress_meta_category"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::CATEGORY]; ?></dt><dd class="tubepress_meta tubepress_meta_category"><?php echo htmlspecialchars($video->getCategory(), ENT_QUOTES, "UTF-8"); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_addons_vimeo_api_const_options_names_Meta::LIKES] &&
                            $video->getLikesCount() != ""):
                            ?>

                            <dt class="tubepress_meta tubepress_meta_likes"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_addons_vimeo_api_const_options_names_Meta::LIKES]; ?></dt><dd class="tubepress_meta tubepress_meta_likes"><?php echo $video->getLikesCount(); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::ID]): ?>

                            <dt class="tubepress_meta tubepress_meta_id"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::ID]; ?></dt><dd class="tubepress_meta tubepress_meta_id"><?php echo $video->getId(); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::VIEWS]): ?>

                            <dt class="tubepress_meta tubepress_meta_views"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::VIEWS]; ?></dt><dd class="tubepress_meta tubepress_meta_views"><?php echo $video->getViewCount(); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::UPLOADED]): ?>

                            <dt class="tubepress_meta tubepress_meta_uploaddate"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::UPLOADED]; ?></dt><dd class="tubepress_meta tubepress_meta_uploaddate"><?php echo $video->getTimePublished(); ?></dd>
                        <?php endif; ?>

                        <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::DESCRIPTION]): ?>

                            <dt class="tubepress_meta tubepress_meta_description"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_api_const_options_names_Meta::DESCRIPTION]; ?></dt><dd class="tubepress_meta tubepress_meta_description"><?php echo $video->getDescription(); ?></dd>
                        <?php endif; ?>

                    </dl>
                </div>
            <?php endforeach; ?>

        </div>
        <?php if (isset(${tubepress_api_const_template_Variable::PAGINATION_BOTTOM})) : echo ${tubepress_api_const_template_Variable::PAGINATION_BOTTOM}; endif; ?>
    </div>
</div>