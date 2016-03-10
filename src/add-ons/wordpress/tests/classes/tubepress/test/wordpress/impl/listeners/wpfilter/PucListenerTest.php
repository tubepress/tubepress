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
 * @covers tubepress_wordpress_impl_listeners_wpfilter_PucListener
 */
class tubepress_test_wordpress_impl_listeners_wpfilter_PucListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpfilter_PucListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockIncomingEvent;

    public function onSetup()
    {
        $urlFactory               = new tubepress_url_impl_puzzle_UrlFactory();
        $this->_mockEnvironment   = $this->mock(tubepress_api_environment_EnvironmentInterface::_);
        $this->_mockContext       = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockIncomingEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_wordpress_impl_listeners_wpfilter_PucListener(

            $urlFactory,
            $this->_mockEnvironment,
            $this->_mockContext
        );
    }

    public function testResult()
    {
        $subject = new stdClass();

        $subject->download_url = 'http://tubepress.com/foo/bar';

        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(true);

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn($subject);
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with($subject);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::TUBEPRESS_API_KEY)->andReturn('key1');

        $this->_sut->onFilter_PucRequestInfoResultTubePress($this->_mockIncomingEvent);

        $this->assertEquals('http://tubepress.com/foo/bar?key=key1&pid=2', $subject->download_url);
    }

    public function testResultNoKey()
    {
        $subject = new stdClass();

        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(true);

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn($subject);
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with($subject);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::TUBEPRESS_API_KEY)->andReturnNull();

        $this->_sut->onFilter_PucRequestInfoResultTubePress($this->_mockIncomingEvent);

        $this->assertNull($subject->download_url);
    }

    public function testRequest()
    {
        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array('foo' => 'bar'));
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with(array('foo' => 'bar', 'key' => 'key1', 'pid' => 2));

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::TUBEPRESS_API_KEY)->andReturn('key1');

        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(true);

        $this->_sut->onFilter_PucRequestInfoQueryArgsTubePress($this->_mockIncomingEvent);
    }
}
