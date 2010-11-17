<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/api/const/options/Meta.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_api_const_options_MetaTest extends org_tubepress_api_const_options_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_api_const_options_Meta';
    }
    
    protected function getExpectedNames()
    {
        return array('author', 'category', 'description', 'id', 'length', 'rating', 
            'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'likes');
    }

}
?>
