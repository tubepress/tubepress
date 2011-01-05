<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Feed.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_FeedTest extends org_tubepress_options_category_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_options_category_Feed';
    }
    
    protected function getExpectedNames()
    {
        return array('cacheEnabled', 'embeddableOnly', 'filter_racy', 'developerKey', 'resultCountCap', 'vimeoKey', 'vimeoSecret');
    }
}
?>
