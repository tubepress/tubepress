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
    
    public function testAssocArray()
    {
        $this->assertFalse($this->_sut->isAssociativeArray(array(1, 2)));
        $this->assertFalse($this->_sut->isAssociativeArray(array()));
        $this->assertFalse($this->_sut->isAssociativeArray(array('foo' => 'bar', 3)));
        $this->assertTrue($this->_sut->isAssociativeArray(array('foo' => 'bar', 'smack' => 'crack')));
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

