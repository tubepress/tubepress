<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_plugins_core_filters_gallerytemplate_PlayerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockExecutionContext;

    private $_mockPlayerHtmlGenerator;

    function setup()
    {
        $this->_sut = new tubepress_plugins_core_filters_gallerytemplate_Player();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_mockPlayerHtmlGenerator = Mockery::mock(tubepress_spi_player_PlayerHtmlGenerator::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setPlayerHtmlGenerator($this->_mockPlayerHtmlGenerator);
    }

    function testNonPlayerLoadOnPage()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('player-name');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_api_video_Video();

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($fakeVideo));

        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_HTML, '');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_NAME, 'player-name');

        $event = new tubepress_api_event_ThumbnailGalleryTemplateConstruction($mockTemplate);
        $event->setArguments(array(

            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PAGE => 1,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PROVIDER_NAME => tubepress_spi_provider_Provider::YOUTUBE,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_VIDEO_GALLERY_PAGE => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }

    function testAlterTemplateStaticPlayer()
    {
        $this->_testPlayerLoadOnPage(tubepress_api_const_options_values_PlayerLocationValue::STATICC);
    }

    function testAlterTemplateNormalPlayer()
    {
        $this->_testPlayerLoadOnPage(tubepress_api_const_options_values_PlayerLocationValue::NORMAL);
    }

    private function _testPlayerLoadOnPage($name)
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn($name);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_api_video_Video();

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array($fakeVideo));

        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_HTML, 'player-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PLAYER_NAME, $name);

        $this->_mockPlayerHtmlGenerator->shouldReceive('getHtml')->once()->with($fakeVideo, 'gallery-id')->andReturn('player-html');

        $event = new tubepress_api_event_ThumbnailGalleryTemplateConstruction($mockTemplate);
        $event->setArguments(array(

            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PAGE => 1,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PROVIDER_NAME => tubepress_spi_provider_Provider::YOUTUBE,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_VIDEO_GALLERY_PAGE => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}
