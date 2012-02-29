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

		<?php
			
			//http://codex.wordpress.org/Function_Reference/wp_nonce_field
			wp_nonce_field('tubepress-save', 'tubepress-nonce');
		?>

	</form>
</div>

<script type="text/javascript">

	jQuery(document).ready(function () {

		var normalizeProviderName = function (raw) {

			var normal = raw.replace('show', '').replace('Options', '');

			return 'tubepress-' + normal.toLowerCase() + '-option';
		},

		doShowAndHide = function (arrayOfSelected, arrayOfPossible) {

			var selector = '';

			for (var i = 0; i < arrayOfPossible.length; i++) {

				if (i != 0) {

					selector += ', ';
				}
				
				selector += '.' + arrayOfPossible[i];
			}

			jQuery(selector).each(function () {

				var element = jQuery(this);
				
				for (var x = 0; x < arrayOfSelected.length; x++) {

					if (element.hasClass(arrayOfSelected[x])) {

						element.show();
						return;
					}
				}

				element.hide();
				
			});
		},

		filterHandler = function () {

			//get the selected classes
			var selected = jQuery('#multiselect-filterdropdown option:selected').map(function (e) {

				return normalizeProviderName(jQuery(this).val());
			}),

			//get all the classes
			allPossible = jQuery('#multiselect-filterdropdown option').map(function (e) {

				return normalizeProviderName(jQuery(this).val());
			});

			//run it, yo
			doShowAndHide(selected, allPossible);			
		};

		//make the multi-selects
		jQuery('#multiselect-filterdropdown').multiselect({

			selectedText : 'choose...'
		});
		
		jQuery('#multiselect-metadropdown').multiselect({

			selectedText : 'choose...',
			height: 350
		});

		//bind to value changes on the filter drop-down
		jQuery('#multiselect-filterdropdown').change(filterHandler);

		//filter based on what's in the drop-down
		filterHandler();

		//add the asterisk to Pro options
		jQuery('.tubepress-pro-option th').append(' *');
	});
</script>
