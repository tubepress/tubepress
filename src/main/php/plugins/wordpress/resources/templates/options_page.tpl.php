<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
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
    	    <div style="float: left; width: 55%">
    	        <?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO}; ?>
    	    </div>
            <div style="float: right; width: 35%; text-align: right">
				<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}->getTitle() ?> <?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}->getHtml(); ?>
    	    </div>
    	</div>


    	<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS}; ?>

    	<input id="tubepress-submit-button" type="submit" name="<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID}; ?>" class="button-primary" value="<?php echo ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT}; ?>" />

        <div id="tubepress-box-holder"></div>

		<?php
			
			//http://codex.wordpress.org/Function_Reference/wp_nonce_field
			wp_nonce_field('tubepress-save', 'tubepress-nonce');
		?>

	</form>
</div>

<script type="text/javascript">
    var TubePressBoxes = <?php echo ${tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler::TEMPLATE_VAR_BOX_ARRAY}; ?>;
    var TubePressOptionFilter = (function () {

                'use strict';

                var normalizeProviderName = function (raw) {

                            var normal = raw.replace('show', '').replace('Options', '');

                            return 'tubepress-participant-' + normal.toLowerCase();
                        },

                        doShowAndHide = function (arrayOfSelected, arrayOfPossible) {

                            var selector = '', i;

                            for (i = 0; i < arrayOfPossible.length; i += 1) {

                                if (i !== 0) {

                                    selector += ', ';
                                }

                                selector += '.' + arrayOfPossible[i];
                            }

                            jQuery(selector).each(function () {

                                var element = jQuery(this), x;

                                for (x = 0; x < arrayOfSelected.length; x += 1) {

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
                        },

                        init = function () {

                            var multiSelect = jQuery('#multiselect-disabledOptionsPageParticipants');

                            //make the multi-selects
                            multiSelect.multiselect({

                                selectedText : 'choose...'
                            });

                            jQuery('#multiselect-metadropdown').multiselect({

                                selectedText : 'choose...',
                                height: 350
                            });

                            //bind to value changes on the filter drop-down
                            multiSelect.change(filterHandler);

                            //filter based on what's in the drop-down
                            filterHandler();
                        };

                return {

                    init : init
                };

            }()),

            TubePressBoxHandler = (function () {

                'use strict';

                //http://www.aaronpeters.nl/blog/iframe-loading-techniques-performance

                var boxParentDiv = '#tubepress-box-holder',

                        createAndAppendIframe = function (title) {

                            var titleDiv = jQuery('<div/>', {

                                    'class' : 'ui-widget ui-widget-header tubepress-participant-header',
                                    'style' : 'margin-bottom: 0'

                                }).append(jQuery('<span/>').html(title)),

                                iframe = jQuery('<iframe/>');

                            jQuery('<div/>', {

                                'class' : 'ui-corner-all ui-widget-content tubepress-box'

                            }).append(titleDiv).append(iframe).appendTo(boxParentDiv);

                            return iframe[0];
                        },

                        writeUrlInIframe = function (iframeElement, url) {

                            var doc = iframeElement.contentWindow.document;

                            doc.open().write('<body onload="var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + url.replace(/\//g, '\\/') + '\'">');

                            doc.close();
                        },

                        load = function (box) {

                            var iframe = createAndAppendIframe(box.title);

                            writeUrlInIframe(iframe, box.url);
                        },

                        init = function () {

                            if (window.TubePressBoxes === undefined) {

                                return;
                            }

                            var i;

                            for (i = 0; i < TubePressBoxes.length; i += 1) {

                                load(TubePressBoxes[i]);
                            }
                        };

                return {

                    init : init
                };

            }());

    jQuery(document).ready(function () {

        'use strict';

        TubePressOptionFilter.init();
        TubePressBoxHandler.init();
    });
</script>
