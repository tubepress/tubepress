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
 * @covers tubepress_shortcode_impl_Shortcode<extended>
 */
class tubepress_test_internal_shortcode_ShortcodeTest extends tubepress_api_test_TubePressUnitTest
{
    public function testOtherAttributes()
    {
        $shortcode = new tubepress_shortcode_impl_Shortcode('name', array('foo' => 'bar'), ' inner ');

        $this->assertEquals('name', $shortcode->getName());
        $this->assertEquals(array('foo' => 'bar'), $shortcode->getAttributes());
        $this->assertEquals(' inner ', $shortcode->getInnerContent());
    }

    /**
     * @dataProvider getDataGetName
     */
    public function testGetName($incoming, $expected)
    {
        $shortcode = new tubepress_shortcode_impl_Shortcode($incoming);

        $this->assertEquals($expected, $shortcode->getName());
        $this->assertEquals(array(), $shortcode->getAttributes());
        $this->assertNull($shortcode->getInnerContent());
    }

    public function getDataGetName()
    {
        return array(

            array(' name ', 'name'),
            array('name ', 'name'),
            array(' name', 'name'),
        );
    }

    /**
     * @dataProvider getDataInvalidConstructions
     */
    public function testInvalidConstructions($name, $attributes, $innerContent, $expectedErrorMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedErrorMessage);

        new tubepress_shortcode_impl_Shortcode($name, $attributes, $innerContent);
    }

    public function getDataInvalidConstructions()
    {
        return array(

            array('',                  array(), null, 'Invalid shortcode name'),
            array('$',                 array(), null, 'Invalid shortcode name'),
            array(' . ',               array(), null, 'Invalid shortcode name'),
            array(' ',                 array(), null, 'Invalid shortcode name'),
            array(str_repeat('x', 51), array(), null, 'Invalid shortcode name'),

            array(new stdClass(), array(),    null, 'Shortcode name must be a string'),
            array(array(),        array(),    null, 'Shortcode name must be a string'),
            array(3,              array(),    null, 'Shortcode name must be a string'),
            array(null,              array(), null, 'Shortcode name must be a string'),

            array('name', new stdClass(), null, 'Shortcode attributes must be an array'),
            array('name', '',             null, 'Shortcode attributes must be an array'),
            array('name', null,           null, 'Shortcode attributes must be an array'),

            array('name', array(''),      null, 'Shortcode attributes must be an associative array'),

            array('name', array(), array(),        'Inner content must either be null or a string'),
            array('name', array(), new stdClass(), 'Inner content must either be null or a string'),
            array('name', array(), 0,              'Inner content must either be null or a string'),
        );
    }
}