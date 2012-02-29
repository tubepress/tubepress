<p>
<label for="tubepress-widget-title"><?php echo ${org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_TITLE}; ?><input class="widefat" id="tubepress-widget-title" name="tubepress-widget-title" type="text" value="<?php echo ${org_tubepress_impl_env_wordpress_Widget::WIDGET_TITLE}; ?>" /></label>
</p>
<p>
<label for="tubepress-widget-tagstring"><?php echo ${org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_SHORTCODE}; ?>
<textarea class="widefat" rows="9" cols="12" id="tubepress-widget-tagstring" name="tubepress-widget-tagstring"><?php echo ${org_tubepress_impl_env_wordpress_Widget::WIDGET_SHORTCODE}; ?></textarea>
</label>
</p>
<input type="hidden" id="<?php echo org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG; ?>" name="<?php echo org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG; ?>" value="1" />
<?php

	//http://codex.wordpress.org/Function_Reference/wp_nonce_field
	wp_nonce_field('tubepress-widget-nonce-save', 'tubepress-widget-nonce');
?>