<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_array_impl_ArrayReader
 */
class tubepress_test_array_impl_ArrayReaderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_array_impl_ArrayReader
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_array_impl_ArrayReader();
    }

    /**
     * @dataProvider dataGetAsString
     */
    public function testGetAsString($path, $expectedResult, $default)
    {
        $x = array(

            'foo' => array(

                'bar' => 'hi'
            ),

            'x' => 'y'
        );

        $actual = $this->_sut->getAsString($x, $path, $default);

        $this->assertEquals($expectedResult, $actual);
    }

    public function dataGetAsString()
    {
        return array(

            array('', null, null),
            array('x', 'y', null),
            array('foo.bar', 'hi', null),
            array('x.no', null, null)
        );
    }
}