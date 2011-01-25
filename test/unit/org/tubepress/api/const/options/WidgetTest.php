<?php
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/const/options/Widget.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_api_const_options_WidgetTest extends org_tubepress_api_const_options_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_api_const_options_Widget';
    }
    
    protected function getExpectedNames()
    {
        return array('widget-tagstring', 'widget-title');
    }
}
?>
