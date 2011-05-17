<?php
require_once 'org/tubepress/api/const/options/OptionsConstantTests.php';
require_once 'org/tubepress/api/provider/ProviderApiTests.php';
require_once 'org/tubepress/api/urls/UrlTests.php';
require_once 'org/tubepress/api/video/VideoTests.php';
require_once 'org/tubepress/impl/bootstrap/BootstrapTests.php';
require_once 'org/tubepress/impl/cache/CacheTests.php';
require_once 'org/tubepress/impl/embedded/EmbeddedPlayerTests.php';
require_once 'org/tubepress/impl/env/EnvTests.php';
require_once 'org/tubepress/impl/environment/EnvironmentTests.php';
require_once 'org/tubepress/impl/factory/FactoryTests.php';
require_once 'org/tubepress/impl/feed/FeedTests.php';
require_once 'org/tubepress/impl/filesystem/ExplorerTests.php';
require_once 'org/tubepress/impl/html/HtmlTests.php';
require_once 'org/tubepress/impl/http/HttpTests.php';
require_once 'org/tubepress/impl/ioc/IocTests.php';
require_once 'org/tubepress/impl/log/LogTests.php';
require_once 'org/tubepress/impl/message/MessageTests.php';
require_once 'org/tubepress/impl/options/OptionsTests.php';
require_once 'org/tubepress/impl/patterns/PatternsTests.php';
require_once 'org/tubepress/impl/player/PlayerTests.php';
require_once 'org/tubepress/impl/plugin/PluginTests.php';
require_once 'org/tubepress/impl/provider/ProviderTests.php';
require_once 'org/tubepress/impl/querystring/QueryStringTests.php';
require_once 'org/tubepress/impl/shortcode/ShortcodeTests.php';
require_once 'org/tubepress/impl/template/TemplateTests.php';
require_once 'org/tubepress/impl/theme/ThemeHandlerTests.php';
require_once 'org/tubepress/impl/url/UrlTests.php';
require_once 'org/tubepress/impl/util/UtilsTests.php';

class TubePressUnitTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Unit Tests");

		/** API */
		$suite->addTest(OptionsApiTests::suite());
		$suite->addTest(ProviderApiTests::suite());
		$suite->addTest(UrlApiTests::suite());
		$suite->addTest(VideoTests::suite());

		/** Impl */
		$suite->addTest(BootstrapTests::suite());
		$suite->addTest(CacheTests::suite());
		$suite->addTest(EmbeddedPlayerTests::suite());
		$suite->addTest(EnvTests::suite());
		$suite->addTest(EnvironmentTests::suite());
		$suite->addTest(FactoryTests::suite());
		$suite->addTest(FeedTests::suite());
		$suite->addTest(ExplorerTests::suite());
		$suite->addTest(HtmlTests::suite());
		$suite->addTest(HttpTests::suite());
		$suite->addTest(IocTests::suite());
		$suite->addTest(LogTests::suite());
		$suite->addTest(MessageTests::suite());
		$suite->addTest(OptionsTests::suite());
		$suite->addTest(PatternsTests::suite());
		$suite->addTest(PlayerTests::suite());
		$suite->addTest(PluginTests::suite());
		$suite->addTest(ProviderTests::suite());
		$suite->addTest(QueryStringTests::suite());
		$suite->addTest(ShortcodeTests::suite());
		$suite->addTest(TemplateTests::suite());
		$suite->addTest(ThemeHandlerTests::suite());
		$suite->addTest(UrlTests::suite());
		$suite->addTest(UtilsTests::suite());

		return $suite;
	}
}
