<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/names/Advanced.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../ClassConstantTestUtility.php';

class org_tubepress_api_const_options_names_AdvancedTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('cacheCleaningFactor', 'cacheDirectory', 'cacheLifetimeSeconds', 'dateFormat', 'debugging_enabled', 'disableHttpTransportCurl', 'disableHttpTransportExtHttp', 'disableHttpTransportFopen', 'disableHttpTransportFsockOpen', 'disableHttpTransportStreams', 'keyword', 'videoBlacklist');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_names_Advanced', $expected);
    }

}
?>
