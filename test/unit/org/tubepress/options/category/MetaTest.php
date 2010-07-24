<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Meta.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_MetaTest extends org_tubepress_options_category_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_options_category_Meta';
    }
    
    protected function getExpectedNames()
    {
        return array('author', 'category', 'description', 'id', 'length', 'rating', 
            'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'likes');
    }

}
?>