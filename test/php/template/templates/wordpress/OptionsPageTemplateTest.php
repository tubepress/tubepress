<?php
require_once BASE . '/sys/classes/org/tubepress/impl/bootstrap/TubePressBootstrapper.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/AbstractFormHandler.class.php';

class org_tubepress_impl_template_templates_wordpress_OptionsPageTemplateTest extends TubePressUnitTest {

    public function test()
    {
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE}    = '<<template-var-title>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER}    = '<<template-var-filter>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO}    = '<<template-var-intro>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID}    = '<<template-var-saveid>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT}    = '<<template-var-savetext>>';
        ${org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS}    = '<<template-var-tabs>>';


        ob_start();
        include BASE . '/sys/ui/templates/wordpress/options_page.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_removeNewLines($this->_expected()), $this->_removeNewLines($result));
    }

    private function _removeNewLines($string)
    {
        return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
    }

    private function _expected()
    {
        return <<<EOT
<div class="wrap">

	<form method="post">

    	<h2><<template-var-title>></h2>

    	<div style="margin-bottom: 1em; width: 60%; float: left">
    	    <<template-var-intro>>    	</div>

    	<<template-var-filter>>
    	<<template-var-tabs>>
    	<br />
    	<input type="submit" name="<<template-var-saveid>>" class="button-primary" value="<<template-var-savetext>>" />
    	<br /><br />

	</form>
</div>

EOT;
    }

}