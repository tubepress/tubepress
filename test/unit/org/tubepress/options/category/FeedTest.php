<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/api/const/options/Feed.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_api_const_options_FeedTest extends org_tubepress_api_const_options_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_api_const_options_Feed';
    }
    
    protected function getExpectedNames()
    {
        return array('cacheEnabled', 'embeddableOnly', 'filter_racy', 'developerKey', 'resultCountCap', 'vimeoKey', 'vimeoSecret');
    }
}
?>
