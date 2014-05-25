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
 * @covers tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName
 */
class tubepress_test_core_impl_listeners_template_ThumbGalleryEmbeddedImplNameTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {

        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_sut = new tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName($this->_mockExecutionContext);
    }

    public function testAlterTemplateLongtailYouTube()
    {
        $this->_testCustomYouTube('longtail');
    }

    public function testAlterTemplateEmbedPlusYouTube()
    {
        $this->_testCustomYouTube('embedplus');
    }

    public function testAlterTemplateProviderDefault()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_IMPL)->andReturn('player-impl');

        $video = new tubepress_core_api_video_Video();
        $video->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_PROVIDER_NAME, 'provider-name');

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($video));

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::EMBEDDED_IMPL_NAME, 'provider-name');

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('videoGalleryPage')->andReturn($providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }

    private function _testCustomYouTube($name)
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_IMPL)->andReturn($name);

        $video = new tubepress_core_api_video_Video();
        $video->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_PROVIDER_NAME, $name);

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($video));

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::EMBEDDED_IMPL_NAME, $name);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('videoGalleryPage')->andReturn($providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}

