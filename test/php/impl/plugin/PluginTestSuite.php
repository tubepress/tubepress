<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'filters/embeddedtemplate/CoreVariablesTest.php';
require_once 'filters/galleryinitjs/GalleryInitJsBaseParamsTest.php';
require_once 'filters/galleryhtml/GalleryJsTest.php';
require_once 'filters/gallerytemplate/PaginationTest.php';
require_once 'filters/gallerytemplate/CoreVariablesTest.php';
require_once 'filters/gallerytemplate/EmbeddedPlayerNameTest.php';
require_once 'filters/gallerytemplate/PlayerTest.php';
require_once 'filters/gallerytemplate/VideoMetaTest.php';
require_once 'filters/playertemplate/CoreVariablesTest.php';
require_once 'filters/providerresult/ResultCountCapperTest.php';
require_once 'filters/providerresult/PerPageSorterTest.php';
require_once 'filters/providerresult/VideoBlacklistTest.php';
require_once 'filters/providerresult/VideoPrependerTest.php';
require_once 'filters/searchinputtemplate/CoreVariablesTest.php';
require_once 'filters/singlevideotemplate/CoreVariablesTest.php';
require_once 'filters/singlevideotemplate/VideoMetaTest.php';
require_once 'listeners/WordPressBootTest.php';
require_once 'listeners/SkeletonExistsListenerTest.php';
require_once 'PluginManagerImplTest.php';

class org_tubepress_impl_plugin_PluginTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_plugin_PluginManagerImplTest',
		    'org_tubepress_impl_plugin_filters_embeddedtemplate_CoreVariablesTest',
		    'org_tubepress_impl_plugin_filters_galleryhtml_GalleryJsTest',
		    'org_tubepress_impl_plugin_filters_gallerytemplate_PaginationTest',
		    'org_tubepress_impl_plugin_filters_gallerytemplate_CoreVariablesTest',
		    'org_tubepress_impl_plugin_filters_gallerytemplate_EmbeddedPlayerNameTest',
		    'org_tubepress_impl_plugin_filters_gallerytemplate_PlayerTest',
		    'org_tubepress_impl_plugin_filters_gallerytemplate_VideoMetaTest',
		    'org_tubepress_impl_plugin_filters_playertemplate_CoreVariablesTest',
		    'org_tubepress_impl_plugin_filters_providerresult_ResultCountCapperTest',
		    'org_tubepress_impl_plugin_filters_providerresult_PerPageSorterTest',
    		'org_tubepress_impl_plugin_filters_providerresult_VideoBlacklistTest',
		    'org_tubepress_impl_plugin_filters_providerresult_VideoPrependerTest',
		    'org_tubepress_impl_plugin_filters_singlevideotemplate_CoreVariablesTest',
		    'org_tubepress_impl_plugin_filters_searchinputtemplate_CoreVariablesTest',
		    'org_tubepress_impl_plugin_filters_singlevideotemplate_VideoMetaTest',
		    'org_tubepress_impl_plugin_listeners_WordPressBootTest',
		    'org_tubepress_impl_plugin_listeners_SkeletonExistsListenerTest',
		    'org_tubepress_impl_plugin_filters_galleryinitjs_GalleryInitJsBaseParamsTest',
        ));
	}
}

