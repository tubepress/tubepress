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
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    public function onSetup() {

        $this->_mockContext         = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockUrlFactory      = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockLangUtils       = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);

        $this->_sut = new tubepress_vimeo2_impl_listeners_embedded_EmbeddedListener(

            $this->_mockContext,
            $this->_mockLangUtils
        );
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testGetFriendlyName()
    {
        $this->assertEquals('Vimeo', $this->_sut->getUntranslatedDisplayName());
    }

    public function testGetTemplate()
    {
        $expected = array(

            'embedded/vimeo.tpl.php',
            TUBEPRESS_ROOT . '/src/add-ons/vimeo/templates/embedded/vimeo.tpl.php'
        );

        $result = $this->_sut->getPathsForTemplateFactory();

        $this->assertEquals($expected, $result);
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

        $result = $this->_sut->getDataUrlForMediaItem($this->_mockUrlFactory, 'xx');

        $this->assertSame($mockUrl, $result);
    }

    public function testCompatibleProviders()
    {
        $result = $this->_sut->getCompatibleProviderNames();

        $this->assertEquals(array('vimeo'), $result);
    }
}

