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
class tubepress_test_impl_embedded_DefaultEmbeddedPlayerHtmlGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_spi_embedded_EmbeddedHtmlGenerator
     */
    private $_sut;

    private $_mockExecutionContext;

    private $_mockEventDispatcher;

    private $_mockThemeHandler;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockThemeHandler     = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandler::_);
    }

    public function testMatchingCustomPlayer()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn('z');

        $mockEmbeddedPlayer = $this->createMockPluggableService(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
        $mockEmbeddedPlayer->shouldReceive('getName')->twice()->andReturn('z');
        $mockEmbeddedPlayer->shouldReceive('getDataUrlForVideo')->once()->with('video-id')->andReturn('data-url');
        $mockEmbeddedPlayer->shouldReceive('getHandledProviderName')->twice()->andReturn('some-provider');

        $mockVideoProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(true);
        $mockVideoProvider->shouldReceive('getName')->once()->andReturn('some-provider');

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockEmbeddedPlayer->shouldReceive('getTemplate')->once()->with($this->_mockThemeHandler)->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate) {

                return $arg instanceof ehough_tickertape_Event && $arg->getSubject() === $mockTemplate
                    && $arg->getArgument('videoId') === 'video-id'
                    && $arg->getArgument('providerName') === 'some-provider'
                    && $arg->getArgument('dataUrl') === 'data-url'
                    && $arg->getArgument('embeddedImplementationName') === 'z';
            }));

        $mockTemplate->shouldReceive('toString')->once()->andReturn('templateAsString');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_EMBEDDED,
            ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate) {

                return $arg instanceof ehough_tickertape_Event && $arg->getSubject() === 'templateAsString'
                    && $arg->getArgument('videoId') === 'video-id'
                    && $arg->getArgument('providerName') === 'some-provider'
                    && $arg->getArgument('dataUrl') === 'data-url'
                    && $arg->getArgument('embeddedImplementationName') === 'z';
            }));

        $html = $this->_sut->getHtml('video-id');

        $this->assertEquals('templateAsString', $html);
    }

    public function testMatchingProviderBased()
    {
        $mockEmbeddedPlayer = $this->createMockPluggableService(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
        $mockEmbeddedPlayer->shouldReceive('getHandledProviderName')->twice()->andReturn('xyz');
        $mockEmbeddedPlayer->shouldReceive('getDataUrlForVideo')->once()->with('video-id')->andReturn('data-url');
        $mockEmbeddedPlayer->shouldReceive('getName')->twice()->andReturn('z');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);

        $mockVideoProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(true);
        $mockVideoProvider->shouldReceive('getName')->twice()->andReturn('xyz');

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockEmbeddedPlayer->shouldReceive('getTemplate')->once()->with($this->_mockThemeHandler)->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate) {

                return $arg instanceof ehough_tickertape_Event && $arg->getSubject() === $mockTemplate
                    && $arg->getArgument('videoId') === 'video-id'
                    && $arg->getArgument('providerName') === 'xyz'
                    && $arg->getArgument('dataUrl') === 'data-url'
                    && $arg->getArgument('embeddedImplementationName') === 'z';
            }));

        $mockTemplate->shouldReceive('toString')->once()->andReturn('templateAsString');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_EMBEDDED,
            ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate) {

                return $arg instanceof ehough_tickertape_Event && $arg->getSubject() === 'templateAsString'
                    && $arg->getArgument('videoId') === 'video-id'
                    && $arg->getArgument('providerName') === 'xyz'
                    && $arg->getArgument('dataUrl') === 'data-url'
                    && $arg->getArgument('embeddedImplementationName') === 'z';
            }));

        $html = $this->_sut->getHtml('video-id');

        $this->assertEquals('templateAsString', $html);
    }

    public function testProvidersRecognizeButNoPlayersDo()
    {
        $mockEmbeddedPlayer = $this->createMockPluggableService(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
        $mockEmbeddedPlayer->shouldReceive('getHandledProviderName')->once()->andReturn('xyz');
        $mockEmbeddedPlayer->shouldReceive('getName')->once()->andReturn('tex');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);

        $mockVideoProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(true);
        $mockVideoProvider->shouldReceive('getName')->twice()->andReturn('something else');

        $html = $this->_sut->getHtml('video-id');

        $this->assertNull($html);
    }

    public function testNoProvidersRecognize()
    {
        $this->createMockPluggableService(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);

        $mockVideoProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(false);

        $html = $this->_sut->getHtml('video-id');

        $this->assertNull($html);
    }

    public function testNoMatchingProviderPlayers()
    {
        $this->createMockPluggableService(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);

        $html = $this->_sut->getHtml('video-id');

        $this->assertNull($html);
    }
}
