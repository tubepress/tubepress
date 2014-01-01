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
 * @covers tubepress_addons_core_impl_listeners_StringMagicFilter
 */
class tubepress_test_addons_core_impl_listeners_StringMagicFilterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_StringMagicFilter
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_StringMagicFilter();
    }

    public function testBooleanVariations()
    {
        $this->_booleanConversion(true, 'true');
        $this->_booleanConversion(true, 'TRUE');
        $this->_booleanConversion(true, ' TRuE  ');

        $this->_booleanConversion(false, 'false  ');
        $this->_booleanConversion(false, 'FALSE');
        $this->_booleanConversion(false, ' faLSe  ');
    }

    public function testInt()
    {
        $event = $this->buildEvent('name', 5);

        $this->_sut->magic($event);

        $this->assertEquals(5, $event->getSubject());
    }

    public function testDeepArray()
    {
        $val = array(array(array('name' => '  some <value> \\\\" ')));

        $expected = array(array(array('name' => 'some &lt;value&gt; "')));

        $event = $this->buildEvent('otherName', $val);

        $this->_sut->magic($event);

        $this->assertEquals($expected, $event->getSubject(), var_export($event->getSubject(), true));
    }

    private function _booleanConversion($expected, $val)
    {
        $event = $this->buildEvent('name', $val);

        $this->_sut->magic($event);

        $this->assertEquals($expected, $event->getSubject());
    }

    private function buildEvent($name, $value)
    {
        $event = new tubepress_spi_event_EventBase($value);
        $event->setArgument('optionName', $name);
        return $event;
    }
}