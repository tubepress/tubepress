<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_impl_util_LangUtilsTestFakeClass
{
    const FOO = 'bar';
    const _   = 'xxx';
}

class tubepress_impl_util_LangUtilsTest extends TubePressUnitTest
{

    function testAssocArray()
    {
        $this->assertFalse(tubepress_impl_util_LangUtils::isAssociativeArray(array(1, 2)));
        $this->assertFalse(tubepress_impl_util_LangUtils::isAssociativeArray(array()));
        $this->assertFalse(tubepress_impl_util_LangUtils::isAssociativeArray(array('foo' => 'bar', 3)));
        $this->assertTrue(tubepress_impl_util_LangUtils::isAssociativeArray(array('foo' => 'bar', 'smack' => 'crack')));
    }

    function testSerialized()
    {
        $isSerialized = tubepress_impl_util_LangUtils::isSerialized('b:0;', $result);

        $this->assertTrue($isSerialized === true);
        $this->assertTrue($result === false);

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized('b:1;', $result);

        $this->assertTrue($isSerialized === true);
        $this->assertTrue($result === true);

        $candidate = array(

            'x' => 5,
            1 => 'hello'
        );

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized(serialize($candidate), $result);

        $this->assertTrue($isSerialized === true);
        $this->assertEquals($candidate, $result);
    }

    function testNotSerialized()
    {
        $isSerialized = tubepress_impl_util_LangUtils::isSerialized('sxx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized(array());

        $this->assertTrue($isSerialized === false);

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized('bxx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized('b:xx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized('xxx');

        $this->assertTrue($isSerialized === false);

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized('');

        $this->assertTrue($isSerialized === false);

        $isSerialized = tubepress_impl_util_LangUtils::isSerialized(null);

        $this->assertTrue($isSerialized === false);
    }

    function testGetDefinedConstantsNoSuchClass()
    {
        $result = tubepress_impl_util_LangUtils::getDefinedConstants('bla bla bla');

        $expected = array();

        $this->assertEquals($expected, $result);
    }

    function testGetDefinedConstants()
    {
        $result = tubepress_impl_util_LangUtils::getDefinedConstants('tubepress_impl_util_LangUtilsTestFakeClass');

        $expected = array(

            'bar'
        );

        $this->assertEquals($expected, $result);
    }
}

