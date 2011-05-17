<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/values/OutputValue.class.php';

class org_tubepress_api_const_options_values_OutputValueTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('searchInput', 'searchResults', 'ajaxSearchInput');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_values_OutputValue');
        
        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
        
    }
}