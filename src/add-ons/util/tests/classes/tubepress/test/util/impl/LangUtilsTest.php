<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_util_impl_LangUtils
 */
class tubepress_test_util_impl_LangUtilsTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_util_impl_LangUtils
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_util_impl_LangUtils();
    }

    /**
     * @dataProvider getDataIsAssociativeArray
     */
    public function testAssocArray($expected, $incoming)
    {
        $this->assertEquals($expected, $this->_sut->isAssociativeArray($incoming));
    }

    public function getDataIsAssociativeArray()
    {
        return array(
            array(false, array(1, 2)),
            array(false, array()),
            array(false, array('foo' => 'bar', 3)),
            array(true,  array('foo' => 'bar', 'smack' => 'crack')),
            array(false, 1),
        );
    }

    /**
     * @dataProvider getDataBooleaenToOneOrZero
     */
    public function testBooleanToOneOrZero($expected, $incoming)
    {
        $this->assertEquals($expected, $this->_sut->booleanToStringOneOrZero($incoming));
    }

    public function getDataBooleaenToOneOrZero()
    {
        return array(
            array('1', true),
            array('0', false),
            array('1', '1'),
            array('0', '0'),
        );
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
            array(array(new stdClass()), false),
            array(array('5', new stdClass()), false),
            array(array('5', 3), false),
            array(array('4'), true),
        );
    }

    /**
     * @dataProvider getDataArrayUnshiftAssociative
     */
    public function testArrayUnshiftAssociative(array $incoming, $key, $value, array $expected)
    {
        $actual = $this->_sut->arrayUnshiftAssociative($incoming, $key, $value);

        $this->assertTrue(is_array($actual));
        $this->assertEquals($expected, $actual);
    }

    public function getDataArrayUnshiftAssociative()
    {
        return array(
            array(array('foo' => 'bar'),                 'hi', 'there', array('hi' => 'there', 'foo' => 'bar')),
            array(array('foo' => 'bar', 'hi' => 'gone'), 'hi', 'there', array('hi' => 'there', 'foo' => 'bar')),
            array(array(),                               'hi', 'foo',   array('hi' => 'foo')),
            array(array(1,2,3),                          'hi', 'foo',   array('foo',1,2,3)),
        );
    }
}
