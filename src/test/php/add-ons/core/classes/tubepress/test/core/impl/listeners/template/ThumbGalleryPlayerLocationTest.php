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
 * @covers tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation
 */
class tubepress_test_core_impl_listeners_template_ThumbGalleryPlayerLocationTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPlayerHtmlGenerator;

    public function onSetup()
    {

        $this->_mockExecutionContext    = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockPlayerHtmlGenerator = $this->mock(tubepress_core_api_player_PlayerHtmlInterface::_);
        $this->_sut = new tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation($this->_mockExecutionContext,
            $this->_mockPlayerHtmlGenerator);
    }

    public function testNonPlayerLoadOnPage()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_LOCATION)->andReturn('player-name');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_core_api_video_Video();

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($fakeVideo));

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::PLAYER_HTML, '');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::PLAYER_NAME, 'player-name');

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('videoGalleryPage')->andReturn($providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }

    public function testAlterTemplateStaticPlayer()
    {
        $this->_testPlayerLoadOnPage('static');
    }

    public function testAlterTemplateNormalPlayer()
    {
        $this->_testPlayerLoadOnPage('normal');
    }

    private function _testPlayerLoadOnPage($name)
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_LOCATION)->andReturn($name);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_core_api_video_Video();

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($fakeVideo));

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::PLAYER_HTML, 'player-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::PLAYER_NAME, $name);

        $this->_mockPlayerHtmlGenerator->shouldReceive('getHtml')->once()->with($fakeVideo, 'gallery-id')->andReturn('player-html');

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('videoGalleryPage')->andReturn($providerResult);


        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}
