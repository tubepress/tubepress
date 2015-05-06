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
 * @covers tubepress_lib_impl_shortcode_Shortcode<extended>
 */
class tubepress_test_lib_impl_shortcode_ShortcodeTest extends tubepress_test_TubePressUnitTest
{
    public function testOtherAttributes()
    {
        $shortcode = new tubepress_lib_impl_shortcode_Shortcode('name', array('foo' => 'bar'), ' inner ', 'original text');

        $this->assertEquals('name', $shortcode->getName());
        $this->assertEquals(array('foo' => 'bar'), $shortcode->getAttributes());
        $this->assertEquals(' inner ', $shortcode->getInnerContent());
        $this->assertEquals('original text', $shortcode->getOriginalText());
    }

    /**
     * @dataProvider getDataGetName
     */
    public function testGetName($incoming, $expected)
    {
        $shortcode = new tubepress_lib_impl_shortcode_Shortcode($incoming);

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
    public function testInvalidConstructions($name, $attributes, $innerContent, $originalText, $expectedErrorMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedErrorMessage);

        new tubepress_lib_impl_shortcode_Shortcode($name, $attributes, $innerContent, $originalText);
    }

    public function getDataInvalidConstructions()
    {
        return array(

            array('',                  array(), null, null, 'Invalid shortcode name'),
            array('$',                 array(), null, null, 'Invalid shortcode name'),
            array(' . ',               array(), null, null, 'Invalid shortcode name'),
            array(' ',                 array(), null, null, 'Invalid shortcode name'),
            array(str_repeat('x', 51), array(), null, null, 'Invalid shortcode name'),

            array(new stdClass(), array(),    null, null, 'Shortcode name must be a string'),
            array(array(),        array(),    null, null, 'Shortcode name must be a string'),
            array(3,              array(),    null, null, 'Shortcode name must be a string'),
            array(null,              array(), null, null, 'Shortcode name must be a string'),

            array('name', new stdClass(), null, null, 'Shortcode attributes must be an array'),
            array('name', '',             null, null, 'Shortcode attributes must be an array'),
            array('name', null,           null, null, 'Shortcode attributes must be an array'),

            array('name', array(''),      null, null, 'Shortcode attributes must be an associative array'),

            array('name', array(), array(),        null, 'Inner content must either be null or a string'),
            array('name', array(), new stdClass(), null, 'Inner content must either be null or a string'),
            array('name', array(), 0,              null, 'Inner content must either be null or a string'),

            array('name', array(), null, array(),        'Original text must either be null or a string'),
            array('name', array(), null, new stdClass(), 'Original text must either be null or a string'),
            array('name', array(), null, 0,              'Original text must either be null or a string'),
        );
    }
}