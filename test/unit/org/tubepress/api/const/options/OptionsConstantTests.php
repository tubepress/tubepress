<?php
require_once dirname(__FILE__) . '/../../../../../../includes/TubePressUnitTest.php';

require_once 'names/AdvancedTest.php';
require_once 'names/DisplayTest.php';
require_once 'names/EmbeddedTest.php';
require_once 'names/OutputTest.php';
require_once 'names/MetaTest.php';
require_once 'names/WidgetTest.php';
require_once 'names/FeedTest.php';
require_once 'TypeTest.php';
require_once 'CategoryNameTest.php';
require_once 'values/ModeValueTest.php';
require_once 'values/OutputValueTest.php';

class OptionsApiTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TubePress Options Constant Tests');
        $suite->addTestSuite('org_tubepress_api_const_options_names_AdvancedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_DisplayTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_EmbeddedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_OutputTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_MetaTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_WidgetTest');
        $suite->addTestSuite('org_tubepress_api_const_options_names_FeedTest');
        $suite->addTestSuite('org_tubepress_api_const_options_CategoryNameTest');
        $suite->addTestSuite('org_tubepress_api_const_options_TypeTest');
        $suite->addTestSuite('org_tubepress_api_const_options_values_ModeValueTest');
        $suite->addTestSuite('org_tubepress_api_const_options_values_OutputValueTest');

        return $suite;
    }
}

