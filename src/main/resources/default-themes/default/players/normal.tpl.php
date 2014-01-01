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
<div class="tubepress_normal_embedded_wrapper" style="width: <?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>px">
  
    <div id="tubepress_embedded_title_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>" class="tubepress_embedded_title">
      <?php echo ${tubepress_api_const_template_Variable::VIDEO}->getTitle(); ?>
    
    </div>
    <div id="tubepress_embedded_object_<?php echo ${tubepress_api_const_template_Variable::GALLERY_ID}; ?>">
      <?php echo ${tubepress_api_const_template_Variable::EMBEDDED_SOURCE}; ?>
    
    </div>
  </div>
