<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/values/OutputValue.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_values_OutputValueTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('searchInput', 'searchResults');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_values_OutputValue'), $expected);

    }
}
?>
