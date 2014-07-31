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
 * @covers tubepress_app_impl_listeners_player_template_PlayerTemplateListener
 */
class tubepress_test_app_impl_listeners_player_template_PlayerTemplateListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_player_template_PlayerTemplateListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {

        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_sut = new tubepress_app_impl_listeners_player_template_PlayerTemplateListener(
            $this->_mockExecutionContext
        );
    }

    public function testAlterTemplate()
    {
        $video = new tubepress_app_api_media_MediaItem('video-id');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTML_GALLERY_ID)->andReturn('gallery-id');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_WIDTH)->andReturn(668);

        $expected = array(
            tubepress_app_api_template_VariableNames::HTML_WIDGET_ID    => 'gallery-id',
            tubepress_app_api_template_VariableNames::MEDIA_ITEM        => $video,
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX => 668,
        );

        $event = $this->mock('tubepress_lib_api_event_EventInterface');
        $event->shouldReceive('getArgument')->once()->with('item')->andReturn($video);
        $event->shouldReceive('getSubject')->once()->andReturn(array('foo' => 'bar'));
        $event->shouldReceive('setSubject')->once()->with(array_merge(array('foo' => 'bar'), $expected));

        $this->_sut->onPlayerTemplatePreRender($event);

        $this->assertTrue(true);
    }
}