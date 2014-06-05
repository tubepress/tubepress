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
 * @covers tubepress_wordpress_impl_actions_Init
 */
class tubepress_test_wordpress_impl_actions_InitTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_actions_Init
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    public function onSetup()
    {

        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockThemeHandler             = $this->mock(tubepress_core_theme_api_ThemeLibraryInterface::_);
        $this->_mockStringUtils              = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_sut = new tubepress_wordpress_impl_actions_Init(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockThemeHandler,
            $this->_mockStringUtils
        );
    }

    public function testInitAction()
    {
        $mockStyleUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockScriptUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockStyleUrl->shouldReceive('toString')->once()->andReturn('style-url');
        $mockScriptUrl->shouldReceive('toString')->twice()->andReturn('script-url');
        $themeStyles = array($mockStyleUrl);
        $themeScripts = array($mockScriptUrl);
        $this->_mockThemeHandler->shouldReceive('getStylesUrls')->once()->andReturn($themeStyles);
        $this->_mockThemeHandler->shouldReceive('getScriptsUrls')->once()->andReturn($themeScripts);

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-theme-0', 'style-url');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-theme-0');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-theme-0', 'script-url');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-theme-0', false, array(), false, false);

        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/src/core/html/web/js/tubepress.js', 'tubepress')->andReturn('<tubepressjs>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<tubepressjs>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery', false, array(), false, false);

        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('script-url', '/src/core/html/web/js/tubepress.js')->andReturn(false);

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_sut->action($mockEvent);
        $this->assertTrue(true);
    }
}
