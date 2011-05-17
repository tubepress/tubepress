<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Advanced.class.php';

class org_tubepress_api_const_options_names_AdvancedTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('cacheCleaningFactor', 'cacheDirectory', 'cacheLifetimeSeconds', 
        	'dateFormat', 'debugging_enabled', 'disableHttpTransportCurl', 
        	'disableHttpTransportExtHttp', 'disableHttpTransportFopen', 
        	'disableHttpTransportFsockOpen', 'disableHttpTransportStreams', 
        	'keyword', 'videoBlacklist');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_names_Advanced');
        
        TubePressArrayTestUtils::checkArrayEquality(
            $actual,
            $expected
        );
    }

}