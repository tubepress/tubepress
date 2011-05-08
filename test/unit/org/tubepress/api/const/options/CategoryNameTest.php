<?php
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/const/options/CategoryName.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_CategoryNameTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('advanced', 'display', 'embedded', 'feed', 'meta', 'widget', 'output');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_CategoryName'), $expected);
    }
}