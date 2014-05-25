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
 * @covers tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider
 */
class tubepress_test_youtube_impl_embedded_YouTubeEmbeddedProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider
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

    public function onSetup()
    {
        $this->_mockUrlFactory = $this->mock(tubepress_core_api_url_UrlFactoryInterface::_);
        $this->_mockContext    = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockLangUtils  = $this->mock(tubepress_api_util_LangUtilsInterface::_);

        $this->_sut = new tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider(
            $this->_mockContext,
            $this->_mockLangUtils);
    }

    public function testGetName()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals(array('youtube'), $this->_sut->getCompatibleProviderNames());
    }

    public function testGetTemplate()
    {
        $expected = array(
           'embedded/youtube.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/add-ons/youtube/resources/templates'
        );

        $result = $this->_sut->getPathsForTemplateFactory();

        $this->assertEquals($expected, $result);
    }

    public function testGetDataUrlAutoHideBoth()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::AUTOPLAY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::LOOP)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SHOW_INFO)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::AUTOHIDE)->andReturn(tubepress_youtube_api_const_options_Values::AUTOHIDE_HIDE_BOTH);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::ENABLE_JS_API)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::FULLSCREEN)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::MODEST_BRANDING)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::SHOW_RELATED)->andReturn(false);

        $mockFullUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockUrl2 = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl2->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockFullUrl->shouldReceive('getScheme')->once()->andReturn('sdy');
        $mockFullUrl->shouldReceive('getHost')->once()->andReturn('too.net');
        $mockQuery->shouldReceive('set')->once()->with('autohide', '1');
        $mockQuery->shouldReceive('set')->once()->with('autoplay', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('enablejsapi', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('fs', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('loop', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('modestbranding', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('origin', 'sdy://too.net');
        $mockQuery->shouldReceive('set')->once()->with('rel', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('showinfo', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('wmode', 'opaque');
        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.youtube.com/embed/xx')->andReturn($mockUrl2);

        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(4)->with(true)->andReturn('troo');
        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(3)->with(false)->andReturn('fawlse');

        $mockProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);

        $result = $this->_sut->getDataUrlForVideo($this->_mockUrlFactory, $mockProvider, 'xx');

        $this->assertSame($mockUrl2, $result);
    }

    public function testGetDataUrlAutoShowBoth()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::AUTOPLAY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::LOOP)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SHOW_INFO)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::AUTOHIDE)->andReturn(tubepress_youtube_api_const_options_Values::AUTOHIDE_SHOW_BOTH);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::ENABLE_JS_API)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::FULLSCREEN)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::MODEST_BRANDING)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::SHOW_RELATED)->andReturn(false);

        $mockFullUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockUrl2 = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl2->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockFullUrl->shouldReceive('getScheme')->once()->andReturn('sdy');
        $mockFullUrl->shouldReceive('getHost')->once()->andReturn('too.net');
        $mockQuery->shouldReceive('set')->once()->with('autohide', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('autoplay', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('enablejsapi', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('fs', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('loop', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('modestbranding', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('origin', 'sdy://too.net');
        $mockQuery->shouldReceive('set')->once()->with('rel', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('showinfo', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('wmode', 'opaque');
        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.youtube.com/embed/xx')->andReturn($mockUrl2);

        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(4)->with(true)->andReturn('troo');
        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(3)->with(false)->andReturn('fawlse');

        $mockProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);

        $result = $this->_sut->getDataUrlForVideo($this->_mockUrlFactory, $mockProvider, 'xx');
        $this->assertSame($mockUrl2, $result);
    }

    public function testGetDataUrlAutoHideBarShowControls()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::AUTOPLAY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::LOOP)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SHOW_INFO)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::AUTOHIDE)->andReturn(tubepress_youtube_api_const_options_Values::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::ENABLE_JS_API)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::FULLSCREEN)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::MODEST_BRANDING)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_youtube_api_const_options_Names::SHOW_RELATED)->andReturn(false);

        $mockFullUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockUrl2 = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_core_api_url_QueryInterface');
        $mockUrl2->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockFullUrl->shouldReceive('getScheme')->once()->andReturn('sdy');
        $mockFullUrl->shouldReceive('getHost')->once()->andReturn('too.net');
        $mockQuery->shouldReceive('set')->once()->with('autohide', '2');
        $mockQuery->shouldReceive('set')->once()->with('autoplay', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('enablejsapi', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('fs', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('loop', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('modestbranding', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('origin', 'sdy://too.net');
        $mockQuery->shouldReceive('set')->once()->with('rel', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('showinfo', 'troo');
        $mockQuery->shouldReceive('set')->once()->with('wmode', 'opaque');
        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.youtube.com/embed/xx')->andReturn($mockUrl2);

        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(4)->with(true)->andReturn('troo');
        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(3)->with(false)->andReturn('fawlse');

        $mockProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);

        $result = $this->_sut->getDataUrlForVideo($this->_mockUrlFactory, $mockProvider, 'xx');
        $this->assertSame($mockUrl2, $result);
    }
}
