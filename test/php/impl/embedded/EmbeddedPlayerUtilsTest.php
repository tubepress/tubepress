<?php

require_once BASE . '/sys/classes/org/tubepress/impl/embedded/EmbeddedPlayerUtils.class.php';

class org_tubepress_impl_embedded_EmbeddedPlayerUtilsTest extends TubePressUnitTest {

    function testBadColor()
    {
        $this->assertEquals('ff88dd', org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue('badcolor', 'ff88dd'));
    }

    function testGoodColor()
    {
        $this->assertEquals('eecc33', org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue('eecc33', 'ff88dd'));
    }

    function testBooleanToOneOrZero()
    {
        $this->assertEquals('1', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero(true));
        $this->assertEquals('0', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero(false));
        $this->assertEquals('1', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero('1'));
        $this->assertEquals('0', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero('0'));
    }

    function testBooleanToString()
    {
        $this->assertEquals('true', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString(true));
        $this->assertEquals('false', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString(false));
    }
}

