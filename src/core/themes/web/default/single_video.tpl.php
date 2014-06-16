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
 * <div class="tubepress_single_video">
 *
 * The outer-most <div> for a TubePress media item. You may add additional class names to
 * the "class" attribute, but do not remove the existing "tubepress_single_video" class name.
 */
?>
<div class="tubepress_single_video">

    <?php
    /**
     * The following block prints out the media item's title, if requested. You may add to, but not remove,
     * the existing class names for each of the elements.
     */
    ?>
    <?php if (isset(${tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW}[tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE])): ?>
    <div class="tubepress_embedded_title">
        <?php echo htmlspecialchars($mediaItem->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE), ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php endif; ?>


    <?php
    /**
     * The following statement prints out any HTML required for the TubePress embedded media player. We do not recommend removing
     * this statement, though you may move it around the template if you'd like.
     */
    ?>
    <?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_SOURCE}; ?>

    <?php
    /**
     * <dl class="tubepress_meta_group" ...>
     *
     * This <dl> wraps each video's metadata (title, runtime, etc). You may add additional class names to
     * the "class" attribute, but do not remove the existing "tubepress_meta_group" class name.
     */
    ?>
    <dl class="tubepress_meta_group" style="width: <?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>px">
    
        <?php require dirname(__FILE__) . '/_fragments/dl_meta_group.fragment.php'; ?>
    
    </dl><?php //end of dl.tubepress_meta_group ?>
    
</div><?php //end of div.tubepress_single_video ?>
