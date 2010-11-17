<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/api/const/options/Embedded.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_api_const_options_EmbeddedTest extends org_tubepress_api_const_options_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_api_const_options_Embedded';
    }
    
    protected function getExpectedNames()
    {
        return array('playerImplementation', 'autoplay', 'border', 'embeddedHeight', 'embeddedWidth', 'hd',
            'genie', 'loop', 'playerColor', 'playerHighlight', 'showRelated',
            'fullscreen', 'showInfo');
    }
}
?>
