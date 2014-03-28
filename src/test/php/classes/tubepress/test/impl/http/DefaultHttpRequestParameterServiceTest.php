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
 * @covers tubepress_impl_http_DefaultHttpRequestParameterService<extended>
 */
class tubepress_test_impl_http_DefaultHttpRequestParameterServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_http_DefaultHttpRequestParameterService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

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

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('yo');

            return $good;
        }));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . '.something', ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === 'yo';

            $arg->setSubject('yo!');

            return $good;
        }));

        $result = $this->_sut->getParamValue('something');

        $this->assertTrue($result === 'yo!');
    }

    public function testGetParamAsIntActuallyInt()
    {
        $_POST['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('44');

            return $good;
        }));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . '.something', ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === '44';

            $arg->setSubject('444');

            return $good;
        }));

        $result = $this->_sut->getParamValueAsInt('something', 1);

        $this->assertTrue($result === 444);
    }

    public function testGetParamAsIntNotActuallyInt()
    {
        $_GET['something'] = array(1, 2, 3);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === array(1, 2, 3)
                && $arg->getArgument('optionName') === 'something';

            $arg->setSubject('44vb');

            return $good;
        }));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . '.something', ehough_mockery_Mockery::on(function ($arg) {


            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === '44vb';

            $arg->setSubject('44vb777ert');

            return $good;
        }));

        $result = $this->_sut->getParamValueAsInt('something', 33);

        $this->assertTrue($result === 33);
    }
}

