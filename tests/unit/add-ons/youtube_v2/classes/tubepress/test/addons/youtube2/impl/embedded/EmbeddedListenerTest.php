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
 * @covers tubepress_youtube2_impl_embedded_YouTubeEmbeddedProvider
 */
class tubepress_test_youtube2_impl_embedded_YouTubeEmbeddedProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube2_impl_embedded_YouTubeEmbeddedProvider
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
    private $_mockMediaItem;

    public function onSetup()
    {
        $this->_mockUrlFactory     = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockContext        = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockLangUtils      = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockUrlFactory     = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockMediaItem      = $this->mock('tubepress_app_api_media_MediaItem');

        $this->_sut = new tubepress_youtube2_impl_embedded_YouTubeEmbeddedProvider(
            $this->_mockContext,
            $this->_mockLangUtils,
            $this->_mockUrlFactory
        );
    }

    public function testBasics()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
        $this->assertEquals('YouTube', $this->_sut->getUntranslatedDisplayName());
        $this->assertEquals(array('youtube'), $this->_sut->getCompatibleMediaProviderNames());
        $this->assertEquals('single/embedded/youtube_iframe', $this->_sut->getTemplateName());
        $this->assertEquals(array(TUBEPRESS_ROOT . '/src/add-ons/youtube_v2/templates'), $this->_sut->getTemplateDirectories());
    }

    public function testGetDataUrl()
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

        $this->_mockMediaItem->shouldReceive('getId')->once()->andReturn('xx');

        $expected = array(
            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL => $mockUrl2
        );

        $actual = $this->_sut->getTemplateVariables($this->_mockMediaItem);

        $this->assertEquals($expected, $actual);
    }
}
