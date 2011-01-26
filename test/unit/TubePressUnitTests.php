<?php
require 'PhpUnitLoader.php';
require_once 'org/tubepress/api/const/options/OptionConstantsTests.php';
require_once 'org/tubepress/impl/bootstrap/BootstrapTests.php';
require_once 'org/tubepress/impl/cache/CacheTests.php';
require_once 'org/tubepress/impl/embedded/EmbeddedPlayerTests.php';
require_once 'org/tubepress/impl/environment/EnvironmentTests.php';
require_once 'org/tubepress/impl/factory/FactoryTests.php';
require_once 'org/tubepress/impl/feed/FeedTests.php';
require_once 'org/tubepress/impl/filesystem/ExplorerTests.php';
require_once 'org/tubepress/impl/gallery/GalleryTests.php';
require_once 'org/tubepress/impl/http/HttpTests.php';
require_once 'org/tubepress/impl/log/LogTests.php';
require_once 'org/tubepress/impl/message/MessageTests.php';
require_once 'org/tubepress/impl/options/OptionsTests.php';
require_once 'org/tubepress/impl/pagination/PaginationTests.php';
require_once 'org/tubepress/impl/patterns/PatternsTests.php';
require_once 'org/tubepress/impl/player/PlayerTests.php';
require_once 'org/tubepress/impl/provider/ProviderTests.php';
require_once 'org/tubepress/impl/querystring/QueryStringTests.php';
require_once 'org/tubepress/impl/shortcode/ShortcodeTests.php';
require_once 'org/tubepress/impl/single/SingleVideoTests.php';
require_once 'org/tubepress/impl/template/TemplateTests.php';
require_once 'org/tubepress/impl/theme/ThemeHandlerTests.php';
require_once 'org/tubepress/impl/url/UrlTests.php';
require_once 'org/tubepress/impl/ioc/IocTests.php';
require_once 'org/tubepress/impl/util/UtilsTests.php';
require_once 'org/tubepress/api/video/VideoTests.php';
require_once 'org/tubepress/impl/env/EnvTests.php';


class TubePressUnitTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Unit Tests");
		$suite->addTest(CacheTests::suite());
		$suite->addTest(EmbeddedPlayerTests::suite());
		$suite->addTest(BootstrapTests::suite());
		$suite->addTest(GalleryTests::suite());
		$suite->addTest(IocTests::suite());
		$suite->addTest(MessageTests::suite());
		$suite->addTest(PaginationTests::suite());
		$suite->addTest(PlayerTests::suite());
		$suite->addTest(QueryStringTests::suite());
		$suite->addTest(ShortcodeTests::suite());
		$suite->addTest(TemplateTests::suite());
		$suite->addTest(FeedTests::suite());
		$suite->addTest(UtilsTests::suite());
		$suite->addTest(VideoTests::suite());
		$suite->addTest(OptionsTests::suite());
		$suite->addTest(ProviderTests::suite());
		$suite->addTest(HttpTests::suite());
		$suite->addTest(SingleVideoTests::suite());
		$suite->addTest(ThemeHandlerTests::suite());
		$suite->addTest(OptionConstantsTests::suite());
		$suite->addTest(FactoryTests::suite());
		$suite->addTest(UrlTests::suite());
		$suite->addTest(ExplorerTests::suite());
		$suite->addTest(LogTests::suite());
		$suite->addTest(PatternsTests::suite());
		$suite->addTest(EnvironmentTests::suite());
		$suite->addTest(EnvTests::suite());

		return $suite;
	}
}

?>
