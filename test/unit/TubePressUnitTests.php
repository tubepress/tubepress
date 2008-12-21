<?php

require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
include dirname(__FILE__) . "/../../tubepress_classloader.php";

$testNames = array(
	"SimpleTubePressCacheService",
	"SimpleTubePressFeedInspectionService",
	"SimpleTubePressOptionsManager",
	"SimpleTubePressQueryStringService",
	"SimpleTubePressShortcodeService",
	"SimpleTubePressThumbnailService",
	"SimpleTubePressUrlBuilder",
	"SimpleTubePressInputValidationService",
	"SimpleTubePressVideoFactory",
	"TPGreyBoxPlayer",
	"TPlightWindowPlayer",
	"TPNormalPlayer",
	"TPShadowBoxPlayer",
	"TPYouTubePlayer",
	"TubePressAdvancedOptions",
	"TubePressDisplayOptions",
	"TubePressEmbeddedOptions",
	"SimpleTubePressEmbeddedPlayerService",
	"TubePressFeedRetrievalService_HTTP_Request2",
	"TubePressGalleryOptions",
	"TubePressGalleryUnit",
	"TubePressMetaOptions",
	"TubePressOptionsForm",
	"TubePressPaginationService_DiggStyle",
	"TubePressStringUtils",
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
