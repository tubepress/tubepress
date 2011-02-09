<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/values/OutputModeValue.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_values_OutputModeValueTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('searchInput', 'searchResults');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_values_OutputModeValue', $expected);
    }
}
?>
