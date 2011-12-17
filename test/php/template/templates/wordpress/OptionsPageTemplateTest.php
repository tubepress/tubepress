<?php
require_once BASE . '/sys/classes/org/tubepress/impl/bootstrap/TubePressBootstrapper.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/AbstractFormHandler.class.php';

class org_tubepress_impl_template_templates_wordpress_OptionsPageTemplateTest extends TubePressUnitTest {

    public function test()
    {
        $filter = \Mockery::mock(org_tubepress_spi_options_ui_Field::__);
        $filter->shouldReceive('getTitle')->once()->andReturn('filter-title');
        $filter->shouldReceive('getHtml')->once()->andReturn('filter-html');
        
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE}    = '<<template-var-title>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}    = $filter;
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO}    = '<<template-var-intro>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID}    = '<<template-var-saveid>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT}    = '<<template-var-savetext>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS}    = '<<template-var-tabs>>';


        ob_start();
        include BASE . '/sys/ui/templates/wordpress/options_page.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_removeTabs($this->_removeNewLines($this->_expected())), $this->_removeTabs($this->_removeNewLines($result)));
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
<div class="wrap">   <form method="post">       <h2><<template-var-title>></h2>       <div style="margin-bottom: 1em; width: 100%; float: left">           <div style="float: left; width: 59%">               <<template-var-intro>>           </div>           <div style="float: right">                   filter-title filter-html           </div>       </div>       <<template-var-tabs>>       <br />       <input type="submit" name="<<template-var-saveid>>" class="button-primary" value="<<template-var-savetext>>" />       <br /><br />   </form></div><script type="text/javascript">   jQuery(document).ready(function () {      var normalizeProviderName = function (raw) {         var normal = raw.replace('show', '').replace('Options', '');         return 'tubepress-' + normal.toLowerCase() + '-option';      },      doShowAndHide = function (arrayOfSelected, arrayOfPossible) {         var selector = '';         for (var i = 0; i < arrayOfPossible.length; i++) {            if (i != 0) {               selector += ', ';            }                        selector += '.' + arrayOfPossible[i];         }         jQuery(selector).each(function () {            var element = jQuery(this);                        for (var x = 0; x < arrayOfSelected.length; x++) {               if (element.hasClass(arrayOfSelected[x])) {                  element.show();                  return;               }            }            element.hide();                     });      },      filterHandler = function () {         //get the selected classes         var selected = jQuery('#multiselect-filterdropdown option:selected').map(function (e) {            return normalizeProviderName(jQuery(this).val());         }),         //get all the classes         allPossible = jQuery('#multiselect-filterdropdown option').map(function (e) {            return normalizeProviderName(jQuery(this).val());         });         //run it, yo         doShowAndHide(selected, allPossible);               };      //make the multi-selects      jQuery('#multiselect-filterdropdown').multiselect({         selectedText : 'choose...'      });            jQuery('#multiselect-metadropdown').multiselect({         selectedText : 'choose...',         height: 350      });      //bind to value changes on the filter drop-down      jQuery('#multiselect-filterdropdown').change(filterHandler);      //filter based on what's in the drop-down      filterHandler();   });</script>

EOT;
    }

}