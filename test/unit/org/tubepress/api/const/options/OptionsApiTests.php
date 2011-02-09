<?php
require dirname(__FILE__) . '/../../../../../PhpUnitLoader.php';

require_once 'names/AdvancedTest.php';
require_once 'names/DisplayTest.php';
require_once 'names/EmbeddedTest.php';
require_once 'names/OutputTest.php';
require_once 'names/MetaTest.php';
require_once 'names/WidgetTest.php';
require_once 'names/FeedTest.php';
require_once 'TypeTest.php';
require_once 'CategoryNameTest.php';
require_once 'values/GalleryContentModeTest.php';
require_once 'values/GalleryContentModeValueTest.php';
require_once 'values/OutputModeValueTest.php';

class OptionsApiTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TubePress Options API Tests');
        $suite->addTestSuite('org_tubepress_api_const_options_names_AdvancedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_DisplayTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_EmbeddedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_OutputTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_MetaTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_WidgetTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_FeedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_CategoryNameTest');
        $suite->addTestSuite('org_tubepress_api_const_options_TypeTest');
        $suite->addTestSuite('org_tubepress_api_const_options_values_GalleryContentModeTest');
        $suite->addTestSuite('org_tubepress_api_const_options_values_GalleryContentModeValueTest');
        $suite->addTestSuite('org_tubepress_api_const_options_values_OutputModeValueTest');

        return $suite;
    }
}
?>
