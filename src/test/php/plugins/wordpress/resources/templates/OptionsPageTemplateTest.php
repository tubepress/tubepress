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

if (! function_exists('wp_nonce_field')) {

    function wp_nonce_field() { echo 'nonce'; }
}

class tubepress_plugins_wordpress_resources_templates_OptionsPageTemplateTest extends TubePressUnitTest
{
    public function test()
    {
        $filter = \Mockery::mock('tubepress_spi_options_ui_Field');
        $filter->shouldReceive('getTitle')->once()->andReturn('filter-title');
        $filter->shouldReceive('getHtml')->once()->andReturn('filter-html');

        ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE}    = '<<template-var-title>>';
        ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}    = $filter;
        ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO}    = '<<template-var-intro>>';
        ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID}    = '<<template-var-saveid>>';
        ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT}    = '<<template-var-savetext>>';
        ${tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS}    = '<<template-var-tabs>>';
        ${tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler::TEMPLATE_VAR_BOX_ARRAY} = '<<<boxes>>>';

        ob_start();
        include __DIR__ . '/../../../../../../main/php/plugins/wordpress/resources/templates/options_page.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_removeTabs($this->_removeNewLines($this->_expected())), $this->_removeTabs($this->_removeNewLines($result)));
    }

    public function doNonce()
    {
        echo 'nonce';
    }

    private function _removeNewLines($string)
    {
        return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
    }

    private function _removeTabs($string)
    {
        return (string)str_replace("\t", '   ', $string);
    }

    private function _expected()
    {
        return <<<EOT
<div class="wrap">   <form method="post">       <h2><<template-var-title>></h2>       <div style="margin-bottom: 1em; width: 100%; float: left">           <div style="float: left; width: 55%">               <<template-var-intro>>           </div>            <div style="float: right; width: 35%; text-align: right">            filter-title filter-html           </div>       </div>       <<template-var-tabs>>       <input id="tubepress-submit-button" type="submit" name="<<template-var-saveid>>" class="button-primary" value="<<template-var-savetext>>" />        <div id="tubepress-box-holder"></div>      nonce   </form></div><script type="text/javascript">    var TubePressBoxes = <<<boxes>>>;    var TubePressOptionFilter = (function () {                'use strict';                var normalizeProviderName = function (raw) {                            var normal = raw.replace('show', '').replace('Options', '');                            return 'tubepress-participant-' + normal.toLowerCase();                        },                        doShowAndHide = function (arrayOfSelected, arrayOfPossible) {                            var selector = '', i;                            for (i = 0; i < arrayOfPossible.length; i += 1) {                                if (i !== 0) {                                    selector += ', ';                                }                                selector += '.' + arrayOfPossible[i];                            }                            jQuery(selector).each(function () {                                var element = jQuery(this), x;                                for (x = 0; x < arrayOfSelected.length; x += 1) {                                    if (element.hasClass(arrayOfSelected[x])) {                                        element.show();                                        return;                                    }                                }                                element.hide();                            });                        },                        filterHandler = function () {                            //get the selected classes                            var selected = jQuery('#multiselect-disabledOptionsPageParticipants option:selected').map(function (e) {                                        return normalizeProviderName(jQuery(this).val());                                    }),                            //get all the classes                                    allPossible = jQuery('#multiselect-disabledOptionsPageParticipants option').map(function (e) {                                        return normalizeProviderName(jQuery(this).val());                                    });                            //run it, yo                            doShowAndHide(selected, allPossible);                        },                        init = function () {                            var multiSelect = jQuery('#multiselect-disabledOptionsPageParticipants');                            //make the multi-selects                            multiSelect.multiselect({                                selectedText : 'choose...'                            });                            jQuery('#multiselect-metadropdown').multiselect({                                selectedText : 'choose...',                                height: 350                            });                            //bind to value changes on the filter drop-down                            multiSelect.change(filterHandler);                            //filter based on what's in the drop-down                            filterHandler();                        };                return {                    init : init                };            }()),            TubePressBoxHandler = (function () {                'use strict';                //http://www.aaronpeters.nl/blog/iframe-loading-techniques-performance                var boxParentDiv = '#tubepress-box-holder',                        createAndAppendIframe = function (title) {                            var titleDiv = jQuery('<div/>', {                                    'class' : 'ui-widget ui-widget-header tubepress-participant-header',                                    'style' : 'margin-bottom: 0'                                }).append(jQuery('<span/>').html(title)),                                iframe = jQuery('<iframe/>');                            jQuery('<div/>', {                                'class' : 'ui-corner-all ui-widget-content tubepress-box'                            }).append(titleDiv).append(iframe).appendTo(boxParentDiv);                            return iframe[0];                        },                        writeUrlInIframe = function (iframeElement, url) {                            var doc = iframeElement.contentWindow.document;                            doc.open().write('<body onload="var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + url.replace(/\//g, '\\\/') + '\'">');                            doc.close();                        },                        load = function (box) {                            var iframe = createAndAppendIframe(box.title);                            writeUrlInIframe(iframe, box.url);                        },                        init = function () {                            if (window.TubePressBoxes === undefined) {                                return;                            }                            var i;                            for (i = 0; i < TubePressBoxes.length; i += 1) {                                load(TubePressBoxes[i]);                            }                        };                return {                    init : init                };            }());    jQuery(document).ready(function () {        'use strict';        TubePressOptionFilter.init();        TubePressBoxHandler.init();    });</script>
EOT;
    }

}