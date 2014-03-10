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
 * @covers tubepress_addons_wordpress_impl_actions_Init
 */
class tubepress_test_addons_wordpress_impl_actions_InitTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_actions_Init
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

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_actions_Init();

        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockThemeHandler             = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);
    }

    public function testInitAction()
    {
        $themeStyles = array('a', 'b', 'c');
        $themeScripts = array('x', 'y', 'z');
        $this->_mockThemeHandler->shouldReceive('getStyles')->once()->andReturn($themeStyles);
        $this->_mockThemeHandler->shouldReceive('getScripts')->once()->andReturn($themeScripts);

        for ($x = 0; $x < count($themeStyles); $x++) {

            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-theme-' . $x, $themeStyles[$x]);
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-theme-' . $x);
        }

        for ($x = 0; $x < count($themeScripts); $x++) {

            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-theme-' . $x, $themeScripts[$x]);
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-theme-' . $x, false, array(), false, false);
        }

        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/src/main/web/js/tubepress.js', 'tubepress')->andReturn('<tubepressjs>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<tubepressjs>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery', false, array(), false, false);

        $mockEvent = ehough_mockery_Mockery::mock('tubepress_api_event_EventInterface');
        $this->_sut->action($mockEvent);
        $this->assertTrue(true);
    }
}
