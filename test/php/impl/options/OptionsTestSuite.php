<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'WordPressStorageManagerTest.php';
require_once 'DefaultOptionValidatorTest.php';
require_once 'DefaultOptionDescriptorReferenceTest.php';
require_once 'ui/DefaultTabsHandlerTest.php';
require_once 'ui/DefaultWidgetBuilderTest.php';
require_once 'ui/widgets/TextWidgetTest.php';
require_once 'ui/widgets/CheckboxWidgetTest.php';
require_once 'ui/widgets/ColorWidgetTest.php';
require_once 'ui/widgets/DropdownWidgetTest.php';
require_once 'ui/widgets/ThemeWidgetTest.php';
require_once 'ui/widgets/MetaMultiSelectInputTest.php';
require_once 'ui/tabs/AppearanceTabTest.php';

class org_tubepress_impl_options_OptionsTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_options_WordPressStorageManagerTest',
			'org_tubepress_impl_options_DefaultOptionValidatorTest',
			'org_tubepress_impl_options_DefaultOptionDescriptorReferenceTest',
			'org_tubepress_impl_options_ui_DefaultTabsHandlerTest',
    		'org_tubepress_impl_options_ui_DefaultWidgetBuilderTest',
			'org_tubepress_impl_options_ui_widgets_TextWidgetTest',
		    'org_tubepress_impl_options_ui_widgets_CheckboxWidgetTest',
    		'org_tubepress_impl_options_ui_widgets_ColorWidgetTest',
			'org_tubepress_impl_options_ui_widgets_DropdownWidgetTest',
			'org_tubepress_impl_options_ui_widgets_ThemeWidgetTest',
    		'org_tubepress_impl_options_ui_widgets_MetaMultiSelectInputTest',
    		'org_tubepress_impl_options_ui_tabs_AppearanceTabTest',
		));
	}
}

