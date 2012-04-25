<?php

require_once BASE . '/sys/classes/org/tubepress/impl/util/LangUtils.class.php';

class org_tubepress_impl_util_LangUtilsTestFakeClass
{
    const FOO = 'bar';
    const _   = 'xxx';
}

class org_tubepress_impl_util_LangUtilsTest extends TubePressUnitTest
{

    function testSerialized()
    {
        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized('b:0;', $result);

        $this->assertTrue($isSerialized === true);
        $this->assertTrue($result === false);

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized('b:1;', $result);

        $this->assertTrue($isSerialized === true);
        $this->assertTrue($result === true);

        $candidate = array(

            'x' => 5,
            1 => 'hello'
        );

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized(serialize($candidate), $result);

        $this->assertTrue($isSerialized === true);
        $this->assertArrayEquality($candidate, $result);
    }

    function testNotSerialized()
    {
        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized('sxx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized(array());

        $this->assertTrue($isSerialized === false);

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized('bxx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized('b:xx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized('xxx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized('');

        $this->assertTrue($isSerialized === false);

        $isSerialized = org_tubepress_impl_util_LangUtils::isSerialized(null);

        $this->assertTrue($isSerialized === false);
    }

    function testAssocArray()
    {
        $this->assertFalse(org_tubepress_impl_util_LangUtils::isAssociativeArray(array(1, 2)));
        $this->assertFalse(org_tubepress_impl_util_LangUtils::isAssociativeArray(array()));
        $this->assertFalse(org_tubepress_impl_util_LangUtils::isAssociativeArray(array('foo' => 'bar', 3)));
        $this->assertTrue(org_tubepress_impl_util_LangUtils::isAssociativeArray(array('foo' => 'bar', 'smack' => 'crack')));
    }

    function testGetDefinedConstantsNoSuchClass()
    {
        $result = org_tubepress_impl_util_LangUtils::getDefinedConstants('bla bla bla');

        $expected = array();

        $this->assertArrayEquality($expected, $result);
    }

    function testGetDefinedConstants()
    {
        $result = org_tubepress_impl_util_LangUtils::getDefinedConstants('org_tubepress_impl_util_LangUtilsTestFakeClass');

        $expected = array(

            'bar'
        );

        $this->assertArrayEquality($expected, $result);
    }
}

