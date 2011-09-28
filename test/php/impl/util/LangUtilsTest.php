<?php

require_once BASE . '/sys/classes/org/tubepress/impl/util/LangUtils.class.php';

class org_tubepress_impl_util_LangUtilsTest extends TubePressUnitTest
{

    function testAssocArray()
    {
        $this->assertFalse(org_tubepress_impl_util_LangUtils::isAssociativeArray(array(1, 2)));
        $this->assertFalse(org_tubepress_impl_util_LangUtils::isAssociativeArray(array()));
        $this->assertFalse(org_tubepress_impl_util_LangUtils::isAssociativeArray(array('foo' => 'bar', 3)));
        $this->assertTrue(org_tubepress_impl_util_LangUtils::isAssociativeArray(array('foo' => 'bar', 'smack' => 'crack')));
    }
}

