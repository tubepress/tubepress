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
 * @covers tubepress_core_media_gallery_impl_listeners_template_PlayerLocation
 */
class tubepress_test_core_impl_listeners_template_ThumbGalleryPlayerLocationTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_gallery_impl_listeners_template_PlayerLocation
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

        $this->_mockExecutionContext    = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockPlayerHtmlGenerator = $this->mock(tubepress_core_player_api_PlayerHtmlInterface::_);
        $this->_sut = new tubepress_core_media_gallery_impl_listeners_template_PlayerLocation($this->_mockExecutionContext,
            $this->_mockPlayerHtmlGenerator);
    }

    public function testNonPlayerLoadOnPage()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('player-name');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_core_provider_api_MediaItem();

        $providerResult = new tubepress_core_provider_api_Page();
        $providerResult->setItems(array($fakeVideo));

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::PLAYER_HTML, '');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::PLAYER_NAME, 'player-name');

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);

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
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn($name);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_core_provider_api_MediaItem();

        $providerResult = new tubepress_core_provider_api_Page();
        $providerResult->setItems(array($fakeVideo));

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::PLAYER_HTML, 'player-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::PLAYER_NAME, $name);

        $this->_mockPlayerHtmlGenerator->shouldReceive('getHtml')->once()->with($fakeVideo, 'gallery-id')->andReturn('player-html');

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);


        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}
