<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Embedded.class.php';

class org_tubepress_api_const_options_names_EmbeddedTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('playerImplementation', 'autoplay', 'embeddedHeight', 'embeddedWidth', 'hd',
            'loop', 'playerColor', 'playerHighlight', 'showRelated',
            'fullscreen', 'showInfo');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_names_Embedded');

        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
    }
}
