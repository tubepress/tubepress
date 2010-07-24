<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Embedded.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_EmbeddedTest extends org_tubepress_options_category_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_options_category_Embedded';
    }
    
    protected function getExpectedNames()
    {
        return array('playerImplementation', 'autoplay', 'border', 'embeddedHeight', 'embeddedWidth', 'hd',
            'genie', 'loop', 'playerColor', 'playerHighlight', 'showRelated',
            'fullscreen', 'showInfo');
    }
}
?>