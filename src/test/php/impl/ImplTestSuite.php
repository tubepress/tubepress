<?php

require_once 'bootstrap/BootstrapTestSuite.php';
require_once 'embedded/EmbeddedPlayerTestSuite.php';
require_once 'env/EnvTestSuite.php';
require_once 'environment/EnvironmentTestSuite.php';
require_once 'exec/ExecutionTestSuite.php';
require_once 'factory/FactoryTestSuite.php';
require_once 'feed/FeedTestSuite.php';
require_once 'html/HtmlTestSuite.php';
require_once 'http/HttpTestSuite.php';
require_once 'ioc/IocTestSuite.php';
require_once 'log/LogTestSuite.php';
require_once 'message/MessageTestSuite.php';
require_once 'options/OptionsTestSuite.php';
require_once 'player/PlayerTestSuite.php';
require_once 'plugin/PluginTestSuite.php';
require_once 'provider/ProviderTestSuite.php';
require_once 'querystring/QueryStringTestSuite.php';
require_once 'shortcode/ShortcodeTestSuite.php';
require_once 'theme/ThemeTestSuite.php';
require_once 'util/UtilsTestSuite.php';

class org_tubepress_impl_ImplTestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();

		$suite->addTest(org_tubepress_impl_bootstrap_BootstrapTestSuite::suite());
		$suite->addTest(org_tubepress_impl_embedded_EmbeddedPlayerTestSuite::suite());
		$suite->addTest(org_tubepress_impl_env_EnvTestSuite::suite());
		$suite->addTest(org_tubepress_impl_environment_EnvironmentTestSuite::suite());
		$suite->addTest(org_tubepress_impl_exec_ExecutionTestSuite::suite());
		$suite->addTest(org_tubepress_impl_factory_FactoryTestSuite::suite());
		$suite->addTest(org_tubepress_impl_feed_FeedTestSuite::suite());
		$suite->addTest(org_tubepress_impl_html_HtmlTestSuite::suite());
		$suite->addTest(org_tubepress_impl_http_HttpTestSuite::suite());
		$suite->addTest(org_tubepress_impl_ioc_IocTestSuite::suite());
		$suite->addTest(org_tubepress_impl_log_LogTestSuite::suite());
		$suite->addTest(org_tubepress_impl_message_MessageTestSuite::suite());
		$suite->addTest(org_tubepress_impl_options_OptionsTestSuite::suite());
		$suite->addTest(org_tubepress_impl_player_PlayerTestSuite::suite());
		$suite->addTest(org_tubepress_impl_plugin_PluginTestSuite::suite());
		$suite->addTest(org_tubepress_impl_provider_ProviderTestSuite::suite());
		$suite->addTest(org_tubepress_impl_querystring_QueryStringTestSuite::suite());
		$suite->addTest(org_tubepress_impl_shortcode_ShortcodeTestSuite::suite());
		$suite->addTest(org_tubepress_impl_theme_ThemeTestSuite::suite());
		$suite->addTest(org_tubepress_impl_util_UtilsTestSuite::suite());

		return $suite;
	}
}
