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
 * @covers tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation
 */
class tubepress_test_addons_core_impl_listeners_template_ThumbGalleryPlayerLocationTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation
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

        $this->_mockExecutionContext    = ehough_mockery_Mockery::mock(tubepress_api_options_ContextInterface::_);
        $this->_mockPlayerHtmlGenerator = $this->createMockSingletonService(tubepress_spi_player_PlayerHtmlGenerator::_);
        $this->_sut = new tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation($this->_mockExecutionContext);
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

        $event = new tubepress_spi_event_EventBase($mockTemplate);
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

        $event = new tubepress_spi_event_EventBase($mockTemplate);
        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'youtube',
            'videoGalleryPage' => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}
