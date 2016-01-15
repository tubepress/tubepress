<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_options_impl_DispatchingReference<extended>
 */
class tubepress_test_options_impl_DispatchingReferenceTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_options_impl_DispatchingReference
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDelegateReference1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDelegateReference2;

    public function onSetup()
    {
        $this->_mockEventDispatcher    = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockDelegateReference1 = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $this->_mockDelegateReference2 = $this->mock(tubepress_api_options_ReferenceInterface::_);

        $this->_sut = new tubepress_options_impl_DispatchingReference($this->_mockEventDispatcher);

        $this->_sut->setReferences(array($this->_mockDelegateReference1, $this->_mockDelegateReference2));

        $this->_mockDelegateReference1->shouldReceive('getAllOptionNames')->once()->andReturn(array('foo'));
        $this->_mockDelegateReference2->shouldReceive('getAllOptionNames')->once()->andReturn(array('bar'));
    }

    /**
     * @dataProvider getDataDelegationNoDispatch
     */
    public function testDelegationWithoutEventDispatcher($getter)
    {
        $this->_mockDelegateReference2->shouldReceive($getter)->once()->andReturn('hi');

        $actual = $this->_sut->$getter('bar');

        $this->assertEquals('hi', $actual);
    }

    public function getDataDelegationNoDispatch()
    {
        return array(

            array('isProOnly'),
            array('isMeantToBePersisted'),
            array('isAbleToBeSetViaShortcode'),
            array('isBoolean'),
        );
    }

    /**
     * @dataProvider getDataDelegation
     */
    public function testDelegationWithEventDispatcher($getter, $eventName)
    {
        $this->_mockDelegateReference2->shouldReceive($getter)->once()->andReturn('hi');

        $this->_setupEventDispatcher('bar', 'hi', $eventName);

        $actual = $this->_sut->$getter('bar');

        $this->assertEquals('hi', $actual);
    }

    public function getDataDelegation()
    {
        return array(

            array('getDefaultValue', tubepress_api_event_Events::OPTION_DEFAULT_VALUE),
            array('getUntranslatedDescription', tubepress_api_event_Events::OPTION_DESCRIPTION),
            array('getUntranslatedLabel', tubepress_api_event_Events::OPTION_LABEL),
        );
    }

    public function testGetPropertyAsBoolean()
    {
        $this->_mockDelegateReference2->shouldReceive('getProperty')->twice()->with('bar', 'prop-name')->andReturn('raw');

        $actual = $this->_sut->getProperty('bar', 'prop-name');

        $this->assertEquals('raw', $actual);

        $actual = $this->_sut->getPropertyAsBoolean('bar', 'prop-name');

        $this->assertTrue($actual === true);
    }

    public function testHas()
    {
        $this->_mockDelegateReference2->shouldReceive('hasProperty')->once()->with('bar', 'something')->andReturn(true);

        $actual = $this->_sut->hasProperty('bar', 'something');

        $this->assertTrue($actual);
    }

    public function testHasNoSuchOption()
    {
        $this->setExpectedException('InvalidArgumentException', 'hello is not a known option');

        $this->_sut->hasProperty('hello', 'something');
    }

    public function testOptionexists()
    {
        $this->assertTrue($this->_sut->optionExists('foo'));
        $this->assertTrue($this->_sut->optionExists('bar'));
        $this->assertFalse($this->_sut->optionExists('hello'));
    }

    public function testGetAllOptionNames()
    {
        $actual   = $this->_sut->getAllOptionNames();
        $expected = array('foo', 'bar');

        $this->assertEquals($expected, $actual);
    }

    private function _setupEventDispatcher($optionName, $subject, $eventName)
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($subject, array('optionName' => $optionName))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with("$eventName.$optionName", $mockEvent);

        $mockEvent->shouldReceive('getSubject')->once()->andReturn($subject);
    }
}