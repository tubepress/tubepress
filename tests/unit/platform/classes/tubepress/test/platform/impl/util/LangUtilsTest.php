<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_platform_impl_util_LangUtils
 */
class tubepress_test_impl_util_LangUtilsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_platform_impl_util_LangUtils
     */
    private $_sut;
    
    public function onSetup()
    {
        $this->_sut = new tubepress_platform_impl_util_LangUtils();
    }

    /**
     * @dataProvider getDataAssociativeArray
     */
    public function testAssocArray($candidate, $expected)
    {
        $actual = $this->_sut->isAssociativeArray($candidate);

        $this->assertEquals($expected, $actual, 'Assertion failed for ' . print_r($candidate, true));
    }

    public function getDataAssociativeArray()
    {
        return array(

            array(array('5' => 5), true),
            array(new stdClass(), false),
            array(44, false),
            array('hello', false),
            array(array(), false),
            array(array('a' => 'a'), true),
            array(array(1 => 'a'), true),
            array(array(1, 2), false,),
            array(array('foo' => 'bar', 'smack' => 'pap'), true),
            array(array_fill_keys(range(2,1000,3),uniqid()), true),
            array(array_fill(0,1000,uniqid()), false),
        );
    }

    public function testBooleanToOneOrZero()
    {
        $this->assertEquals('1', $this->_sut->booleanToStringOneOrZero(true));
        $this->assertEquals('0', $this->_sut->booleanToStringOneOrZero(false));
        $this->assertEquals('1', $this->_sut->booleanToStringOneOrZero('1'));
        $this->assertEquals('0', $this->_sut->booleanToStringOneOrZero('0'));
    }

    /**
     * @dataProvider getDataArrayOfStrings
     */
    public function testArrayOfStrings($candidate, $expected)
    {
        $this->assertEquals($expected, $this->_sut->isSimpleArrayOfStrings($candidate));
    }

    public function getDataArrayOfStrings()
    {
        return array(

            array(array(3), false),
            array(array(new stdClass), false),
            array(array('5', new stdClass), false),
            array(array('5', 3), false),
            array(array('4'), true)
        );
    }
}

