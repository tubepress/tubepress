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
<p>
<label for="tubepress-widget-title"><?php echo ${tubepress_addons_wordpress_impl_DefaultWidgetHandler::WIDGET_CONTROL_TITLE}; ?><input class="widefat" id="tubepress-widget-title" name="tubepress-widget-title" type="text" value="<?php echo ${tubepress_addons_wordpress_impl_DefaultWidgetHandler::WIDGET_TITLE}; ?>" /></label>
</p>
<p>
<label for="tubepress-widget-tagstring"><?php echo ${tubepress_addons_wordpress_impl_DefaultWidgetHandler::WIDGET_CONTROL_SHORTCODE}; ?>
<textarea class="widefat" rows="9" cols="12" id="tubepress-widget-tagstring" name="tubepress-widget-tagstring"><?php echo ${tubepress_addons_wordpress_impl_DefaultWidgetHandler::WIDGET_SHORTCODE}; ?></textarea>
</label>
</p>
<input type="hidden" id="<?php echo tubepress_addons_wordpress_impl_DefaultWidgetHandler::WIDGET_SUBMIT_TAG; ?>" name="<?php echo tubepress_addons_wordpress_impl_DefaultWidgetHandler::WIDGET_SUBMIT_TAG; ?>" value="1" />
<?php

//http://codex.wordpress.org/Function_Reference/wp_nonce_field
wp_nonce_field('tubepress-widget-nonce-save', 'tubepress-widget-nonce');
