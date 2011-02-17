<?php
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/const/options/CategoryName.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_TypeTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('boolean', 'color', 'integral', 'mode', 'order', 'output', 'player', 'playerImplementation', org_tubepress_api_const_options_values_ModeValue::PLAYLIST, 'safeSearch', 'text', 'theme', 'timeFrame', 'youtubeUser');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_Type'), $expected);
    }
}
?>



