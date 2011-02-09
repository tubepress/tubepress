<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/names/Embedded.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_names_EmbeddedTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('playerImplementation', 'autoplay', 'border', 'embeddedHeight', 'embeddedWidth', 'hd',
            'genie', 'loop', 'playerColor', 'playerHighlight', 'showRelated',
            'fullscreen', 'showInfo');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_names_Embedded'), $expected);
    }
}
?>
