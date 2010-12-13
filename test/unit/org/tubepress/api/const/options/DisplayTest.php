<?php
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/const/options/Display.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_api_const_options_DisplayTest extends org_tubepress_api_const_options_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_api_const_options_Display';
    }
    
    protected function getExpectedNames()
    {
        return array('ajaxPagination', 'playerLocation', 'descriptionLimit', 'orderBy', 'relativeDates', 
            'resultsPerPage', 'thumbHeight', 'thumbWidth', 'paginationAbove', 'paginationBelow',
            'hqThumbs', 'randomize_thumbnails', 'theme');
    }
}
?>
