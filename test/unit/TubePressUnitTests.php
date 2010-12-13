<?php
require 'PhpUnitLoader.php';
require_once 'org/tubepress/impl/cache/CacheTests.php';
require_once 'org/tubepress/impl/embedded/EmbeddedPlayerTests.php';
require_once 'org/tubepress/impl/gallery/GalleryTests.php';
require_once 'org/tubepress/log/LogTests.php';
require_once 'org/tubepress/video/feed/inspection/InspectionTests.php';
require_once 'org/tubepress/video/feed/retrieval/RetrievalTests.php';
require_once 'org/tubepress/video/feed/provider/ProviderTests.php';
require_once 'org/tubepress/ioc/IocTests.php';
require_once 'org/tubepress/impl/message/MessageTests.php';
require_once 'org/tubepress/impl/pagination/PaginationTests.php';
require_once 'org/tubepress/player/PlayerTests.php';
require_once 'org/tubepress/impl/querystring/QueryStringTests.php';
require_once 'org/tubepress/impl/shortcode/ShortcodeTests.php';
require_once 'org/tubepress/impl/template/TemplateTests.php';
require_once 'org/tubepress/url/UrlTests.php';
require_once 'org/tubepress/util/UtilsTests.php';
require_once 'org/tubepress/video/VideoTests.php';
require_once 'org/tubepress/options/OptionsTests.php';
require_once 'org/tubepress/impl/http/AgentDetectorTests.php';
require_once 'org/tubepress/single/SingleVideoTests.php';
require_once 'org/tubepress/impl/theme/ThemeHandlerTests.php';

class TubePressUnitTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Unit Tests");
		$suite->addTest(CacheTests::suite());
		$suite->addTest(EmbeddedPlayerTests::suite());
		$suite->addTest(GalleryTests::suite());
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
		$suite->addTest(AgentDetectorTests::suite());
		$suite->addTest(SingleVideoTests::suite());
		$suite->addTest(ThemeHandlerTests::suite());

		return $suite;
	}
}

?>
