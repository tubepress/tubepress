<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'WordPressStorageManagerTest.php';
require_once 'DefaultOptionValidatorTest.php';
require_once 'DefaultOptionDescriptorReferenceTest.php';
require_once 'ui/DefaultTabsHandlerTest.php';
require_once 'ui/DefaultFieldBuilderTest.php';
require_once 'ui/fields/TextFieldTest.php';
require_once 'ui/fields/CheckboxFieldTest.php';
require_once 'ui/fields/ColorFieldTest.php';
require_once 'ui/fields/DropdownFieldTest.php';
require_once 'ui/fields/ThemeFieldTest.php';
require_once 'ui/fields/MetaMultiSelectFieldTest.php';
require_once 'ui/fields/FilterMultiSelectFieldTest.php';
require_once 'ui/tabs/ThumbsTabTest.php';
require_once 'ui/tabs/AdvancedTabTest.php';
require_once 'ui/tabs/CacheTabTest.php';
require_once 'ui/tabs/EmbeddedTabTest.php';
require_once 'ui/tabs/FeedTabTest.php';
require_once 'ui/tabs/MetaTabTest.php';
require_once 'ui/tabs/ThemeTabTest.php';
require_once 'ui/tabs/GallerySourceTabTest.php';

class org_tubepress_impl_options_OptionsTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
 			'org_tubepress_impl_options_WordPressStorageManagerTest',
 			'org_tubepress_impl_options_DefaultOptionValidatorTest',
			'org_tubepress_impl_options_DefaultOptionDescriptorReferenceTest',
			'org_tubepress_impl_options_ui_DefaultTabsHandlerTest',
    		'org_tubepress_impl_options_ui_DefaultFieldBuilderTest',
			'org_tubepress_impl_options_ui_fields_TextFieldTest',
		    'org_tubepress_impl_options_ui_fields_CheckboxFieldTest',
    		'org_tubepress_impl_options_ui_fields_ColorFieldTest',
			'org_tubepress_impl_options_ui_fields_DropdownFieldTest',
			'org_tubepress_impl_options_ui_fields_ThemeFieldTest',
    		'org_tubepress_impl_options_ui_fields_MetaMultiSelectFieldTest',
    		'org_tubepress_impl_options_ui_fields_FilterMultiSelectFieldTest',
    		'org_tubepress_impl_options_ui_tabs_ThumbsTabTest',
			'org_tubepress_impl_options_ui_tabs_AdvancedTabTest',
    		'org_tubepress_impl_options_ui_tabs_CacheTabTest',
    		'org_tubepress_impl_options_ui_tabs_EmbeddedTabTest',
    		'org_tubepress_impl_options_ui_tabs_FeedTabTest',
    		'org_tubepress_impl_options_ui_tabs_MetaTabTest',
    		'org_tubepress_impl_options_ui_tabs_ThemeTabTest',
    		'org_tubepress_impl_options_ui_tabs_GallerySourceTabTest',
		));
	}
}

