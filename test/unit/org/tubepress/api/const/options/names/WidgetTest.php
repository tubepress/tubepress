<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Widget.class.php';

class org_tubepress_api_const_options_names_WidgetTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('widget-tagstring', 'widget-title');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_names_Widget');
        
        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
        
    }
}