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
 */
?>
<p>
<label for="tubepress-widget-title"><?php echo ${tubepress_plugins_wordpress_impl_DefaultWidgetHandler::WIDGET_CONTROL_TITLE}; ?><input class="widefat" id="tubepress-widget-title" name="tubepress-widget-title" type="text" value="<?php echo ${tubepress_plugins_wordpress_impl_DefaultWidgetHandler::WIDGET_TITLE}; ?>" /></label>
</p>
<p>
<label for="tubepress-widget-tagstring"><?php echo ${tubepress_plugins_wordpress_impl_DefaultWidgetHandler::WIDGET_CONTROL_SHORTCODE}; ?>
<textarea class="widefat" rows="9" cols="12" id="tubepress-widget-tagstring" name="tubepress-widget-tagstring"><?php echo ${tubepress_plugins_wordpress_impl_DefaultWidgetHandler::WIDGET_SHORTCODE}; ?></textarea>
</label>
</p>
<input type="hidden" id="<?php echo tubepress_plugins_wordpress_impl_DefaultWidgetHandler::WIDGET_SUBMIT_TAG; ?>" name="<?php echo tubepress_plugins_wordpress_impl_DefaultWidgetHandler::WIDGET_SUBMIT_TAG; ?>" value="1" />
<?php

//http://codex.wordpress.org/Function_Reference/wp_nonce_field
wp_nonce_field('tubepress-widget-nonce-save', 'tubepress-widget-nonce');
