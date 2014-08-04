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
 * @covers tubepress_app_impl_template_TemplatingService<extended>
 */
class tubepress_test_app_impl_template_TemplatingServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_template_TemplatingService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDelegateEngine;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockDelegateEngine  = $this->mock('ehough_templating_EngineInterface');
        $this->_mockEventDispatcher = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);

        $this->_sut = new tubepress_app_impl_template_TemplatingService(

            $this->_mockDelegateEngine,
            $this->_mockEventDispatcher
        );
    }

    public function testRender()
    {
        $templateVars           = array('foo' => 'bar');
        $nameSelectionEventArgs = array('templateVars' => $templateVars);

        $templateSelectEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $templateSelectEvent->shouldReceive('getSubject')->once()->andReturn('new-template-name');
        $templateSelectEvent->shouldReceive('getArgument')->once()->with('templateVars')->andReturn($templateVars);

        $originalPreRenderEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $originalPreRenderEvent->shouldReceive('getSubject')->once()->andReturn($templateVars);

        $newPreRenderEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $newPreRenderEvent->shouldReceive('getSubject')->times(3)->andReturn($templateVars);

        $newPostRenderEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $newPostRenderEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $originalPostRenderEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $originalPostRenderEvent->shouldReceive('getSubject')->once()->andReturn('hi');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('template-name', $nameSelectionEventArgs)->andReturn($templateSelectEvent);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($templateVars)->andReturn($originalPreRenderEvent);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($templateVars)->andReturn($newPreRenderEvent);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('foo', $templateVars)->andReturn($newPostRenderEvent);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('abc', $templateVars)->andReturn($originalPostRenderEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::TEMPLATE_SELECT, $templateSelectEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.template-name', $originalPreRenderEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.new-template-name', $newPreRenderEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.new-template-name', $newPostRenderEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.template-name', $originalPostRenderEvent);

        $this->_mockDelegateEngine->shouldReceive('render')->once()->with('new-template-name', $templateVars)->andReturn('foo');

        $result = $this->_sut->renderTemplate('template-name', $templateVars);

        $this->assertEquals('hi', $result);
    }
}