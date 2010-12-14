<?php
require dirname(__FILE__) . '/../../../../../PhpUnitLoader.php';

require_once 'AdvancedTest.php';
require_once 'DisplayTest.php';
require_once 'EmbeddedTest.php';
require_once 'GalleryTest.php';
require_once 'MetaTest.php';
require_once 'WidgetTest.php';
require_once 'FeedTest.php';

class OptionConstantsTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TubePress Options Constants Tests');
        $suite->addTestSuite('org_tubepress_api_const_options_AdvancedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_DisplayTest');
        $suite->addTestSuite('org_tubepress_api_const_options_EmbeddedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_GalleryTest');
        $suite->addTestSuite('org_tubepress_api_const_options_MetaTest');
        $suite->addTestSuite('org_tubepress_api_const_options_WidgetTest');
        $suite->addTestSuite('org_tubepress_api_const_options_FeedTest');

        return $suite;
    }
}
?>
