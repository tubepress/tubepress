<?php

require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
include dirname(__FILE__) . "/../../tubepress_classloader.php";

$testNames = array(
	"SimpleTubePressCacheService",
	"SimpleTubePressFeedInspectionService",
	"SimpleTubePressOptionsManager",
	"SimpleTubePressQueryStringService",
	"SimpleTubePressThumbnailService",
	"SimpleTubePressUrlBuilder",
	"SimpleTubePressVideoFactory",
	"TPGreyBoxPlayer",
	"TPlightWindowPlayer",
	"TPNormalPlayer",
	"TPShadowBoxPlayer",
	"TPYouTubePlayer",
	"TubePressAdvancedOptions",
	"TubePressDisplayOptions",
	"TubePressEmbeddedOptions",
	"TubePressEmbeddedPlayer",
	"TubePressFeedRetrievalService_HTTP_Request",
	"TubePressGalleryOptions",
	"TubePressGallery",
	"TubePressMetaOptions",
	"TubePressOptionsForm",
	"TubePressPaginationService_DiggStyle",
	"TubePressShortcode",
	"TubePressStringUtils",
	"TubePressValidator",
	"TubePressVideo",
	"TubePressWidgetGallery",
	"TubePressWidgetOptions",
	"WordPressStorageManager"
);

foreach ($testNames as $test) {
	require_once "tests/" . $test . "Test.php";
}

function __($key) {
	return $key;
}

class TubePressTestSuite
{
	public static function suite()
	{
		global $testNames;
		$suite = new PHPUnit_Framework_TestSuite("TubePress Tests");
		foreach ($testNames as $test) {
			$suite->addTestSuite($test . "Test");
		}
		return $suite;
	}
}

?>
