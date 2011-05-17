<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Meta.class.php';

class org_tubepress_api_const_options_names_MetaTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('author', 'category', 'description', 'id', 'length', 'rating',
            'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'likes');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_names_Meta');
        
        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
        
    }
}