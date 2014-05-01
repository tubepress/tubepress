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
 * @covers tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService
 */
class tubepress_test_addons_youtube_impl_embedded_YouTubeEmbeddedPlayerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockQss;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockQss        = $this->createMockSingletonService(tubepress_api_url_CurrentUrlServiceInterface::_);
        $this->_mockUrlFactory = $this->createMockSingletonService(tubepress_spi_url_UrlFactoryInterface::_);

        $this->_sut = new tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService($this->_mockQss);
    }

    public function testGetName()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals('youtube', $this->_sut->getHandledProviderName());
    }

    public function testGetTemplate()
    {
        $mockThemeHandler = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/youtube.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/add-ons/youtube/resources/templates'
        )->andReturn('abc');

        $result = $this->_sut->getTemplate($mockThemeHandler);

        $this->assertEquals('abc', $result);
    }

    public function testGetDataUrlAutoHideBoth()
    {
        $mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE)->andReturn(tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BOTH);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED)->andReturn(false);

        $mockFullUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockUrl2 = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl2->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockFullUrl->shouldReceive('getScheme')->once()->andReturn('sdy');
        $mockFullUrl->shouldReceive('getHost')->once()->andReturn('too.net');
        $mockQuery->shouldReceive('set')->once()->with('autohide', '1');
        $mockQuery->shouldReceive('set')->once()->with('autoplay', '1');
        $mockQuery->shouldReceive('set')->once()->with('enablejsapi', '1');
        $mockQuery->shouldReceive('set')->once()->with('fs', '0');
        $mockQuery->shouldReceive('set')->once()->with('loop', '0');
        $mockQuery->shouldReceive('set')->once()->with('modestbranding', '1');
        $mockQuery->shouldReceive('set')->once()->with('origin', 'sdy://too.net');
        $mockQuery->shouldReceive('set')->once()->with('rel', '0');
        $mockQuery->shouldReceive('set')->once()->with('showinfo', '1');
        $mockQuery->shouldReceive('set')->once()->with('wmode', 'opaque');
        $this->_mockQss->shouldReceive('getUrl')->once()->andReturn($mockFullUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.youtube.com/embed/xx')->andReturn($mockUrl2);

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertSame($mockUrl2, $result);
    }

    public function testGetDataUrlAutoShowBoth()
    {
        $mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE)->andReturn(tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_SHOW_BOTH);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED)->andReturn(false);

        $mockFullUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockUrl2 = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl2->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockFullUrl->shouldReceive('getScheme')->once()->andReturn('sdy');
        $mockFullUrl->shouldReceive('getHost')->once()->andReturn('too.net');
        $mockQuery->shouldReceive('set')->once()->with('autohide', '0');
        $mockQuery->shouldReceive('set')->once()->with('autoplay', '1');
        $mockQuery->shouldReceive('set')->once()->with('enablejsapi', '1');
        $mockQuery->shouldReceive('set')->once()->with('fs', '0');
        $mockQuery->shouldReceive('set')->once()->with('loop', '0');
        $mockQuery->shouldReceive('set')->once()->with('modestbranding', '1');
        $mockQuery->shouldReceive('set')->once()->with('origin', 'sdy://too.net');
        $mockQuery->shouldReceive('set')->once()->with('rel', '0');
        $mockQuery->shouldReceive('set')->once()->with('showinfo', '1');
        $mockQuery->shouldReceive('set')->once()->with('wmode', 'opaque');
        $this->_mockQss->shouldReceive('getUrl')->once()->andReturn($mockFullUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.youtube.com/embed/xx')->andReturn($mockUrl2);

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertSame($mockUrl2, $result);
    }

    public function testGetDataUrlAutoHideBarShowControls()
    {
        $mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE)->andReturn(tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED)->andReturn(false);

        $mockFullUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockUrl2 = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl2->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockFullUrl->shouldReceive('getScheme')->once()->andReturn('sdy');
        $mockFullUrl->shouldReceive('getHost')->once()->andReturn('too.net');
        $mockQuery->shouldReceive('set')->once()->with('autohide', '2');
        $mockQuery->shouldReceive('set')->once()->with('autoplay', '1');
        $mockQuery->shouldReceive('set')->once()->with('enablejsapi', '1');
        $mockQuery->shouldReceive('set')->once()->with('fs', '0');
        $mockQuery->shouldReceive('set')->once()->with('loop', '0');
        $mockQuery->shouldReceive('set')->once()->with('modestbranding', '1');
        $mockQuery->shouldReceive('set')->once()->with('origin', 'sdy://too.net');
        $mockQuery->shouldReceive('set')->once()->with('rel', '0');
        $mockQuery->shouldReceive('set')->once()->with('showinfo', '1');
        $mockQuery->shouldReceive('set')->once()->with('wmode', 'opaque');
        $this->_mockQss->shouldReceive('getUrl')->once()->andReturn($mockFullUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.youtube.com/embed/xx')->andReturn($mockUrl2);

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertSame($mockUrl2, $result);
    }
}
