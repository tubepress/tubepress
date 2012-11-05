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

        ob_start();
        include __DIR__ . '/../../../../../../../main/php/plugins/addon/wordpress/resources/templates/options_page.tpl.php';
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
<div class="wrap">   <form method="post">       <h2><<template-var-title>></h2>       <div style="margin-bottom: 1em; width: 100%; float: left">           <div style="float: left; width: 59%">               <<template-var-intro>>           </div>           <div style="float: right">                   filter-title filter-html           </div>       </div>       <<template-var-tabs>>       <br />       <input type="submit" name="<<template-var-saveid>>" class="button-primary" value="<<template-var-savetext>>" />       <br /><br />      nonce   </form></div><script type="text/javascript">   jQuery(document).ready(function () {      var normalizeProviderName = function (raw) {         var normal = raw.replace('show', '').replace('Options', '');         return 'tubepress-participant-' + normal.toLowerCase();      },      doShowAndHide = function (arrayOfSelected, arrayOfPossible) {         var selector = '';         for (var i = 0; i < arrayOfPossible.length; i++) {            if (i != 0) {               selector += ', ';            }            selector += '.' + arrayOfPossible[i];         }         jQuery(selector).each(function () {            var element = jQuery(this);            for (var x = 0; x < arrayOfSelected.length; x++) {               if (element.hasClass(arrayOfSelected[x])) {                  element.show();                  return;               }            }            element.hide();         });      },      filterHandler = function () {         //get the selected classes         var selected = jQuery('#multiselect-disabledOptionsPageParticipants option:selected').map(function (e) {            return normalizeProviderName(jQuery(this).val());         }),         //get all the classes         allPossible = jQuery('#multiselect-disabledOptionsPageParticipants option').map(function (e) {            return normalizeProviderName(jQuery(this).val());         });         //run it, yo         doShowAndHide(selected, allPossible);      };      //make the multi-selects      jQuery('#multiselect-disabledOptionsPageParticipants').multiselect({         selectedText : 'choose...'      });      jQuery('#multiselect-metadropdown').multiselect({         selectedText : 'choose...',         height: 350      });      //bind to value changes on the filter drop-down      jQuery('#multiselect-disabledOptionsPageParticipants').change(filterHandler);      //filter based on what's in the drop-down      filterHandler();   });</script>
EOT;
    }

}