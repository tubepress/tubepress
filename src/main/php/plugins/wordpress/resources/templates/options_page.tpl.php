<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<div class="wrap">

	<form method="post">

    	<h2><?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE}; ?></h2>

    	<div style="margin-bottom: 1em; width: 100%; float: left">
    	    <div style="float: left; width: 59%">
    	        <?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO}; ?>
    	    </div>
    	    <div style="float: right">	    
				<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}->getTitle() ?> <?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}->getHtml(); ?>
    	    </div>
    	</div>


    	<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS}; ?>

    	<br />
    	<input type="submit" name="<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID}; ?>" class="button-primary" value="<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT}; ?>" />
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

			return 'tubepress-participant-' + normal.toLowerCase();
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
			var selected = jQuery('#multiselect-disabledOptionsPageParticipants option:selected').map(function (e) {

				return normalizeProviderName(jQuery(this).val());
			}),

			//get all the classes
			allPossible = jQuery('#multiselect-disabledOptionsPageParticipants option').map(function (e) {

				return normalizeProviderName(jQuery(this).val());
			});

			//run it, yo
			doShowAndHide(selected, allPossible);
		};

		//make the multi-selects
		jQuery('#multiselect-disabledOptionsPageParticipants').multiselect({

			selectedText : 'choose...'
		});

		jQuery('#multiselect-metadropdown').multiselect({

			selectedText : 'choose...',
			height: 350
		});

		//bind to value changes on the filter drop-down
		jQuery('#multiselect-disabledOptionsPageParticipants').change(filterHandler);

		//filter based on what's in the drop-down
		filterHandler();
	});
</script>
