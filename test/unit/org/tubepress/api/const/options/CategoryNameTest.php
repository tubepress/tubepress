<?php
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/const/options/CategoryName.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../ClassConstantTestUtility.php';

class org_tubepress_api_const_options_CategoryNameTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('advanced', 'display', 'embedded', 'feed', 'meta', 'widget', 'output');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_CategoryName', $expected);
    }
}
?>
