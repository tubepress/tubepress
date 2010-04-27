<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'org/tubepress/cache/CacheTests.php';
require_once 'org/tubepress/embedded/EmbeddedTests.php';
require_once 'org/tubepress/gallery/TubePressGalleryTests.php';
require_once 'org/tubepress/log/LogTests.php';
require_once 'org/tubepress/video/feed/inspection/InspectionTests.php';
require_once 'org/tubepress/video/feed/retrieval/RetrievalTests.php';
require_once 'org/tubepress/video/feed/provider/ProviderTests.php';
require_once 'org/tubepress/ioc/IocTests.php';
require_once 'org/tubepress/message/MessageTests.php';
require_once 'org/tubepress/pagination/PaginationTests.php';
require_once 'org/tubepress/player/PlayerTests.php';
require_once 'org/tubepress/querystring/QueryStringTests.php';
require_once 'org/tubepress/shortcode/ShortcodeTests.php';
require_once 'org/tubepress/template/TemplateTests.php';
require_once 'org/tubepress/url/UrlTests.php';
require_once 'org/tubepress/util/UtilsTests.php';
require_once 'org/tubepress/video/VideoTests.php';
require_once 'org/tubepress/options/OptionsTests.php';
require_once 'org/tubepress/browser/BrowserDetectorTests.php';
require_once 'org/tubepress/single/SingleVideoTests.php';

require_once 'net/sourceforge/phpvideotoolkit/PhpVideoToolkitTests.php';

class TubePressUnitTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Unit Tests");
		$suite->addTest(CacheTests::suite());
		$suite->addTest(EmbeddedTests::suite());
		$suite->addTest(TubePressGalleryTests::suite());
		$suite->addTest(LogTests::suite());
		$suite->addTest(IocTests::suite());
		$suite->addTest(MessageTests::suite());
		$suite->addTest(PaginationTests::suite());
		$suite->addTest(PlayerTests::suite());
		$suite->addTest(QueryStringTests::suite());
		$suite->addTest(ShortcodeTests::suite());
		$suite->addTest(TemplateTests::suite());
		$suite->addTest(UrlTests::suite());
		$suite->addTest(UtilsTests::suite());
		$suite->addTest(VideoTests::suite());
		$suite->addTest(OptionsTests::suite());
		$suite->addTest(InspectionTests::suite());
		$suite->addTest(RetrievalTests::suite());
		$suite->addTest(ProviderTests::suite());
		$suite->addTest(BrowserDetectorTests::suite());
		$suite->addTest(SingleVideoTests::suite());
		
		$suite->addTest(PhpVideoToolkitTests::suite());
		return $suite;
	}
}

?>
