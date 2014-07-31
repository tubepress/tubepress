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
 * @covers tubepress_app_impl_listeners_single_template_SingleItemTemplateListener
 */
class tubepress_test_app_feature_single_impl_listeners_template_SingleVideoCoreVariablesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_single_template_SingleItemTemplateListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    public function onSetup()
    {
        $this->_mockContext    = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockTranslator = $this->mock(tubepress_lib_api_translation_TranslatorInterface::_);

        $this->_sut = new tubepress_app_impl_listeners_single_template_SingleItemTemplateListener(
            $this->_mockContext,
            $this->_mockTranslator
        );
    }

    public function testOnSingleVideo()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_WIDTH)->andReturn(889);

        $video = new tubepress_app_api_media_MediaItem('video-id');

        $expected = array(
            'foo'                                                       => 'bar',
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX => 889,
            tubepress_app_api_template_VariableNames::MEDIA_ITEM        => $video,
        );

        $event = $this->mock('tubepress_lib_api_event_EventInterface');
        $event->shouldReceive('hasArgument')->once()->with('item')->andReturn(true);
        $event->shouldReceive('getArgument')->once()->with('item')->andReturn($video);
        $event->shouldReceive('getSubject')->once()->andReturn(array('foo' => 'bar'));
        $event->shouldReceive('setSubject')->once()->with($expected);

        $this->_sut->onSingleTemplatePreRender($event);

        $this->assertTrue(true);
    }
}

