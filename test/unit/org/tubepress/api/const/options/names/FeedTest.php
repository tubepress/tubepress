<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Feed.class.php';

class org_tubepress_api_const_options_names_FeedTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('cacheEnabled', 'embeddableOnly', 'filter_racy', 'developerKey', 'resultCountCap', 'searchResultsRestrictedToUser', 'vimeoKey', 'vimeoSecret');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_names_Feed');
        
        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
    }
}