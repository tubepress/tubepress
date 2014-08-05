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
 * @covers tubepress_vimeo2_impl_listeners_embedded_EmbeddedListener
 */
class tubepress_test_vimeo2_impl_listeners_embedded_EmbeddedListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo2_impl_listeners_embedded_EmbeddedListener
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

    public function onSetup() {

        $this->_mockUrlFactory     = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockContext        = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockLangUtils      = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockUrlFactory     = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockPreRenderEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockMediaItem      = $this->mock('tubepress_app_api_media_MediaItem');
        $this->_mockMediaProvider  = $this->mock(tubepress_app_api_media_MediaProviderInterface::__);

        $this->_sut = new tubepress_vimeo2_impl_listeners_embedded_EmbeddedListener(

            $this->_mockContext,
            $this->_mockLangUtils,
            $this->_mockUrlFactory
        );

        $this->_mockMediaItem->shouldReceive('getAttribute')->once()->with(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER)
            ->andReturn($this->_mockMediaProvider);
    }

    public function testGetDataUrl()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_LOOP)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR)->andReturn('ABCDEF');

        $mockUrl   = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://player.vimeo.com/video/xx')->andReturn($mockUrl);

        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(4)->with(true)->andReturn('troooo');
        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->once()->with(false)->andReturn('fawlse');

        $mockQuery->shouldReceive('set')->once()->with('autoplay', 'troooo');
        $mockQuery->shouldReceive('set')->once()->with('color', 'ABCDEF');
        $mockQuery->shouldReceive('set')->once()->with('loop', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('portrait', 'troooo');
        $mockQuery->shouldReceive('set')->once()->with('byline', 'troooo');
        $mockQuery->shouldReceive('set')->once()->with('title', 'troooo');
        $mockQuery->shouldReceive('set')->once()->with('api', 1);
        $mockQuery->shouldReceive('set')->once()->with('player_id', ehough_mockery_Mockery::on(function ($param) {
            $stringUtils = new tubepress_platform_impl_util_StringUtils();
            return $stringUtils->startsWith($param, 'tubepress-media-object-');
        }));

        $this->_mockPreRenderEvent->shouldReceive('getSubject')->once()->andReturn(array(
            'mediaItem' => $this->_mockMediaItem
        ));

        $this->_mockMediaItem->shouldReceive('getId')->once()->andReturn('xx');

        $this->_mockPreRenderEvent->shouldReceive('setSubject')->once()->with(array(
            'mediaItem'                                                 => $this->_mockMediaItem,
            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL => $mockUrl
        ));

        $this->_mockMediaProvider->shouldReceive('getName')->once()->andReturn('vimeo_v2');
        $this->_sut->onEmbeddedTemplatePreRender($this->_mockPreRenderEvent);

        $this->assertTrue(true);
    }
}

