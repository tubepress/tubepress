<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/names/Meta.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_names_MetaTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('author', 'category', 'description', 'id', 'length', 'rating', 
            'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'likes');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_names_Meta', $expected);
    }

}
?>
