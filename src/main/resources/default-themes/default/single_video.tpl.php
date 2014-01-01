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

<div class="tubepress_single_video">
    
    <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_api_const_options_names_Meta::TITLE]): ?>
        <div class="tubepress_embedded_title"><?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, "UTF-8"); ?></div>
    <?php endif; ?>
    
    <?php echo ${tubepress_api_const_template_Variable::EMBEDDED_SOURCE}; ?>

    <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>px">
      
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
        
    <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_addons_youtube_api_const_options_names_Meta::RATINGS] &&
        $video->getRatingCount() != ""):
    ?>
     
    <dt class="tubepress_meta tubepress_meta_ratings"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_addons_youtube_api_const_options_names_Meta::RATINGS]; ?></dt><dd class="tubepress_meta tubepress_meta_ratings"><?php echo $video->getRatingCount(); ?></dd>
    <?php endif; ?>
    
    <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_addons_vimeo_api_const_options_names_Meta::LIKES] &&
              $video->getLikesCount() != ""):
          ?>
           
          <dt class="tubepress_meta tubepress_meta_likes"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_addons_vimeo_api_const_options_names_Meta::LIKES]; ?></dt><dd class="tubepress_meta tubepress_meta_likes"><?php echo $video->getLikesCount(); ?></dd>
          <?php endif; ?>
        
    <?php if (${tubepress_api_const_template_Variable::META_SHOULD_SHOW}[tubepress_addons_youtube_api_const_options_names_Meta::RATING] &&
         $video->getRatingAverage() != ""):
     ?>
    
    <dt class="tubepress_meta tubepress_meta_rating"><?php echo ${tubepress_api_const_template_Variable::META_LABELS}[tubepress_addons_youtube_api_const_options_names_Meta::RATING]; ?></dt><dd class="tubepress_meta tubepress_meta_rating"><?php echo $video->getRatingAverage(); ?></dd>
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
