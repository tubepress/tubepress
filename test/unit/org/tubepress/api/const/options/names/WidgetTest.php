<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Widget.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_names_WidgetTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('widget-tagstring', 'widget-title');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_names_Widget'), $expected);
    }
}
