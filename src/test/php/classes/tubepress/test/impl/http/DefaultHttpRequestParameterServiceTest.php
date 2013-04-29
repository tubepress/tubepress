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
class tubepress_test_impl_http_DefaultHttpRequestParameterServiceTest extends tubepress_test_TubePressUnitTest
{
    private $_sut;

    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->createMockSingletonService('ehough_tickertape_EventDispatcherInterface');

        $this->_sut = new tubepress_impl_http_DefaultHttpRequestParameterService();
    }

    public function testParamExists()
    {
        $_GET['something'] = 5;

        $this->assertTrue($this->_sut->hasParam('something') === true);
    }

    public function testParamNotExists()
    {
        $this->assertTrue($this->_sut->hasParam('something') === false);
    }

    public function testGetParamValueNoExist()
    {
        $this->assertTrue($this->_sut->getParamValue('something') === null);
    }

    public function testGetParam()
    {
        $_POST['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_READFROMEXTERNAL, ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('yo');

            return $good;
        }));

        $result = $this->_sut->getParamValue('something');

        $this->assertTrue($result === 'yo');
    }

    public function testGetParamAsIntActuallyInt()
    {
        $_POST['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_READFROMEXTERNAL, ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('44');

            return $good;
        }));

        $result = $this->_sut->getParamValueAsInt('something', 1);

        $this->assertTrue($result === 44);
    }

    public function testGetParamAsIntNotActuallyInt()
    {
        $_GET['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_READFROMEXTERNAL, ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('44vb');

            return $good;
        }));

        $result = $this->_sut->getParamValueAsInt('something', 33);

        $this->assertTrue($result === 33);
    }
}

