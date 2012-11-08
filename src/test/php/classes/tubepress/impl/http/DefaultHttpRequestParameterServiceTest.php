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
class tubepress_impl_http_DefaultHttpRequestParameterServiceTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockEventDispatcher;

    function onSetup()
    {
        $this->_mockEventDispatcher = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');

        $this->_sut = new tubepress_impl_http_DefaultHttpRequestParameterService();
    }

    function testParamExists()
    {
        $this->assertTrue($this->_sut->hasParam('something') === false);

        $_REQUEST['something'] = 5;

        $this->assertTrue($this->_sut->hasParam('something') === true);
    }

    function testGetParamValueNoExist()
    {
        $this->assertTrue($this->_sut->getParamValue('something') === null);
    }

    function testGetParam()
    {
        $_REQUEST['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT, Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('yo');

            return $good;
        }));

        $result = $this->_sut->getParamValue('something');

        $this->assertTrue($result === 'yo');
    }

    function testGetParamAsIntActuallyInt()
    {
        $_REQUEST['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT, Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('44');

            return $good;
        }));

        $result = $this->_sut->getParamValueAsInt('something', 1);

        $this->assertTrue($result === 44);
    }

    function testGetParamAsIntNotActuallyInt()
    {
        $_REQUEST['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT, Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('44vb');

            return $good;
        }));

        $result = $this->_sut->getParamValueAsInt('something', 33);

        $this->assertTrue($result === 33);
    }
}

