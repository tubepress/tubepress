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
 * @covers tubepress_youtube2_impl_listeners_embedded_EmbeddedListener
 */
class tubepress_test_youtube2_impl_listeners_embedded_EmbeddedListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube2_impl_listeners_embedded_EmbeddedListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPreRenderEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaItem;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProvider;

    public function onSetup()
    {
        $this->_mockUrlFactory     = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockContext        = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockLangUtils      = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockUrlFactory     = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockPreRenderEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockMediaItem      = $this->mock('tubepress_app_api_media_MediaItem');
        $this->_mockMediaProvider  = $this->mock(tubepress_app_api_media_MediaProviderInterface::__);

        $this->_sut = new tubepress_youtube2_impl_listeners_embedded_EmbeddedListener(
            $this->_mockContext,
            $this->_mockLangUtils,
            $this->_mockUrlFactory
        );

        $this->_mockPreRenderEvent->shouldReceive('getArgument')->twice()->with('mediaItem')->andReturn($this->_mockMediaItem);
        $this->_mockMediaItem->shouldReceive('getAttribute')->once()->with(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER)
            ->andReturn($this->_mockMediaProvider);
    }

    public function testOnEmbeddedTemplatePreRender()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_LOOP)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube2_api_Constants::OPTION_AUTOHIDE)->andReturn(tubepress_youtube2_api_Constants::AUTOHIDE_HIDE_BOTH);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube2_api_Constants::OPTION_FULLSCREEN)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube2_api_Constants::OPTION_MODEST_BRANDING)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube2_api_Constants::OPTION_SHOW_RELATED)->andReturn(false);

        $mockFullUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockUrl2 = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockUrl2->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockFullUrl->shouldReceive('getScheme')->once()->andReturn('sdy');
        $mockFullUrl->shouldReceive('getHost')->once()->andReturn('too.net');
        $mockQuery->shouldReceive('set')->once()->with('autohide', '1');
        $mockQuery->shouldReceive('set')->once()->with('autoplay', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('enablejsapi', '1');
        $mockQuery->shouldReceive('set')->once()->with('fs', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('modestbranding', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('origin', 'sdy://too.net');
        $mockQuery->shouldReceive('set')->once()->with('rel', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('showinfo', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('wmode', 'opaque');
        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.youtube.com/embed/xx')->andReturn($mockUrl2);

        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(3)->with(true)->andReturn('troo');
        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(2)->with(false)->andReturn('fawlse');

        $this->_mockPreRenderEvent->shouldReceive('getSubject')->once()->andReturn(array());

        $this->_mockMediaItem->shouldReceive('getId')->once()->andReturn('xx');

        $this->_mockPreRenderEvent->shouldReceive('setSubject')->once()->with(array(
            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL => $mockUrl2
        ));

        $this->_mockMediaProvider->shouldReceive('getName')->once()->andReturn('youtube');
        $this->_sut->onEmbeddedTemplatePreRender($this->_mockPreRenderEvent);

        $this->assertTrue(true);
    }
}
