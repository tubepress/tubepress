<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/names/Feed.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_names_FeedTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('cacheEnabled', 'embeddableOnly', 'filter_racy', 'developerKey', 'resultCountCap', 'searchResultsRestrictedToUser', 'vimeoKey', 'vimeoSecret');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_names_Feed'), $expected);

    }

}
?>
