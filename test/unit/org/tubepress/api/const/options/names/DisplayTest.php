<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Display.class.php';

class org_tubepress_api_const_options_names_DisplayTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('ajaxPagination', 'playerLocation', 'descriptionLimit', 'orderBy', 'relativeDates',
            'resultsPerPage', 'thumbHeight', 'thumbWidth', 'paginationAbove', 'paginationBelow',
            'hqThumbs', 'randomize_thumbnails', 'theme', 'fluidThumbs');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_names_Display');
        
        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
    }
}