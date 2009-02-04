<?php

require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../classes/tubepress_classloader.php";

$testNames = array(
    "org_tubepress_options_reference_SimpleOptionsReference",
	"org_tubepress_cache_SimpleCacheService",
	"org_tubepress_gdata_inspection_SimpleFeedInspectionService",
	"org_tubepress_options_manager_SimpleOptionsManager",
	"org_tubepress_querystring_SimpleQueryStringService",
	"org_tubepress_shortcode_SimpleShortcodeService",
	"org_tubepress_thumbnail_SimpleThumbnailService",
	"org_tubepress_url_SimpleUrlBuilder",
	"org_tubepress_options_validation_SimpleInputValidationService",
	"org_tubepress_video_factory_SimpleVideoFactory",
	"org_tubepress_player_impl_GreyBoxPlayer",
	"org_tubepress_player_impl_LightWindowPlayer",
	"org_tubepress_player_impl_NormalPlayer",
	"org_tubepress_player_impl_ShadowBoxPlayer",
	"org_tubepress_player_impl_YouTubePlayer",
	"org_tubepress_options_category_Advanced",
	"org_tubepress_options_category_Display",
	"org_tubepress_options_category_Embedded",
	"org_tubepress_video_embed_SimpleEmbeddedPlayerService",
//	"org_tubepress_gdata_retrieval_HTTPRequest2",
	"org_tubepress_options_category_Gallery",
	"org_tubepress_gallery_Gallery",
	"org_tubepress_options_category_Meta",
	"org_tubepress_options_Form",
	"org_tubepress_pagination_DiggStylePaginationService",
	"org_tubepress_util_StringUtils",
	"org_tubepress_video_Video",
	"org_tubepress_gallery_WidgetGallery",
	"org_tubepress_options_category_Widget",
	"org_tubepress_options_storage_WordPressStorageManager"
);

foreach ($testNames as $test) {
	require_once "tests/" . $test . "Test.php";
}

function __($key) {
	return $key;
}

class TubePressUnitTests
{
	public static function suite()
	{
		global $testNames;
		$suite = new PHPUnit_Framework_TestSuite("TubePress Unit Tests");
		foreach ($testNames as $test) {
			$suite->addTestSuite($test . "Test");
		}
		return $suite;
	}
}

?>
