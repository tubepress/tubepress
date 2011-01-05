<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Advanced.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_AdvancedTest extends org_tubepress_options_category_AbstractOptionsCategoryTest {
	
	protected function getClassName()
	{
	    return 'org_tubepress_options_category_Advanced';
	}
	
    protected function getExpectedNames()
    {
        return array('dateFormat', 'debugging_enabled', 'keyword', 'videoBlacklist');
    }
	
}
?>
