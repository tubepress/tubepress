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
 * @covers tubepress_core_impl_embedded_EmbeddedHtml<extended>
 */
class tubepress_test_impl_embedded_EmbeddedHtml extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_embedded_EmbeddedHtml
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockUrlFactory       = $this->mock(tubepress_core_api_url_UrlFactoryInterface::_);
        $this->_mockTemplateFactory  = $this->mock(tubepress_core_api_template_TemplateFactoryInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_core_impl_embedded_EmbeddedHtml(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockUrlFactory,
            $this->_mockTemplateFactory
        );
    }

    public function testMatchingCustomPlayer()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_IMPL)->andReturn('z');

        $mockVideoProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(true);
        $mockVideoProvider->shouldReceive('getName')->times(2)->andReturn('some-provider');

        $mockEmbeddedPlayer = $this->mock(tubepress_core_api_embedded_EmbeddedProviderInterface::_);
        $mockEmbeddedPlayer->shouldReceive('getName')->times(2)->andReturn('z');
        $mockEmbeddedPlayer->shouldReceive('getDataUrlForVideo')->once()->with($this->_mockUrlFactory, $mockVideoProvider, 'video-id')->andReturn('data-url');
        $mockEmbeddedPlayer->shouldReceive('getCompatibleProviderNames')->once()->andReturn(array('some-provider'));

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockEmbeddedPlayer->shouldReceive('getPathsForTemplateFactory')->once()->andReturn(array('x'));

        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array('x'))->andReturn($mockTemplate);

        $mockTemplateEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockTemplate, array(
                'videoId' => 'video-id',
                'providerName' => 'some-provider',
                'dataUrl' => 'data-url',
                'embeddedImplementationName' => 'z',
        ))->andReturn($mockTemplateEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_core_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            $mockTemplateEvent);

        $mockTemplate->shouldReceive('toString')->once()->andReturn('templateAsString');

        $mockHtmlEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('foobar');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('templateAsString', array(
            'videoId' => 'video-id',
            'providerName' => 'some-provider',
            'dataUrl' => 'data-url',
            'embeddedImplementationName' => 'z',
        ))->andReturn($mockHtmlEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::HTML_EMBEDDED,
            $mockHtmlEvent);

        $this->_sut->setVideoProviders(array($mockVideoProvider));
        $this->_sut->setEmbeddedProviders(array($mockEmbeddedPlayer));

        $html = $this->_sut->getHtml('video-id');

        $this->assertEquals('foobar', $html);
    }

    public function testMatchingProviderBased()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_IMPL)->andReturn(tubepress_core_api_const_options_ValidValues::EMBEDDED_IMPL_PROVIDER_BASED);

        $mockVideoProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(true);
        $mockVideoProvider->shouldReceive('getName')->times(3)->andReturn('xyz');

        $mockEmbeddedPlayer = $this->mock(tubepress_core_api_embedded_EmbeddedProviderInterface::_);
        $mockEmbeddedPlayer->shouldReceive('getDataUrlForVideo')->once()->with($this->_mockUrlFactory, $mockVideoProvider, 'video-id')->andReturn('data-url');
        $mockEmbeddedPlayer->shouldReceive('getName')->twice()->andReturn('z');
        $mockEmbeddedPlayer->shouldReceive('getCompatibleProviderNames')->once()->andReturn(array('xyz'));

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockEmbeddedPlayer->shouldReceive('getPathsForTemplateFactory')->once()->andReturn(array('x'));

        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array('x'))->andReturn($mockTemplate);

        $mockTemplateEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockTemplate, array(
            'videoId' => 'video-id',
            'providerName' => 'xyz',
            'dataUrl' => 'data-url',
            'embeddedImplementationName' => 'z',
        ))->andReturn($mockTemplateEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            $mockTemplateEvent);

        $mockTemplate->shouldReceive('toString')->once()->andReturn('templateAsString');

        $mockHtmlEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('foobar');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('templateAsString', array(
            'videoId' => 'video-id',
            'providerName' => 'xyz',
            'dataUrl' => 'data-url',
            'embeddedImplementationName' => 'z',
        ))->andReturn($mockHtmlEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::HTML_EMBEDDED,
            $mockHtmlEvent);

        $this->_sut->setVideoProviders(array($mockVideoProvider));
        $this->_sut->setEmbeddedProviders(array($mockEmbeddedPlayer));

        $html = $this->_sut->getHtml('video-id');

        $this->assertEquals('foobar', $html);
    }

    public function testProvidersRecognizeButNoPlayersDo()
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('Could not generate the embedded player HTML for video-id');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_IMPL)->andReturn(tubepress_core_api_const_options_ValidValues::EMBEDDED_IMPL_PROVIDER_BASED);

        $mockVideoProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(true);
        $mockVideoProvider->shouldReceive('getName')->twice()->andReturn('something else');

        $mockEmbeddedPlayer = $this->mock(tubepress_core_api_embedded_EmbeddedProviderInterface::_);
        $mockEmbeddedPlayer->shouldReceive('getName')->once()->andReturn('tex');
        $mockEmbeddedPlayer->shouldReceive('getCompatibleProviderNames')->once()->andReturn(array('foobar'));

        $this->_sut->setVideoProviders(array($mockVideoProvider));
        $this->_sut->setEmbeddedProviders(array($mockEmbeddedPlayer));

        $html = $this->_sut->getHtml('video-id');

        $this->assertNull($html);
    }

    public function testNoProvidersRecognize()
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('No video providers recognize video video-id');

        $this->mock(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $mockVideoProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);
        $mockVideoProvider->shouldReceive('recognizesVideoId')->once()->with('video-id')->andReturn(false);

        $this->_sut->setVideoProviders(array($mockVideoProvider));

        $html = $this->_sut->getHtml('video-id');

        $this->assertNull($html);
    }

    public function testNoMatchingProviderPlayers()
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('No video providers recognize video video-id');

        $this->mock(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $html = $this->_sut->getHtml('video-id');

        $this->assertNull($html);
    }
}
