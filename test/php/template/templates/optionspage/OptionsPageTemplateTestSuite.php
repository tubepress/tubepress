<?php
require_once dirname(__FILE__) . '/../../../../includes/TubePressUnitTest.php';
require_once 'TabsTemplateTest.php';
require_once 'widgets/TextInputTemplateTest.php';

class org_tubepress_impl_template_templates_optionspage_OptionsPageTemplateTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(

            'org_tubepress_impl_template_templates_optionspage_TabsTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_widgets_TextInputTemplateTest',
       	));
	}
}

