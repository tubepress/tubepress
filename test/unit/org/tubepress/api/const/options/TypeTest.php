<?php
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/const/options/Type.class.php';

class org_tubepress_api_const_options_TypeTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('boolean', 'color', 'integral', 'mode', 'order', 'output', 'player', 'playerImplementation', org_tubepress_api_const_options_values_ModeValue::PLAYLIST, 'safeSearch', 'text', 'theme', 'timeFrame', 'youtubeUser');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_Type');

        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
    }
}