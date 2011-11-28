<?php
require_once dirname(__FILE__) . '/../../../../includes/TubePressUnitTest.php';
require_once 'TabsTemplateTest.php';
require_once 'TabTemplateTest.php';
require_once 'fields/TextFieldTemplateTest.php';
require_once 'fields/CheckboxFieldTemplateTest.php';
require_once 'fields/ColorFieldTemplateTest.php';
require_once 'fields/DropdownFieldTemplateTest.php';
require_once 'fields/MultiSelectTemplateTest.php';

class org_tubepress_impl_template_templates_optionspage_OptionsPageTemplateTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(

            'org_tubepress_impl_template_templates_optionspage_TabsTemplateTest',
    		'org_tubepress_impl_template_templates_optionspage_TabTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_fields_TextFieldTemplateTest',
    		'org_tubepress_impl_template_templates_optionspage_fields_CheckboxFieldTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_fields_ColorFieldTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_fields_DropdownFieldTemplateTest',
			'org_tubepress_impl_template_templates_optionspage_fields_MultiSelectTemplateTest',
       	));
	}
}

