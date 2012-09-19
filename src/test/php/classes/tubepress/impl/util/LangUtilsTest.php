<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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

