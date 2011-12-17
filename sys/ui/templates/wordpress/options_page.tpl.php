<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
<div class="wrap">

	<form method="post">

    	<h2><?php echo ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE}; ?></h2>

    	<div style="margin-bottom: 1em; width: 100%; float: left">
    	    <div style="float: left; width: 59%">
    	        <?php echo ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO}; ?>
    	    </div>
    	    <div style="float: right">	    
				<?php echo ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}->getTitle() ?> <?php echo ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}->getHtml(); ?>
    	    </div>
    	</div>


    	<?php echo ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS}; ?>

    	<br />
    	<input type="submit" name="<?php echo ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID}; ?>" class="button-primary" value="<?php echo ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT}; ?>" />
    	<br /><br />

	</form>
</div>

<script type="text/javascript">

	jQuery(document).ready(function () {

		var opts = {

				selectedText : 'choose...'
		};
		
		jQuery('#multiselect-filterdropdown').multiselect(opts);
		jQuery('#multiselect-metadropdown').multiselect(opts);
	});
</script>
