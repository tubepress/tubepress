<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'strategies/YouTubeFactoryStrategyTest.php';
require_once 'DelegatingVideoFactoryTest.php';
require_once 'strategies/VimeoFactoryStrategyTest.php';
class FactoryTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Video factory tests");
        $suite->addTestSuite('org_tubepress_impl_factory_strategies_YouTubeFactoryStrategyTest');
        $suite->addTestSuite('org_tubepress_impl_factory_DelegatingVideoFactoryTest');
        $suite->addTestSuite('org_tubepress_impl_factory_strategies_VimeoFactoryStrategyTest');
        return $suite;
    }
}
