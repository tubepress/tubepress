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
abstract class tubepress_plugins_core_filters_AbstractStringMagicFilterTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
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