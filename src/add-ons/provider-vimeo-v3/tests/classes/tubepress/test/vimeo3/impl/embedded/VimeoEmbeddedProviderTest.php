<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo3_impl_embedded_VimeoEmbeddedProvider
 */
class tubepress_test_vimeo3_impl_embedded_VimeoEmbeddedProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo3_impl_embedded_VimeoEmbeddedProvider
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockMediaItem;

    public function onSetup() {

        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockContext    = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockLangUtils  = $this->mock(tubepress_api_util_LangUtilsInterface::_);
        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockMediaItem  = $this->mock('tubepress_api_media_MediaItem');

        $this->_sut = new tubepress_vimeo3_impl_embedded_VimeoEmbeddedProvider(

            $this->_mockContext,
            $this->_mockLangUtils,
            $this->_mockUrlFactory
        );
    }

    public function testBasics()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
        $this->assertEquals('Vimeo', $this->_sut->getUntranslatedDisplayName());
        $this->assertEquals(array('vimeo'), $this->_sut->getCompatibleMediaProviderNames());
        $this->assertEquals('single/embedded/vimeo_iframe', $this->_sut->getTemplateName());
        $this->assertEquals(array(TUBEPRESS_ROOT . '/src/add-ons/provider-vimeo-v3/templates'), $this->_sut->getTemplateDirectories());
    }

    public function testGetDataUrl()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::EMBEDDED_AUTOPLAY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::EMBEDDED_LOOP)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::EMBEDDED_SHOW_INFO)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_vimeo3_api_Constants::OPTION_PLAYER_COLOR)->andReturn('ABCDEF');

        $mockUrl   = $this->mock('tubepress_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://player.vimeo.com/video/xx')->andReturn($mockUrl);

        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->times(4)->with(true)->andReturn('troooo');
        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->once()->with(false)->andReturn('fawlse');

        $mockQuery->shouldReceive('set')->once()->with('autoplay', 'troooo');
        $mockQuery->shouldReceive('set')->once()->with('color', 'ABCDEF');
        $mockQuery->shouldReceive('set')->once()->with('loop', 'fawlse');
        $mockQuery->shouldReceive('set')->once()->with('portrait', 'troooo');
        $mockQuery->shouldReceive('set')->once()->with('byline', 'troooo');
        $mockQuery->shouldReceive('set')->once()->with('title', 'troooo');

        $this->_mockMediaItem->shouldReceive('getId')->once()->andReturn('xx');

        $expected = array(
            tubepress_api_template_VariableNames::EMBEDDED_DATA_URL => $mockUrl,
        );

        $actual = $this->_sut->getTemplateVariables($this->_mockMediaItem);

        $this->assertEquals($expected, $actual);
    }
}
