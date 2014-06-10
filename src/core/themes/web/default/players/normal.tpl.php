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
<div class="tubepress_normal_embedded_wrapper" style="width: <?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>px">
  
    <div id="tubepress_embedded_title_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>" class="tubepress_embedded_title">
      <?php echo ${tubepress_core_html_single_api_Constants::TEMPLATE_VAR_MEDIA_ITEM}->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE); ?>
    
    </div>
    <div id="tubepress_embedded_object_<?php echo ${tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID}; ?>">
      <?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_SOURCE}; ?>
    
    </div>
  </div>
