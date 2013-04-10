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
class tubepress_addons_core_impl_filters_gallerytemplate_PlayerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_filters_gallerytemplate_Player
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
        $this->_sut = new tubepress_addons_core_impl_filters_gallerytemplate_Player();

        $this->_mockExecutionContext    = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockPlayerHtmlGenerator = $this->createMockSingletonService(tubepress_spi_player_PlayerHtmlGenerator::_);
    }

    public function testNonPlayerLoadOnPage()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('player-name');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_api_video_Video();

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($fakeVideo));

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_HTML, '');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_NAME, 'player-name');

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);
        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'youtube',
            'videoGalleryPage' => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
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
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn($name);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_api_video_Video();

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($fakeVideo));

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_HTML, 'player-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_NAME, $name);

        $this->_mockPlayerHtmlGenerator->shouldReceive('getHtml')->once()->with($fakeVideo, 'gallery-id')->andReturn('player-html');

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);
        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'youtube',
            'videoGalleryPage' => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}
