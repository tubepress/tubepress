<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'WordPressStorageManagerTest.php';
require_once 'DefaultOptionValidatorTest.php';
require_once 'DefaultOptionDescriptorReferenceTest.php';
require_once 'ui/DefaultTabsHtmlGeneratorTest.php';
require_once 'ui/widgets/TextWidgetTest.php';

class org_tubepress_impl_options_OptionsTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_options_WordPressStorageManagerTest',
			'org_tubepress_impl_options_DefaultOptionValidatorTest',
			'org_tubepress_impl_options_DefaultOptionDescriptorReferenceTest',
			'org_tubepress_impl_options_ui_DefaultTabsHtmlGeneratorTest',
			'org_tubepress_impl_options_ui_widgets_TextWidgetTest',
		));
	}
}

