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
abstract class tubepress_addons_core_impl_listeners_AbstractStringMagicFilterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_AbstractStringMagicFilter
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = $this->_buildSut();
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

        $result = $this->_performAltering($this->_sut, $event);

        $this->assertEquals(5, $event->getSubject());
    }

    public function testDeepArray()
    {
        $val = array(array(array('name' => '  some <value> \\\\" ')));

        $expected = array(array(array('name' => 'some &lt;value&gt; "')));

        $event = $this->buildEvent('otherName', $val);

        $this->_performAltering($this->_sut, $event);

        $this->assertEquals($expected, $event->getSubject(), var_export($event->getSubject(), true));
    }

    protected abstract function _buildSut();

    protected abstract function _performAltering($sut, tubepress_api_event_EventInterface $event);

    private function _booleanConversion($expected, $val)
    {
        $event = $this->buildEvent('name', $val);

        $this->_performAltering($this->_sut, $event);

        $this->assertEquals($expected, $event->getSubject());
    }

    private function buildEvent($name, $value)
    {
        $event = new tubepress_spi_event_EventBase($value);
        $event->setArgument('optionName', $name);
        return $event;
    }
}