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
 * @covers tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService
 */
class tubepress_test_addons_vimeo_impl_embedded_VimeoEmbeddedPlayerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    public function onSetup() {

        $this->_mockContext = ehough_mockery_Mockery::mock(tubepress_api_options_ContextInterface::_);
        $this->_mockUrlFactory = $this->createMockSingletonService(tubepress_api_url_UrlFactoryInterface::_);
        $this->_sut = new tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService(
            $this->_mockContext, $this->_mockUrlFactory);
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals('vimeo', $this->_sut->getHandledProviderName());
    }

    public function testGetTemplate()
    {
        $mockThemeHandler = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/vimeo.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/add-ons/vimeo/resources/templates'
        )->andReturn('abc');

        $result = $this->_sut->getTemplate($mockThemeHandler);

        $this->assertEquals('abc', $result);
    }

    public function testGetDataUrl()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('ABCDEF');

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockQuery = ehough_mockery_Mockery::mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://player.vimeo.com/video/xx')->andReturn($mockUrl);

        $mockQuery->shouldReceive('set')->once()->with('autoplay', '1');
        $mockQuery->shouldReceive('set')->once()->with('color', 'ABCDEF');
        $mockQuery->shouldReceive('set')->once()->with('loop', '0');
        $mockQuery->shouldReceive('set')->once()->with('portrait', '1');
        $mockQuery->shouldReceive('set')->once()->with('byline', '1');
        $mockQuery->shouldReceive('set')->once()->with('title', '1');
        $mockQuery->shouldReceive('set')->once()->with('api', '1');
        $mockQuery->shouldReceive('set')->once()->with('player_id', ehough_mockery_Mockery::on(function ($param) {
            return tubepress_impl_util_StringUtils::startsWith($param, 'tubepress-video-object-');
        }));

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertSame($mockUrl, $result);
    }

}

