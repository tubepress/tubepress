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
abstract class tubepress_plugins_core_impl_filters_AbstractStringMagicFilterTest extends TubePressUnitTest
{
	private $_sut;

	function onSetup()
	{
		$this->_sut = $this->_buildSut();
	}

	function testBooleanVariations()
	{
	    $this->_booleanConversion(true, 'true');
	    $this->_booleanConversion(true, 'TRUE');
	    $this->_booleanConversion(true, ' TRuE  ');

	    $this->_booleanConversion(false, 'false  ');
	    $this->_booleanConversion(false, 'FALSE');
	    $this->_booleanConversion(false, ' faLSe  ');
	}

    function testInt()
    {
        $event = $this->buildEvent('name', 5);

        $result = $this->_performAltering($this->_sut, $event);

        $this->assertEquals(5, $event->getSubject());
    }

    function testDeepArray()
    {
        $val = array(array(array('name' => '  some <value> \\\\" ')));

        $expected = array(array(array('name' => 'some &lt;value&gt; "')));

        $event = $this->buildEvent('otherName', $val);

        $this->_performAltering($this->_sut, $event);

        $this->assertEquals($expected, $event->getSubject(), var_export($event->getSubject(), true));
    }

    protected abstract function _buildSut();

    protected abstract function _performAltering($sut, tubepress_api_event_TubePressEvent $event);

    private function _booleanConversion($expected, $val)
    {
        $event = $this->buildEvent('name', $val);

        $this->_performAltering($this->_sut, $event);

        return $this->assertEquals($expected, $event->getSubject());
    }

    private function buildEvent($name, $value)
    {
        $event = new tubepress_api_event_TubePressEvent($value);
        $event->setArgument('optionName', $name);
        return $event;
    }
}