<?php
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/const/options/CategoryName.class.php';

class org_tubepress_api_const_options_CategoryNameTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('advanced', 'display', 'embedded', 'feed', 'meta', 'widget', 'output');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_CategoryName');
        
        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
        
    }
}