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
 * @covers tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService
 */
class tubepress_test_addons_core_impl_shortcode_commands_SingleVideoPluggableShortcodeHandlerServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider;

    public function onSetup()
    {
        $this->_mockExecutionContext = ehough_mockery_Mockery::mock(tubepress_api_options_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockThemeHandler     = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);
        $this->_mockProvider = $this->createMockSingletonService(tubepress_spi_collector_VideoCollector::_);


        $this->_sut = new tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService($this->_mockExecutionContext, $this->_mockEventDispatcher);
    }

    public function testExecuteNoVideo()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::VIDEO)->andReturn('');

        $this->assertFalse($this->_sut->shouldExecute());
    }

    public function testExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::VIDEO)->andReturn('video-id');

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('single_video.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default')->andReturn($mockTemplate);

        $video = new tubepress_api_video_Video();

        $this->_mockProvider->shouldReceive('collectSingleVideo')->once()->with('video-id')->andReturn($video);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === $mockTemplate;
        }));

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_EventNames::HTML_SINGLE_VIDEO)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_SINGLE_VIDEO, ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === 'template-string';
        }));

        $this->assertEquals('template-string', $this->_sut->getHtml());
    }

    public function testGetName()
    {
        $this->assertEquals('single-video', $this->_sut->getName());
    }
}