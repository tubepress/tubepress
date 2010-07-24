<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Widget.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_WidgetTest extends org_tubepress_options_category_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_options_category_Widget';
    }
    
    protected function getExpectedNames()
    {
        return array('widget-tagstring', 'widget-title');
    }
}
?>