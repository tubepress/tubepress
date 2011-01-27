<?php
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/const/options/Advanced.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_api_const_options_AdvancedTest extends org_tubepress_api_const_options_AbstractOptionsCategoryTest {
	
	protected function getClassName()
	{
	    return 'org_tubepress_api_const_options_Advanced';
	}
	
    protected function getExpectedNames()
    {
        return array('dateFormat', 'debugging_enabled', 'disableHttpTransportCurl', 'disableHttpTransportExtHttp', 'disableHttpTransportFopen', 'disableHttpTransportFsockOpen', 'disableHttpTransportStreams', 'keyword', 'videoBlacklist');
    }
	
}
?>
