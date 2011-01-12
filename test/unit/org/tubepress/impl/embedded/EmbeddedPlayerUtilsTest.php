<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/embedded/EmbeddedPlayerUtils.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_embedded_EmbeddedPlayerUtilsTest extends TubePressUnitTest {
    
    function testBadColor()
    {
        $this->assertEquals('sdfsdf', org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue('badcolor', '000000'));
    }
    
}
?>
