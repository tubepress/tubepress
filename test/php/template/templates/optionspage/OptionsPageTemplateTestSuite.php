<?php
require_once dirname(__FILE__) . '/../../../../includes/TubePressUnitTest.php';
require_once 'TabsTemplateTest.php';
require_once 'TabTemplateTest.php';
require_once 'widgets/TextInputTemplateTest.php';
require_once 'widgets/CheckboxInputTemplateTest.php';
require_once 'widgets/ColorInputTemplateTest.php';
require_once 'widgets/DropdownInputTemplateTest.php';
require_once 'widgets/MultiSelectTemplateTest.php';

class org_tubepress_impl_template_templates_optionspage_OptionsPageTemplateTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(

            'org_tubepress_impl_template_templates_optionspage_TabsTemplateTest',
    		'org_tubepress_impl_template_templates_optionspage_TabTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_widgets_TextInputTemplateTest',
    		'org_tubepress_impl_template_templates_optionspage_widgets_CheckboxInputTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_widgets_ColorInputTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_widgets_DropdownInputTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_widgets_MultiSelectTemplateTest',
       	));
	}
}

