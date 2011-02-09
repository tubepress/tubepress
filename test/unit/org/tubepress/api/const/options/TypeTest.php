<?php
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/const/options/CategoryName.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../ClassConstantTestUtility.php';

class org_tubepress_api_const_options_TypeTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('boolean', 'color', 'integral', 'mode', 'order', 'output', 'player', 'playerImplementation', 'playlist', 'safeSearch', 'text', 'theme', 'timeFrame', 'youtubeUser');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_Type', $expected);
    }
}
?>



