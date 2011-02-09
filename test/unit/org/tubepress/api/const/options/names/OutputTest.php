<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/names/Output.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_names_OutputTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('mode', 'video', 'output');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_names_Output', $expected);
    }
}
?>
