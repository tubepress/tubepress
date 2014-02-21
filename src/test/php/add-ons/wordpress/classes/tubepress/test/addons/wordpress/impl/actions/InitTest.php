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

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_actions_Init();

        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
    }

    public function testInitAction()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/src/main/web/js/tubepress.js', 'tubepress')->andReturn('<tubepressjs>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/src/main/web/css/tubepress.css', 'tubepress')->andReturn('<tubepresscss>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<tubepressjs>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress', '<tubepresscss>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery', false, array(), false, false);

        $this->_sut->execute(array());

        $this->assertTrue(true);
    }
}
