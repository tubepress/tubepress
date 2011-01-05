<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Display.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_DisplayTest extends org_tubepress_options_category_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_options_category_Display';
    }
    
    protected function getExpectedNames()
    {
        return array('ajaxPagination', 'playerLocation', 'descriptionLimit', 'orderBy', 'relativeDates', 
            'resultsPerPage', 'thumbHeight', 'thumbWidth', 'paginationAbove', 'paginationBelow',
            'hqThumbs', 'randomize_thumbnails', 'theme');
    }
}
?>
