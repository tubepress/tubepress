<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Meta.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_names_MetaTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('author', 'category', 'description', 'id', 'length', 'rating',
            'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'likes');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_names_Meta'), $expected);
    }

}
?>
